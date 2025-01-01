<?php namespace Vankosoft\ApplicationBundle\Component\Application;

use Symfony\Component\HttpKernel\Kernel as HttpKernel;
use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;

use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\Routing\Loader\Configurator\RoutingConfigurator;

/**
 * @see \Sylius\Bundle\CoreBundle\Application\Kernel
 */
abstract class Kernel extends HttpKernel
{
    use MicroKernelTrait;
    
    const VERSION   = '1.4.20';
    const APP_ID    = 'admin-panel';
    
    public function getVarDir(): string
    {
        $dirVar = $this->getProjectDir() . '/var';
        if ( $this->isVagrantEnvironment() ) {
            return '/dev/shm/' . $_ENV['HOST'];
        }
        
        return $dirVar;
    }
    
    /**
     * {@inheritdoc}
     */
    public function getCacheDir(): string
    {
        return $this->getVarDir() . '/' . static::APP_ID . '/cache/' . $this->environment;
    }
    
    /**
     * {@inheritdoc}
     */
    public function getLogDir(): string
    {
        return $this->getVarDir() . '/' . static::APP_ID . '/log';
    }
    
    /**
     * Override MicroKernelTrait::registerBundles()
     *
     * {@inheritdoc}
     */
    public function registerBundles(): iterable
    {
        $contents = require $this->__getConfigDir() . '/bundles.php';
        foreach ( $contents as $class => $envs ) {
            if ( $envs[$this->environment] ?? $envs['all'] ?? false ) {
                yield new $class();
            }
        }
    }
    
    protected function configureContainer( ContainerConfigurator $container, LoaderInterface $loader, ContainerBuilder $builder ): void
    {
        // Set Shared Sessions Path
        $builder->setParameter( 'vs_application.session_save_path', $this->getVarDir() . '/sessions/' );
        
        $configDir  = $this->__getConfigDir();
        
        $container->import( $configDir . '/{packages}/*.yaml' );
        $container->import( $configDir . '/{packages}/'. $this->environment . '/*.yaml' );
        
        if ( is_file( $configDir . '/services.yaml' ) ) {
            $container->import( $configDir . '/services.yaml' );
            $container->import( $configDir . '/{services}_' . $this->environment.'.yaml' );
        } else {
            $container->import( $configDir . '/{services}.php' );
        }
    }
    
    protected function configureRoutes( RoutingConfigurator $routes ): void
    {
        $configDir = $this->__getConfigDir();
        
        $routes->import( $configDir . '/{routes}/' . $this->environment . '/*.yaml' );
        $routes->import( $configDir . '/{routes}/*.yaml' );
        
        if ( is_file( $configDir . '/routes.yaml' ) ) {
            $routes->import( $configDir . '/routes.yaml' );
        } else {
            $routes->import( $configDir . '/{routes}.php' );
        }
    }
    
    protected function isVagrantEnvironment(): bool
    {
        //return ( getenv( 'HOME' ) === '/home/vagrant' || getenv( 'VAGRANT' ) === 'VAGRANT' ) && is_dir( '/dev/shm' );
        //return is_dir( '/home/vagrant' ) && is_dir( '/dev/shm' );
        return (bool)shell_exec( 'id -u vagrant 2>/dev/null' ) && is_dir( '/dev/shm' );
    }
    
    abstract protected function __getConfigDir(): string;
}
