<?php namespace Vankosoft\ApplicationInstalatorBundle\DataFixtures\VankosoftApplicationFixtures;

use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Vankosoft\ApplicationInstalatorBundle\DataFixtures\AbstractResourceFixture;

final class UserRolesFixture extends AbstractResourceFixture
{
    public function getName(): string
    {
        return 'user_roles';
    }
    
    protected function configureResourceNode( ArrayNodeDefinition $resourceNode ): void
    {
        $resourceNode
            ->children()
                ->scalarNode( 'locale' )->end()
                ->scalarNode( 'title' )->end()
                ->scalarNode( 'description' )->end()
                ->scalarNode( 'taxonomy_code' )->end()
                ->scalarNode( 'role' )->end()
                ->scalarNode( 'parent' )->end()
                ->variableNode( 'translations' )->cannotBeEmpty()->defaultValue( [] )->end()
        ;
    }
}
