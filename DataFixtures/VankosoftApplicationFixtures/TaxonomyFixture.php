<?php namespace Vankosoft\ApplicationInstalatorBundle\DataFixtures\VankosoftApplicationFixtures;

use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Vankosoft\ApplicationInstalatorBundle\DataFixtures\AbstractResourceFixture;

final class TaxonomyFixture extends AbstractResourceFixture
{
    public function getName(): string
    {
        return 'taxonomy';
    }
    
    protected function configureResourceNode( ArrayNodeDefinition $resourceNode ): void
    {
        $resourceNode
            ->children()
                ->scalarNode( 'code' )->end()
                ->scalarNode( 'title' )->end()
                ->scalarNode( 'description' )->end()
                ->scalarNode( 'locale' )->end()
                ->variableNode( 'translations' )->cannotBeEmpty()->defaultValue( [] )->end()
        ;
    }
}
