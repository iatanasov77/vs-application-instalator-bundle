<?php namespace Vankosoft\ApplicationInstalatorBundle\DataFixtures\VankosoftApplicationFixtures;

use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Vankosoft\ApplicationInstalatorBundle\DataFixtures\AbstractResourceFixture;

final class PagesFixture extends AbstractResourceFixture
{
    public function getName(): string
    {
        return 'pages';
    }
    
    protected function configureResourceNode( ArrayNodeDefinition $resourceNode ): void
    {
        $resourceNode
            ->children()
                ->scalarNode( 'locale' )->end()
                ->scalarNode( 'title' )->end()
                ->scalarNode( 'description' )->end()
                ->scalarNode( 'text' )->end()
                ->booleanNode( 'published' )->defaultTrue()->end()
                
                ->scalarNode( 'category_code' )->end()
                ->variableNode( 'translations' )->cannotBeEmpty()->defaultValue( [] )->end()
            ;
    }
}
