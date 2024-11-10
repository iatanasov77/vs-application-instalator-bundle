<?php namespace Vankosoft\ApplicationInstalatorBundle\DataFixtures\Factory;

use Symfony\Component\OptionsResolver\OptionsResolver;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Vankosoft\ApplicationBundle\Model\Interfaces\CookieConsentTranslationInterface;

class CookieConsentTranslationsExampleFactory extends AbstractExampleFactory implements ExampleFactoryInterface
{
    /** @var FactoryInterface */
    private $cookieConsentTranslationsFactory;
    
    /** @var OptionsResolver */
    private $optionsResolver;
    
    public function __construct(
        FactoryInterface $cookieConsentTranslationsFactory
    ) {
        $this->cookieConsentTranslationsFactory = $cookieConsentTranslationsFactory;
        
        $this->optionsResolver  = new OptionsResolver();
        $this->configureOptions( $this->optionsResolver );
    }
    
    public function create( array $options = [] ): CookieConsentTranslationInterface
    {
        $options    = $this->optionsResolver->resolve( $options );
        
        $entity     = $this->cookieConsentTranslationsFactory->createNew();
        
        $entity->setLanguageCode( $options['languageCode'] );
        $entity->setLocaleCode( $options['localeCode'] );
        $entity->setBtnAcceptAll( $options['btnAcceptAll'] );
        $entity->setBtnRejectAll( $options['btnRejectAll'] );
        $entity->setBtnAcceptNecessary( $options['btnAcceptNecessary'] );
        $entity->setBtnShowPreferences( $options['btnShowPreferences'] );
        $entity->setLabel( $options['label'] );
        $entity->setTitle( $options['title'] );
        $entity->setDescription( $options['description'] );
        
        return $entity;
    }
    
    protected function configureOptions( OptionsResolver $resolver ): void
    {
        $resolver
            ->setDefault( 'languageCode', null )
            ->setAllowedTypes( 'languageCode', ['string'] )
            
            ->setDefault( 'localeCode', null )
            ->setAllowedTypes( 'localeCode', ['string'] )
            
            ->setDefault( 'btnAcceptAll', null )
            ->setAllowedTypes( 'btnAcceptAll', ['string'] )
            
            ->setDefault( 'btnRejectAll', null )
            ->setAllowedTypes( 'btnRejectAll', ['string'] )
            
            ->setDefault( 'btnAcceptNecessary', null )
            ->setAllowedTypes( 'btnAcceptNecessary', ['string'] )
            
            ->setDefault( 'btnShowPreferences', null )
            ->setAllowedTypes( 'btnShowPreferences', ['string'] )
            
            ->setDefault( 'label', null )
            ->setAllowedTypes( 'label', ['string'] )
            
            ->setDefault( 'title', null )
            ->setAllowedTypes( 'title', ['string'] )
            
            ->setDefault( 'description', null )
            ->setAllowedTypes( 'description', ['string'] )
        ;
    }
}
