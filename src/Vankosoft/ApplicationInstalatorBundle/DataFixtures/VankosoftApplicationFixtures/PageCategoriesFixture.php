<?php namespace VS\ApplicationInstalatorBundle\DataFixtures\VankosoftApplicationFixtures;

use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use VS\ApplicationInstalatorBundle\DataFixtures\AbstractResourceFixture;

final class PageCategoriesFixture extends AbstractResourceFixture
{
    public function getName(): string
    {
        return 'page_categories';
    }
    
    protected function configureResourceNode( ArrayNodeDefinition $resourceNode ): void
    {
        $resourceNode
            ->children()
                ->scalarNode( 'locale' )->end()
                ->scalarNode( 'title' )->end()
                ->scalarNode( 'description' )->end()
                ->scalarNode( 'taxonomy_code' )->end()
        ;
    }
}
