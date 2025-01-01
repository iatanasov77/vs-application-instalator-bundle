<?php namespace Vankosoft\ApplicationBundle\DataCollector;

use Symfony\Component\HttpKernel\DataCollector\DataCollector;
use Doctrine\DBAL\Exception\TableNotFoundException;
use Doctrine\DBAL\Exception\ConnectionException;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RequestStack;

use Vankosoft\ApplicationBundle\Component\Application\Kernel;
use Vankosoft\ApplicationBundle\Component\Application\Project;
use Vankosoft\ApplicationInstalatorBundle\Repository\InstalationInfoRepositoryInterface;

/**
 * Tutorial: https://symfony.com/doc/current/profiler/data_collector.html
 * Example: Sylius\Bundle\CoreBundle\Collector\SyliusCollector
 */
final class VsApplicationCollector extends DataCollector
{
    /** @var Project */
    private $projectType;
    
    /** @var RepositoryInterface */
    private $localesRepository;
    
    /** @var string */
    private $version;
    
    /** @var string */
    private $projectVersion;
    
    /** @var array */
    private $bundles;
    
    /** @var string */
    private $defaultLocaleCode;
    
    public function __construct(
        Project $projectType,
        RepositoryInterface $localesRepository,
        InstalationInfoRepositoryInterface $installationInfoRepository,
        string $version,
        array $bundles,
        string $defaultLocaleCode
    ) {
        $this->projectType          = $projectType;
        $this->localesRepository    = $localesRepository;
        $this->version              = $version;
        $this->bundles              = $bundles;
        $this->defaultLocaleCode    = $defaultLocaleCode;
        
        try {
            $instalationInfo            = $installationInfoRepository->getLatestInstallation();
            if ( $instalationInfo ) {
                $this->projectVersion   = $instalationInfo->getVersion();
            }
        } catch ( TableNotFoundException $e ) {
            // DO Nothing
        } catch ( ConnectionException $e ) {
            // DO Nothing
        }
    }
    
    public function getProjectType(): string
    {
        return $this->data['project_type'];
    }
    
    public function getVersion()
    {
        return $this->data['version'];
    }
    
    public function getProjectVersion()
    {
        return $this->data['projectVersion'];
    }
    
    public function getExtensions(): array
    {
        return $this->data['extensions'];
    }
    
    public function getLocales(): array
    {
        return $this->data['locales'];
    }
    
    /**
     * @return string
     */
    public function getLocaleCode(): ?string
    {
        return $this->data['locale_code'];
    }
    
    /**
     * @return string
     */
    public function getDefaultLocaleCode(): ?string
    {
        return $this->data['default_locale_code'];
    }
    
    public function collect( Request $request, Response $response, \Throwable $exception = null )
    {
        $currentLocale  = $request->getLocale();
        $locales        = [];
        foreach ( $this->localesRepository->findAll() as $locale ) {
            $locales[]  = [
                'code'      => $locale->getCode(),
                'current'   => ( $currentLocale == $locale->getCode() ),
                'default'   => ( $this->defaultLocaleCode == $locale->getCode() ),
            ];
        }
        
        $this->data = [
            'project_type'          => $this->projectType->projectType(),
            'version'               => $this->version,
            'projectVersion'        => $this->projectVersion,
            'default_locale_code'   => $this->defaultLocaleCode,
            'locale_code'           => $currentLocale,
            'locales'               => $locales,
            'extensions'            => [
                'VSUsersSubscriptionsBundle'    => ['name' => 'Subscription', 'enabled' => false],
                'VSPaymentBundle'               => ['name' => 'Payment', 'enabled' => false],
                'VSCatalogBundle'               => ['name' => 'Catalog', 'enabled' => false],
                'VSApiBundle'                   => ['name' => 'API', 'enabled' => false],
                'VSThruwayBundle'               => ['name' => 'WAMP', 'enabled' => false],
            ],
        ];
        
        foreach ( array_keys( $this->data['extensions'] ) as $bundleName ) {
            if ( isset( $this->bundles[$bundleName] ) ) {
                $this->data['extensions'][$bundleName]['enabled']   = true;
            }
        }
    }
    
    public function reset(): void
    {
        $this->data['locale_code'] = null;
    }
    
    public function getName(): string
    {
        return 'vs_application.core_collector';
    }
}
