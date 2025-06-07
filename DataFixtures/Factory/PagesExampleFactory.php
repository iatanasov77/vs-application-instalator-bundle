<?php namespace Vankosoft\ApplicationInstalatorBundle\DataFixtures\Factory;

use Symfony\Component\OptionsResolver\OptionsResolver;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Sylius\Resource\Doctrine\Persistence\RepositoryInterface;

use Vankosoft\CmsBundle\Model\Interfaces\PageInterface;
use Vankosoft\ApplicationBundle\Component\SlugGenerator;

class PagesExampleFactory extends AbstractExampleFactory implements ExampleFactoryInterface //, ExampleTranslationsFactoryInterface
{
    /** @var FactoryInterface */
    private $pagesFactory;
    
    /** @var RepositoryInterface */
    private $pageCategoryRepository;
    
    /** @var OptionsResolver */
    private $optionsResolver;
    
    /** @var SlugGenerator */
    private $slugGenerator;
    
    public function __construct(
        RepositoryInterface $pageCategoryRepository,
        FactoryInterface $pagesFactory,
        SlugGenerator $slugGenerator
    ) {
            $this->pageCategoryRepository   = $pageCategoryRepository;
            $this->pagesFactory             = $pagesFactory;
            $this->slugGenerator            = $slugGenerator;
            
            $this->optionsResolver          = new OptionsResolver();
            $this->configureOptions( $this->optionsResolver );
    }
    
    public function create( array $options = [] ): PageInterface
    {
        $options    = $this->optionsResolver->resolve( $options );

        $pageEntity = $this->pagesFactory->createNew();
        $slug       = $this->slugGenerator->generate( $options['title'] );
        
        $pageEntity->setSlug( $slug );
        $pageEntity->setTranslatableLocale( $options['locale'] );
        $pageEntity->setFallbackLocale( 'en_US' );
        $pageEntity->setTitle( $options['title'] );
        $pageEntity->setDescription( $options['description'] );
        $pageEntity->setText( $options['text'] );
        $pageEntity->setPublished( $options['published'] );

        $category   = $this->pageCategoryRepository->findByTaxonCode( $options['category_code'] );
        $pageEntity->addCategory( $category );
        
        return $pageEntity;
    }
    
    public function createTranslation( $entity, $localeCode, $options = [] )
    {
        $entity->setTranslatableLocale( $localeCode );
        $entity->setTitle( $options['title'] );
        $entity->setDescription( $options['description'] );
        $entity->setText( $options['text'] );
        
        return $entity;
    }
    
    protected function configureOptions( OptionsResolver $resolver ): void
    {
        $resolver
            ->setDefault( 'title', null )
            ->setAllowedTypes( 'title', ['string'] )
            
            ->setDefault( 'description', null )
            ->setAllowedTypes( 'description', ['string'] )
            
            ->setDefault( 'text', null )
            ->setAllowedTypes( 'text', ['string'] )
            
            ->setDefault( 'published', true )
            ->setAllowedTypes( 'published', ['bool'] )
            
            ->setDefault( 'locale', 'en_US' )
            ->setAllowedTypes( 'locale', ['string'] )
            
            ->setDefault( 'category_code', null )
            ->setAllowedTypes( 'category_code', ['string'] )
            
            ->setDefault( 'translations', [] )
            ->setAllowedTypes( 'translations', ['array'] )
        ;
    }
}
