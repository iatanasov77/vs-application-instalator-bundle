<?php namespace VS\ApplicationBundle\Component\Application;

use Symfony\Component\HttpKernel\Kernel as HttpKernel;
use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;

use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Routing\RouteCollectionBuilder;

/**
 * @see \Sylius\Bundle\CoreBundle\Application\Kernel
 */
abstract class Kernel extends HttpKernel
{
    use MicroKernelTrait;
    
    const VERSION       = '1.4.20';
    
    const APP_ID        = 'admin-panel';

    const CONFIG_EXTS   = '.{php,xml,yaml,yml}';
    
    public function getVarDir()
    {
        $dirVar = $this->getProjectDir() . '/var';
        if ( isset( $_ENV['DIR_VAR'] ) ) {
            $dirVar = $_ENV['DIR_VAR'];
        }
        
        return $dirVar;
    }
    
    public function getCacheDir()
    {
        return $this->getVarDir() . '/' . static::APP_ID . '/cache/' . $this->environment;
        //return parent::getCacheDir();
    }
    
    public function getLogDir()
    {
        return $this->getVarDir() . '/' . static::APP_ID . '/log';
        //return parent::getLogDir();
    }
    
    protected function isVagrantEnvironment(): bool
    {
        return (getenv('HOME') === '/home/vagrant' || getenv('VAGRANT') === 'VAGRANT') && is_dir('/dev/shm');
    }
    
    abstract protected function configureContainer( ContainerBuilder $container, LoaderInterface $loader ): void;
    
    abstract protected function configureRoutes( RouteCollectionBuilder $routes ): void;
}
