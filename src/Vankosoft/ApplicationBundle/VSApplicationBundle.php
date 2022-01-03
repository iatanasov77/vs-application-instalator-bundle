<?php namespace Vankosoft\ApplicationBundle;

use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Doctrine\Bundle\DoctrineBundle\DependencyInjection\Compiler\DoctrineOrmMappingsPass;

use Sylius\Bundle\ResourceBundle\AbstractResourceBundle;
use Sylius\Bundle\ResourceBundle\SyliusResourceBundle;

class VSApplicationBundle extends AbstractResourceBundle
{
    public function getSupportedDrivers(): array
    {
        return [
            SyliusResourceBundle::DRIVER_DOCTRINE_ORM,
            SyliusResourceBundle::DRIVER_DOCTRINE_MONGODB_ODM,
        ];
    }
    
    public function boot(): void
    {
        parent::boot();
    }
    
    public function build( ContainerBuilder $container ): void
    {
        parent::build( $container );
        
        $mappings = [
            realpath( __DIR__.'/Resources/config/doctrine-mapping' ) => 'Vankosoft\ApplicationBundle\Model',
        ];
        
        if ( class_exists( 'Doctrine\Bundle\DoctrineBundle\DependencyInjection\Compiler\DoctrineOrmMappingsPass' ) ) {
            $container->addCompilerPass( DoctrineOrmMappingsPass::createXmlMappingDriver( $mappings ) );
            //$container->addCompilerPass( DoctrineOrmMappingsPass::createYamlMappingDriver( $mappings ) );
            //$container->addCompilerPass( DoctrineOrmMappingsPass::createAnnotationMappingDriver( \array_values( $mappings ), \array_keys( $mappings ) ) );
        }
    }
    
    /**
     * {@inheritdoc}
     */
    public function getContainerExtension(): ?ExtensionInterface
    {
        return new \Vankosoft\ApplicationBundle\DependencyInjection\VSApplicationExtension();
    }
    
    protected function getModelNamespace(): string
    {
        return 'Vankosoft\ApplicationBundle\Model';
    }
}
