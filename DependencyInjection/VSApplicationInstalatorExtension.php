<?php namespace Vankosoft\ApplicationInstalatorBundle\DependencyInjection;

use Sylius\Bundle\ResourceBundle\DependencyInjection\Extension\AbstractResourceExtension;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\Loader;

/**
 * This is the class that loads and manages your bundle configuration
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html}
 */
class VSApplicationInstalatorExtension extends AbstractResourceExtension
{
    /**
     * {@inheritDoc}
     */
    public function load( array $config, ContainerBuilder $container ): void
    {
        $config = $this->processConfiguration( $this->getConfiguration([], $container), $config );
        $loader = new Loader\YamlFileLoader( $container, new FileLocator( __DIR__.'/../Resources/config' ) );

        $loader->load( 'services.yaml' );
        
        // Register resources
        $this->registerResources( 'vs_application_instalator', $config['orm_driver'], $config['resources'], $container );
    }
}
