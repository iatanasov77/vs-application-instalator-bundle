<?php namespace Vankosoft\ApplicationBundle\Component\Application;

use Symfony\Component\HttpKernel\Kernel as HttpKernel;
use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;

use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Routing\RouteCollectionBuilder;

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\Routing\Loader\Configurator\RoutingConfigurator;

/**
 * @see \Sylius\Bundle\CoreBundle\Application\Kernel
 */
abstract class Kernel extends HttpKernel
{
    use MicroKernelTrait;
    
    const VERSION       = '1.4.20';
    const APP_ID        = 'admin-panel';
    
    public function getVarDir()
    {
        $dirVar = $this->getProjectDir() . '/var';
        if ( $this->isVagrantEnvironment() ) {
            return '/dev/shm/' . $_ENV['HOST'];
        }
        
        return $dirVar;
    }
    
    public function getCacheDir()
    {
        return $this->getVarDir() . '/' . static::APP_ID . '/cache/' . $this->environment;
    }
    
    public function getLogDir()
    {
        return $this->getVarDir() . '/' . static::APP_ID . '/log';
    }
    
    protected function isVagrantEnvironment(): bool
    {
        return ( getenv( 'HOME' ) === '/home/vagrant' || getenv( 'VAGRANT' ) === 'VAGRANT' ) && is_dir( '/dev/shm' );
    }
    
    /**
     * {@inheritdoc}
     */
    abstract protected function configureContainer( ContainerConfigurator $container, LoaderInterface $loader, ContainerBuilder $builder ): void;
    
    /**
     * {@inheritdoc}
     */
    abstract protected function configureRoutes( RoutingConfigurator $routes ): void;
}
