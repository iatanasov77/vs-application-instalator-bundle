<?php namespace Vankosoft\ApplicationInstalatorBundle\DataFixtures\VankosoftApplicationFixtures;

use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Vankosoft\ApplicationInstalatorBundle\DataFixtures\AbstractResourceFixture;

final class UsersFixture extends AbstractResourceFixture
{
    public function getName(): string
    {
        return 'users';
    }
    
    protected function configureResourceNode( ArrayNodeDefinition $resourceNode ): void
    {
        $resourceNode
            ->children()
                ->scalarNode('role_code')->end()
                ->scalarNode('email')->cannotBeEmpty()->end()
                ->scalarNode('username')->cannotBeEmpty()->end()
                ->booleanNode('enabled')->end()
                ->booleanNode('api')->end()
                ->scalarNode('password')->cannotBeEmpty()->end()
                ->scalarNode('locale_code')->cannotBeEmpty()->end()
                ->scalarNode('title')->cannotBeEmpty()->end()
                ->scalarNode('first_name')->cannotBeEmpty()->end()
                ->scalarNode('last_name')->cannotBeEmpty()->end()
                ->scalarNode('avatar')->end()
        ;
    }
}
