<?php namespace Vankosoft\ApplicationBundle\DataCollector;

use Symfony\Component\HttpKernel\DataCollector\DataCollector;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RequestStack;

use Vankosoft\ApplicationBundle\Component\Application\Kernel;

/**
 * Tutorial: https://symfony.com/doc/current/profiler/data_collector.html
 * Example: Sylius\Bundle\CoreBundle\Collector\SyliusCollector
 */
final class VsApplicationCollector extends DataCollector
{
    public function __construct(
        RequestStack $requestStack,
        string $version,
        array $bundles,
        string $defaultLocaleCode
    ) {
        $mainRequest    = $requestStack->getMainRequest();
        
        if ( $mainRequest ) {
            $this->data = [
                'version'               => $version,
                'default_locale_code'   => $defaultLocaleCode,
                'locale_code'           => $mainRequest->getLocale(),
                'extensions'            => [
                    'VSUsersSubscriptionsBundle'    => ['name' => 'Subscription', 'enabled' => false],
                    'VSPaymentBundle'               => ['name' => 'Payment', 'enabled' => false],
                    'VSApiBundle'                   => ['name' => 'API', 'enabled' => false],
                ],
            ];
            
            foreach ( array_keys( $this->data['extensions'] ) as $bundleName ) {
                if ( isset( $bundles[$bundleName] ) ) {
                    $this->data['extensions'][$bundleName]['enabled']   = true;
                }
            }
        }
    }
    
    public function getVersion()
    {
        return $this->data['version'];
    }
    
    public function getExtensions(): array
    {
        return $this->data['extensions'];
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
