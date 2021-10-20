<?php namespace VS\ApplicationInstalatorBundle\DataFixtures\VankosoftApplicationFixtures;

use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use VS\ApplicationInstalatorBundle\DataFixtures\AbstractResourceFixture;

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
                ->scalarNode( 'text' )->end()
                ->booleanNode( 'published' )->defaultTrue()->end()
                
                ->scalarNode( 'category_code' )->end()
            ;
    }
}
