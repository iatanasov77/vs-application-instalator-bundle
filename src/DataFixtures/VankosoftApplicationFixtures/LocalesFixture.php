<?php namespace VS\ApplicationInstalatorBundle\DataFixtures\VankosoftApplicationFixtures;

use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use VS\ApplicationInstalatorBundle\DataFixtures\AbstractResourceFixture;

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
                ->scalarNode( 'code' )->end()
        ;
    }
}
