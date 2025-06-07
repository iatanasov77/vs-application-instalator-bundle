<?php namespace Vankosoft\ApplicationInstalatorBundle\DataFixtures\Factory;

use Symfony\Component\OptionsResolver\OptionsResolver;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Sylius\Resource\Doctrine\Persistence\RepositoryInterface;

use Vankosoft\CmsBundle\Model\Interfaces\PageCategoryInterface;
use Vankosoft\ApplicationBundle\Component\SlugGenerator;

class PageCategoriesExampleFactory extends AbstractExampleFactory implements ExampleFactoryInterface, ExampleTranslationsFactoryInterface
{
    /** @var RepositoryInterface */
    private $taxonomyRepository;
    
    /** @var FactoryInterface */
    private $taxonFactory;
    
    /** @var FactoryInterface */
    private $pageCategoriesFactory;
    
    /** @var OptionsResolver */
    private $optionsResolver;
    
    /** @var SlugGenerator */
    private $slugGenerator;
    
    public function __construct(
        RepositoryInterface $taxonomyRepository,
        FactoryInterface $taxonFactory,
        FactoryInterface $pageCategoriesFactory,
        SlugGenerator $slugGenerator
    ) {
        $this->taxonomyRepository       = $taxonomyRepository;
        $this->taxonFactory             = $taxonFactory;
        $this->pageCategoriesFactory    = $pageCategoriesFactory;
        $this->slugGenerator            = $slugGenerator;
        
        $this->optionsResolver          = new OptionsResolver();
        $this->configureOptions( $this->optionsResolver );
    }
    
    public function create( array $options = [] ): PageCategoryInterface
    {
        $options                    = $this->optionsResolver->resolve( $options );
        
        $taxonomyRootTaxonEntity    = $this->taxonomyRepository->findByCode( $options['taxonomy_code'] )->getRootTaxon();
        $pageCategoryEntity         = $this->pageCategoriesFactory->createNew();
        
        $taxonEntity                = $this->taxonFactory->createNew();
        $slug                       = $this->slugGenerator->generate( $options['title'] );
        
        $taxonEntity->setCurrentLocale( $options['locale'] );
        $taxonEntity->setFallbackLocale( 'en_US' );
        $taxonEntity->setCode( $slug );
        $taxonEntity->getTranslation()->setName( $options['title'] );
        $taxonEntity->getTranslation()->setDescription( $options['description'] );
        $taxonEntity->getTranslation()->setSlug( $slug );
        $taxonEntity->getTranslation()->setTranslatable( $taxonEntity );
        
        $taxonEntity->setParent( $taxonomyRootTaxonEntity );
        $pageCategoryEntity->setTaxon( $taxonEntity );
        
        return $pageCategoryEntity;
    }
    
    public function createTranslation( $entity, $localeCode, $options = [] )
    {
        $taxonEntity    = $entity->getTaxon();
        
        $taxonEntity->getTranslation( $localeCode );
        $taxonEntity->setCurrentLocale( $localeCode );
        if ( ! in_array( $localeCode, $taxonEntity->getExistingTranslations() ) ) {
            $translation    = $taxonEntity->createNewTranslation();
            
            $translation->setLocale( $localeCode );
            $translation->setName( $options['title'] );
            $translation->setDescription( $options['description'] );
            
            $this->slugGenerator->setLocaleCode( $localeCode );
            $translation->setSlug( $this->slugGenerator->generate( $options['title'] ) );
            
            $taxonEntity->addTranslation( $translation );
        } else {
            $translation   = $taxonEntity->getTranslation( $localeCode );
            
            $translation->setName( $options['title'] );
            $translation->setDescription( $options['description'] );
        }
        
        $entity->setTaxon( $taxonEntity );
        
        return $entity;
    }
    
    protected function configureOptions( OptionsResolver $resolver ): void
    {
        $resolver
            ->setDefault( 'title', null )
            ->setAllowedTypes( 'title', ['string'] )
            
            ->setDefault( 'description', null )
            ->setAllowedTypes( 'description', ['string'] )
            
            ->setDefault( 'locale', 'en_US' )
            ->setAllowedTypes( 'locale', ['string'] )
            
            ->setDefault( 'taxonomy_code', null )
            ->setAllowedTypes( 'taxonomy_code', ['string'] )
            
            ->setDefault( 'translations', [] )
            ->setAllowedTypes( 'translations', ['array'] )
        ;
    }
    
}
