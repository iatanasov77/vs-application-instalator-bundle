<?php namespace Vankosoft\ApplicationInstalatorBundle\DataFixtures;

use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

use Doctrine\Persistence\ObjectManager;
use Sylius\Bundle\FixturesBundle\Fixture\FixtureInterface;

use Vankosoft\ApplicationInstalatorBundle\DataFixtures\Factory\ExampleFactoryInterface;
use Vankosoft\ApplicationInstalatorBundle\DataFixtures\Factory\ExampleTranslationsFactoryInterface;
use Vankosoft\ApplicationBundle\Model\Interfaces\TranslatableInterface;

abstract class AbstractResourceFixture implements FixtureInterface
{
    /** @var ObjectManager */
    private $objectManager;

    /** @var ExampleFactoryInterface */
    private $exampleFactory;

    /** @var OptionsResolver */
    private $optionsResolver;

    public function __construct( ObjectManager $objectManager, ExampleFactoryInterface $exampleFactory ) 
    {
        $this->objectManager    = $objectManager;
        $this->exampleFactory   = $exampleFactory;

        $this->optionsResolver =
            ( new OptionsResolver() )
                ->setDefault( 'random', 0)
                ->setAllowedTypes( 'random', 'int' )
                ->setDefault( 'prototype', [] )
                ->setAllowedTypes( 'prototype', 'array' )
                ->setDefault( 'custom', [] )
                ->setAllowedTypes( 'custom', 'array' )
                ->setNormalizer( 'custom', function (Options $options, array $custom ) {
                    if ( $options['random'] <= 0 ) {
                        return $custom;
                    }

                    return array_merge( $custom, array_fill( 0, $options['random'], $options['prototype'] ) );
                })
        ;
    }

    final public function load( array $options ): void
    {
        $options = $this->optionsResolver->resolve( $options );

        foreach ( $options['custom'] as $resourceOptions ) {
            
            $resource = $this->exampleFactory->create( $resourceOptions );
            $this->objectManager->persist( $resource );
            $this->objectManager->flush();
            $this->objectManager->refresh( $resource );
            
            if ( isset( $resourceOptions['translations'] ) && $this->exampleFactory instanceof ExampleTranslationsFactoryInterface ) {
                foreach ( $resourceOptions['translations'] as $localeCode => $translationOptions ) {
                    if ( isset( $resourceOptions['locale'] ) && $resourceOptions['locale'] == $localeCode ) {
                        continue;
                    }
                    
                    //$resourceCopy           = clone $resource;
                    $translationResource    = $this->exampleFactory->createTranslation( $resource, $localeCode, $translationOptions );
                    
                    if ( $resource instanceof TranslatableInterface ) {
                        if ( $translationResource->getTranslatableLocale() != $resource->getTranslatableLocale() ) {
                            $this->objectManager->persist( $translationResource );
                            $this->objectManager->flush();
                        }
                    } else {
                        $this->objectManager->persist( $translationResource );
                        $this->objectManager->flush();
                    }
                }
            }
        }

        $this->objectManager->clear();
    }

    final public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder( $this->getName() );

        /** @var ArrayNodeDefinition $optionsNode */
        $optionsNode = $treeBuilder->getRootNode();

        $optionsNode->children()
            ->integerNode( 'random')->min( 0 )->defaultValue( 0 )->end()
            ->variableNode( 'prototype' )->end()
        ;

        /** @var ArrayNodeDefinition $resourcesNode */
        $resourcesNode = $optionsNode->children()->arrayNode( 'custom' );

        /** @var ArrayNodeDefinition $resourceNode */
        $resourceNode = $resourcesNode->requiresAtLeastOneElement()->arrayPrototype();
        $this->configureResourceNode( $resourceNode );

        return $treeBuilder;
    }

    protected function configureResourceNode( ArrayNodeDefinition $resourceNode ): void
    {
        // empty
    }
}
