<?php namespace Vankosoft\ApplicationInstalatorBundle\DataFixtures\VankosoftApplicationFixtures;

use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Vankosoft\ApplicationInstalatorBundle\DataFixtures\AbstractResourceFixture;

final class CookieConsentTranslationsFixture extends AbstractResourceFixture
{
    public function getName(): string
    {
        return 'cookie_consent_translations';
    }
    
    protected function configureResourceNode( ArrayNodeDefinition $resourceNode ): void
    {
        $resourceNode
            ->children()
                ->scalarNode( 'languageCode' )->end()
                ->scalarNode( 'localeCode' )->end()
                ->scalarNode( 'btnAcceptAll' )->end()
                ->scalarNode( 'btnRejectAll' )->end()
                ->scalarNode( 'btnAcceptNecessary' )->end()
                ->scalarNode( 'btnShowPreferences' )->end()
                ->scalarNode( 'label' )->end()
                ->scalarNode( 'title' )->end()
                ->scalarNode( 'description' )->end()
        ;
    }
}
