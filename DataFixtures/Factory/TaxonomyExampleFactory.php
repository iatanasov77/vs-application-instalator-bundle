<?php namespace Vankosoft\ApplicationInstalatorBundle\DataFixtures\Factory;

use Symfony\Component\OptionsResolver\OptionsResolver;
use Sylius\Component\Resource\Factory\FactoryInterface;

use Vankosoft\ApplicationBundle\Component\SlugGenerator;
use Vankosoft\ApplicationBundle\Model\Interfaces\TaxonomyInterface;

class TaxonomyExampleFactory extends AbstractExampleFactory implements ExampleFactoryInterface, ExampleTranslationsFactoryInterface
{
    /** @var FactoryInterface */
    private $taxonomyFactory;
    
    /** @var FactoryInterface */
    private $taxonFactory;
    
    /** @var OptionsResolver */
    private $optionsResolver;
    
    /** @var SlugGenerator */
    private $slugGenerator;
    
    public function __construct(
        FactoryInterface $taxonomyFactory,
        FactoryInterface $taxonFactory,
        SlugGenerator $slugGenerator
    ) {
            $this->taxonomyFactory  = $taxonomyFactory;
            $this->taxonFactory     = $taxonFactory;
            $this->slugGenerator    = $slugGenerator;
            
            $this->optionsResolver  = new OptionsResolver();
            $this->configureOptions( $this->optionsResolver );
    }
    
    public function create( array $options = [] ): TaxonomyInterface
    {
        $options    = $this->optionsResolver->resolve( $options );
        
        $taxonomyEntity             = $this->taxonomyFactory->createNew();
        $taxonomyRootTaxonEntity    = $this->taxonFactory->createNew();
        
        $slug                       = $this->slugGenerator->generate( $options['title'] );
        
        $taxonomyRootTaxonEntity->setCurrentLocale( $options['locale'] );
        $taxonomyRootTaxonEntity->setFallbackLocale( 'en_US' );
        $taxonomyRootTaxonEntity->setCode( $slug );
        $taxonomyRootTaxonEntity->getTranslation()->setName( 'Root taxon of Taxonomy: "' . $options['title'] );
        $taxonomyRootTaxonEntity->getTranslation()->setDescription( 'Root taxon of Taxonomy: "' . $options['title'] . '"' );
        $taxonomyRootTaxonEntity->getTranslation()->setSlug( $slug );
        $taxonomyRootTaxonEntity->getTranslation()->setTranslatable( $taxonomyRootTaxonEntity );
        
        $taxonomyEntity->setFallbackLocale( 'en_US' );
        $taxonomyEntity->setTranslatableLocale( $options['locale'] );
        $taxonomyEntity->setCode( $options['code'] );
        $taxonomyEntity->setName( $options['title'] );
        $taxonomyEntity->setDescription( $options['description'] );
        $taxonomyEntity->setRootTaxon( $taxonomyRootTaxonEntity );
        
        return $taxonomyEntity;
    }
    
    public function createTranslation( $entity, $localeCode, $options = [] )
    {
        $taxonomyRootTaxonEntity    = $entity->getRootTaxon();
        
        $this->slugGenerator->setLocaleCode( $localeCode );
        $slug                       = $this->slugGenerator->generate( $options['title'] );
        
        $taxonomyRootTaxonEntity->getTranslation( $localeCode );
        $taxonomyRootTaxonEntity->setCurrentLocale( $localeCode );
        if ( ! in_array( $localeCode, $taxonomyRootTaxonEntity->getExistingTranslations() ) ) {
            $translation    = $taxonomyRootTaxonEntity->createNewTranslation();
            
            $translation->setLocale( $localeCode );
            $translation->setName( $options['title'] );
            $translation->setDescription( $options['description'] );
            
            $this->slugGenerator->setLocaleCode( $localeCode );
            $translation->setSlug( $this->slugGenerator->generate( $options['title'] ) );
            
            $taxonomyRootTaxonEntity->addTranslation( $translation );
        } else {
            $translation   = $taxonomyRootTaxonEntity->getTranslation( $localeCode );
            
            $translation->setName( $options['title'] );
            $translation->setDescription( $options['description'] );
        }
        
        $entity->setTranslatableLocale( $localeCode );
        $entity->setName( $options['title'] );
        $entity->setDescription( $options['description'] );
        $entity->setRootTaxon( $taxonomyRootTaxonEntity );
        
        return $entity;
    }
    
    protected function configureOptions( OptionsResolver $resolver ): void
    {
        $resolver
            ->setDefault( 'code', null )
            ->setAllowedTypes( 'code', ['string'] )
        
            ->setDefault( 'title', null )
            ->setAllowedTypes( 'title', ['string'] )
            
            ->setDefault( 'description', null )
            ->setAllowedTypes( 'description', ['string'] )
            
            ->setDefault( 'locale', 'en_US' )
            ->setAllowedTypes( 'locale', ['string'] )
            
            ->setDefault( 'translations', [] )
            ->setAllowedTypes( 'translations', ['array'] )
        ;
    }
}
