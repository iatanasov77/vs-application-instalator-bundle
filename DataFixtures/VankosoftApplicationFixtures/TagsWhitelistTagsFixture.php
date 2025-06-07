<?php namespace Vankosoft\ApplicationInstalatorBundle\DataFixtures\VankosoftApplicationFixtures;

use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Vankosoft\ApplicationInstalatorBundle\DataFixtures\AbstractResourceFixture;

final class TagsWhitelistTagsFixture extends AbstractResourceFixture
{
    public function getName(): string
    {
        return 'tags_whitelist_tags';
    }
    
    protected function configureResourceNode( ArrayNodeDefinition $resourceNode ): void
    {
        $resourceNode
            ->children()
                ->scalarNode( 'context_code' )->end()
                ->scalarNode( 'tag' )->end()
        ;
    }
}