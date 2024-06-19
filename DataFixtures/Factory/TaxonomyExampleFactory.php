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
        $taxonomyRootTaxonEntity->setCode( $slug );
        $taxonomyRootTaxonEntity->getTranslation()->setName( 'Root taxon of Taxonomy: "' . $options['title'] );
        $taxonomyRootTaxonEntity->getTranslation()->setDescription( 'Root taxon of Taxonomy: "' . $options['title'] . '"' );
        $taxonomyRootTaxonEntity->getTranslation()->setSlug( $slug );
        $taxonomyRootTaxonEntity->getTranslation()->setTranslatable( $taxonomyRootTaxonEntity );
        
        $taxonomyEntity->setCode( $options['code'] );
        $taxonomyEntity->setName( $options['title'] );
        $taxonomyEntity->setDescription( $options['description'] );
        $taxonomyEntity->setRootTaxon( $taxonomyRootTaxonEntity );
        
        return $taxonomyEntity;
    }
    
    public function createTranslation( $entity, $localeCode, $options = [] )
    {
//         $taxonomyRootTaxonEntity    = $entity->getRootTaxon();
            
//         $taxonomyRootTaxonEntity->setCurrentLocale( $localeCode );
//         $taxonomyRootTaxonEntity->getTranslation()->setName( $options['title'] );
//         $taxonomyRootTaxonEntity->getTranslation()->setDescription( $options['description'] );
//         $taxonomyRootTaxonEntity->getTranslation()->setTranslatable( $taxonomyRootTaxonEntity );
        
//         $entity->setRootTaxon( $taxonomyRootTaxonEntity );

        $entity->setTranslatableLocale( $localeCode );
        $entity->setName( $options['title'] );
        $entity->setDescription( $options['description'] );
        
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
