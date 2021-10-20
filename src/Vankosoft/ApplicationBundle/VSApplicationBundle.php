<?php namespace VS\ApplicationBundle;

use Sylius\Bundle\ResourceBundle\AbstractResourceBundle;
use Sylius\Bundle\ResourceBundle\SyliusResourceBundle;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Doctrine\Bundle\DoctrineBundle\DependencyInjection\Compiler\DoctrineOrmMappingsPass;

class VSApplicationBundle extends AbstractResourceBundle
{
    public function getSupportedDrivers(): array
    {
        return [
            SyliusResourceBundle::DRIVER_DOCTRINE_ORM,
            SyliusResourceBundle::DRIVER_DOCTRINE_MONGODB_ODM,
        ];
    }
    
    public function getContainerExtension()
    {
        return new \VS\ApplicationBundle\DependencyInjection\VSApplicationExtension();
    }
    
    public function boot(): void
    {
        parent::boot();
    }
    
    public function build( ContainerBuilder $container ): void
    {
        parent::build( $container );
        
        $mappings = [
            realpath( __DIR__.'/Resources/config/doctrine-mapping' ) => 'VS\ApplicationBundle\Model',
        ];
        
        if ( class_exists( 'Doctrine\Bundle\DoctrineBundle\DependencyInjection\Compiler\DoctrineOrmMappingsPass' ) ) {
            $container->addCompilerPass( DoctrineOrmMappingsPass::createXmlMappingDriver( $mappings ) );
            //$container->addCompilerPass( DoctrineOrmMappingsPass::createYamlMappingDriver( $mappings ) );
            //$container->addCompilerPass( DoctrineOrmMappingsPass::createAnnotationMappingDriver( \array_values( $mappings ), \array_keys( $mappings ) ) );
        }
    }
    
    protected function getModelNamespace(): string
    {
        return 'VS\ApplicationBundle\Model';
    }
}
