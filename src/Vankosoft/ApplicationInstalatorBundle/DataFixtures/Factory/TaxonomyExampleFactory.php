<?php namespace Vankosoft\ApplicationInstalatorBundle\DataFixtures\Factory;

use Symfony\Component\OptionsResolver\OptionsResolver;
use Sylius\Component\Resource\Factory\FactoryInterface;

use Vankosoft\ApplicationBundle\Component\Slug;
use Vankosoft\ApplicationBundle\Model\Interfaces\TaxonomyInterface;

class TaxonomyExampleFactory extends AbstractExampleFactory implements ExampleFactoryInterface
{
    /** @var FactoryInterface */
    private $taxonomyFactory;
    
    /** @var FactoryInterface */
    private $taxonFactory;
    
    /** @var OptionsResolver */
    private $optionsResolver;
    
    public function __construct(
        FactoryInterface $taxonomyFactory,
        FactoryInterface $taxonFactory
    ) {
            $this->taxonomyFactory  = $taxonomyFactory;
            $this->taxonFactory     = $taxonFactory;
            
            $this->optionsResolver  = new OptionsResolver();
            $this->configureOptions( $this->optionsResolver );
    }
    
    public function create( array $options = [] ): TaxonomyInterface
    {
        $options    = $this->optionsResolver->resolve( $options );
        
        $taxonomyEntity             = $this->taxonomyFactory->createNew();
        $taxonomyRootTaxonEntity    = $this->taxonFactory->createNew();
        
        $slug   = Slug::generate( $options['title'] );
        $taxonomyRootTaxonEntity->setCode( $slug );
        $taxonomyRootTaxonEntity->setCurrentLocale( $options['locale'] );
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
        ;
    }
}
