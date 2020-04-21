<?php namespace VS\CmsBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Doctrine\Bundle\DoctrineBundle\DependencyInjection\Compiler\DoctrineOrmMappingsPass;

class VSCmsBundle extends Bundle
{
    public function getContainerExtension()
    {
        return new \VS\CmsBundle\DependencyInjection\IACmsExtension();
    }
    
    public function build( ContainerBuilder $container )
    {
        parent::build( $container );
        
        $mappings = [
            realpath( __DIR__.'/Resources/config/doctrine-mapping' ) => 'VS\CmsBundle\Model',
        ];
        
        if ( class_exists( 'Doctrine\Bundle\DoctrineBundle\DependencyInjection\Compiler\DoctrineOrmMappingsPass' ) ) {
            $container->addCompilerPass( DoctrineOrmMappingsPass::createXmlMappingDriver( $mappings ) );
            $container->addCompilerPass( DoctrineOrmMappingsPass::createYamlMappingDriver( $mappings ) );
            $container->addCompilerPass( DoctrineOrmMappingsPass::createAnnotationMappingDriver( \array_values( $mappings ), \array_keys( $mappings ) ) );
        }
    }
}
