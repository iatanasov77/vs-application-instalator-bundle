<?php namespace Vankosoft\ApplicationBundle\DependencyInjection;

use Sylius\Bundle\ResourceBundle\DependencyInjection\Extension\AbstractResourceExtension;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;

/**
 * This is the class that loads and manages your bundle configuration
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html}
 */
class VSApplicationExtension extends AbstractResourceExtension implements PrependExtensionInterface
{
    use PrependDoctrineMigrationsTrait;
    
    /**
     * {@inheritDoc}
     */
    public function load( array $config, ContainerBuilder $container )
    {
        $config = $this->processConfiguration( $this->getConfiguration([], $container), $config );
        $this->prepend( $container );
        
        $loader = new Loader\YamlFileLoader( $container, new FileLocator( __DIR__.'/../Resources/config' ) );
        $loader->load( 'services.yaml' );
        if ( $container->getParameter( 'kernel.environment' ) == 'dev' ) {
            $loader->load( 'services_dev.yaml' );
        }
        
        // Register resources
        $this->registerResources( 'vs_application', $config['orm_driver'], $config['resources'], $container );
        /*
        $container->getDefinition( 'vs_application.repository.application' )->setLazy( true );
        $container->getDefinition( 'vs_application.repository.locale' )->setLazy( true );
        */
        
        // Set values need to be accesible from controller
        $container->setParameter( 'vs_application.project_type', $config[ 'project_type' ] );
        $container->setParameter( 'vs_application.taxonomy', $config[ 'taxonomy' ] );
        
        // VankoSoft API
        $container->setParameter( 'vs_application.vankosoft_api.enabled', $config[ 'vankosoft_api' ]['enabled'] );
        $container->setParameter( 'vs_application.vankosoft_api.project', $config[ 'vankosoft_api' ]['project'] );
        $container->setParameter( 'vs_application.vankosoft_api.host', $config[ 'vankosoft_api' ]['connection']['host'] );
        $container->setParameter( 'vs_application.vankosoft_api.user', $config[ 'vankosoft_api' ]['connection']['user'] );
        $container->setParameter( 'vs_application.vankosoft_api.password', $config[ 'vankosoft_api' ]['connection']['password'] );
    }
    
    public function prepend( ContainerBuilder $container ): void
    {
        $config = $container->getExtensionConfig( $this->getAlias() );
        $config = $this->processConfiguration( $this->getConfiguration( [], $container ), $config );
        
        $this->prependDoctrineMigrations( $container );
    }
    
    private function debugExtensionConfig( ContainerBuilder $container, string $extension )
    {
        $debugArray = $container->getExtensionConfig( $extension );
        
        $fileLocator = new FileLocator( $container->getParameter( 'kernel.project_dir' ) );
        $debugArray['MigrationsPath'] = $fileLocator->locate("@VSApplicationBundle/DoctrineMigrations");
        
        return $debugArray;
    }
}
