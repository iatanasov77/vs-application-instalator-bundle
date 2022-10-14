<?php namespace Vankosoft\ApplicationInstalatorBundle\DataFixtures\VankosoftApplicationFixtures;

use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Vankosoft\ApplicationInstalatorBundle\DataFixtures\AbstractResourceFixture;

final class LocalesFixture extends AbstractResourceFixture
{
    public function getName(): string
    {
        return 'locales';
    }
    
    protected function configureResourceNode( ArrayNodeDefinition $resourceNode ): void
    {
        $resourceNode
            ->children()
                ->scalarNode( 'translatableLocale' )->end()
                ->scalarNode( 'title' )->end()
                ->scalarNode( 'code' )->end()
        ;
    }
}
