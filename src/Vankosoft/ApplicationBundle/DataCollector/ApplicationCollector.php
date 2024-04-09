<?php namespace Vankosoft\ApplicationBundle\DataCollector;

use Symfony\Component\HttpKernel\DataCollector\DataCollector;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use Vankosoft\ApplicationBundle\Component\Context\ApplicationContextInterface;
use Vankosoft\ApplicationBundle\Component\Context\ApplicationNotFoundException;
use Vankosoft\ApplicationBundle\Model\Interfaces\ApplicationInterface;
use Vankosoft\ApplicationBundle\Repository\Interfaces\ApplicationRepositoryInterface;

/**
 * Tutorial: https://symfony.com/doc/current/profiler/data_collector.html
 * Example: Sylius\Bundle\ChannelBundle\Collector\ChannelCollector
 */
final class ApplicationCollector extends DataCollector
{
    /** @var ApplicationContextInterface */
    private $applicationContext;
    
    /** @var ApplicationRepositoryInterface */
    private $applicationRepository;
    
    /** @var bool */
    private $applicationChangeSupport;
    
    public function __construct(
        ApplicationRepositoryInterface $applicationRepository,
        ApplicationContextInterface $applicationContext,
        bool $applicationChangeSupport  = false
    ) {
        $this->applicationContext       = $applicationContext;
        $this->applicationRepository    = $applicationRepository;
        $this->applicationChangeSupport = $applicationChangeSupport;
    }
    
    public function getApplication(): ?array
    {
        return $this->data['application'];
    }
    
    /**
     * @return iterable|ApplicationInterface[]
     */
    public function getApplications() : iterable
    {
        return $this->data['applications'];
    }
    
    public function isApplicationChangeSupported(): bool
    {
        return $this->data['application_change_support'];
    }
    
    public function collect( Request $request, Response $response, \Throwable $exception = null ): void
    {
        try {
            $application    = $this->pluckApplication( $this->applicationContext->getApplication() );
        } catch ( ApplicationNotFoundException $exception ) {
            
        }
        
        $this->data                 = [
            'application'                   => $application,
            'applications'                  => array_map([$this, 'pluckApplication'], $this->applicationRepository->findAll()),
            'application_change_support'    => $this->applicationChangeSupport,
        ];
    }
    
    public function reset(): void
    {
        $this->data['application']  = null;
    }
    
    public function getName(): string
    {
        return 'vs_application.application_collector';
    }
    
    private function pluckApplication( ApplicationInterface $application ): array
    {
        return [
            'name'      => $application->getTitle(),
            'hostname'  => $application->getHostname(),
            'code'      => $application->getCode(),
        ];
    }
}
