<?php namespace VS\ApplicationInstalatorBundle\DataFixtures\Factory;

use Symfony\Component\OptionsResolver\OptionsResolver;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;

use VS\CmsBundle\Model\PageInterface;
use VS\ApplicationBundle\Component\Slug;

class PagesExampleFactory extends AbstractExampleFactory implements ExampleFactoryInterface
{
    /** @var FactoryInterface */
    private $pagesFactory;
    
    /** @var RepositoryInterface */
    private $pageCategoryRepository;
    
    /** @var OptionsResolver */
    private $optionsResolver;
    
    public function __construct(
        RepositoryInterface $pageCategoryRepository,
        FactoryInterface $pagesFactory
    ) {
            $this->pageCategoryRepository   = $pageCategoryRepository;
            $this->pagesFactory             = $pagesFactory;
            
            $this->optionsResolver          = new OptionsResolver();
            $this->configureOptions( $this->optionsResolver );
    }
    
    public function create( array $options = [] ): PageInterface
    {
        $options    = $this->optionsResolver->resolve( $options );

        $pageEntity = $this->pagesFactory->createNew();
        $slug       = Slug::generate( $options['title'] );
        
        $pageEntity->setSlug( $slug );
        $pageEntity->setTranslatableLocale( $options['locale'] );
        $pageEntity->setTitle( $options['title'] );
        $pageEntity->setText( $options['text'] );
        $pageEntity->setPublished( $options['published'] );

        $category   = $this->pageCategoryRepository->findByTaxonCode( $options['category_code'] );
        $pageEntity->addCategory( $category );
        
        return $pageEntity;
    }
    
    protected function configureOptions( OptionsResolver $resolver ): void
    {
        $resolver
            ->setDefault( 'title', null )
            ->setAllowedTypes( 'title', ['string'] )
            
            ->setDefault( 'text', null )
            ->setAllowedTypes( 'text', ['string'] )
            
            ->setDefault( 'published', true )
            ->setAllowedTypes( 'published', ['bool'] )
            
            ->setDefault( 'locale', 'en_US' )
            ->setAllowedTypes( 'locale', ['string'] )
            
            ->setDefault( 'category_code', null )
            ->setAllowedTypes( 'category_code', ['string'] )
        ;
    }
}
