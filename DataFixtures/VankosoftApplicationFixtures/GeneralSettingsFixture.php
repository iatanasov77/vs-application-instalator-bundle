<?php namespace Vankosoft\ApplicationInstalatorBundle\DataFixtures\VankosoftApplicationFixtures;

use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Vankosoft\ApplicationInstalatorBundle\DataFixtures\AbstractResourceFixture;

final class GeneralSettingsFixture extends AbstractResourceFixture
{
    public function getName(): string
    {
        return 'general_settings';
    }
    
    protected function configureResourceNode( ArrayNodeDefinition $resourceNode ): void
    {
        $resourceNode
            ->children()
                ->booleanNode( 'maintenanceMode' )->defaultFalse()->end()
                
                ->scalarNode( 'theme' )->end()
                ->scalarNode( 'application' )->end()
                ->scalarNode( 'maintenancePage' )->end()
        ;
    }
}
