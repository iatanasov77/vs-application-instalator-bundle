<?php namespace Vankosoft\ApplicationInstalatorBundle\DataFixtures\VankosoftApplicationFixtures;

use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Vankosoft\ApplicationInstalatorBundle\DataFixtures\AbstractResourceFixture;

final class WidgetsFixture extends AbstractResourceFixture
{
    public function getName(): string
    {
        return 'widgets';
    }
    
    protected function configureResourceNode( ArrayNodeDefinition $resourceNode ): void
    {
        $resourceNode
            ->children()
                ->scalarNode( 'locale' )->end()
                ->scalarNode( 'name' )->end()
                ->scalarNode( 'description' )->end()
                ->scalarNode( 'group_code' )->end()
                ->booleanNode( 'active' )->defaultTrue()->end()
                ->booleanNode( 'allowAnonymous' )->defaultFalse()->end()
                ->arrayNode( 'allowedRoles' )
                    ->arrayPrototype()
                        ->children()
                            ->scalarNode( 'role' )->end()
                        ->end()
                    ->end()
                ->end()
        ;
    }
}
