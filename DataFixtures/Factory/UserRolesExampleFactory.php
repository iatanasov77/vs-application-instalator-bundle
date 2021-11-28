<?php namespace VS\ApplicationInstalatorBundle\DataFixtures\Factory;

use Symfony\Component\OptionsResolver\OptionsResolver;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;

use VS\ApplicationBundle\Component\Slug;
use VS\ApplicationBundle\Repository\TaxonRepository;
use VS\UsersBundle\Model\UserRoleInterface;
use VS\UsersBundle\Repository\UserRolesRepository;

class UserRolesExampleFactory extends AbstractExampleFactory implements ExampleFactoryInterface
{
    /** @var RepositoryInterface */
    private $taxonomyRepository;
    
    /** @var FactoryInterface */
    private $taxonFactory;
    
    /** @var TaxonRepository */
    private $taxonRepository;
    
    /** @var FactoryInterface */
    private $userRolesFactory;
    
    /** @var UserRolesRepository */
    private $userRolesRepository;
    
    /** @var OptionsResolver */
    private $optionsResolver;
    
    public function __construct(
        RepositoryInterface $taxonomyRepository,
        FactoryInterface $taxonFactory,
        TaxonRepository $taxonRepository,
        FactoryInterface $userRolesFactory,
        UserRolesRepository $userRolesRepository
    ) {
        $this->taxonomyRepository   = $taxonomyRepository;
        $this->taxonFactory         = $taxonFactory;
        $this->taxonRepository      = $taxonRepository;
        $this->userRolesFactory     = $userRolesFactory;
        $this->userRolesRepository  = $userRolesRepository;
        
        $this->optionsResolver      = new OptionsResolver();
        $this->configureOptions( $this->optionsResolver );
    }
    
    public function create( array $options = [] ): UserRoleInterface
    {
        $options                    = $this->optionsResolver->resolve( $options );
        
        $taxonomyRootTaxonEntity    = $this->taxonomyRepository->findByCode( $options['taxonomy_code'] )->getRootTaxon();
        $taxonParent                = $this->taxonRepository->findOneBy( ['code' => $options['parent']] );
        
        $userRoleEntity             = $this->userRolesFactory->createNew();
        
        $taxonEntity                = $this->taxonFactory->createNew();
        $slug                       = Slug::generate( $options['title'] );
        
        $taxonEntity->setCode( $slug );
        $taxonEntity->setCurrentLocale( $options['locale'] );
        $taxonEntity->getTranslation()->setName( $options['title'] );
        $taxonEntity->getTranslation()->setDescription( $options['description'] );
        $taxonEntity->getTranslation()->setSlug( $slug );
        $taxonEntity->getTranslation()->setTranslatable( $taxonEntity );
        
        $taxonEntity->setParent( $taxonParent ?: $taxonomyRootTaxonEntity );
        $userRoleEntity->setParent( $this->userRolesRepository->findByTaxonCode( $options['parent'] ) );
        $userRoleEntity->setTaxon( $taxonEntity );
        $userRoleEntity->setRole( $options['role'] );
        
        return $userRoleEntity;
    }
    
    protected function configureOptions( OptionsResolver $resolver ): void
    {
        $resolver
            ->setDefault( 'title', null )
            ->setAllowedTypes( 'title', ['string'] )
            
            ->setDefault( 'description', null )
            ->setAllowedTypes( 'description', ['string'] )
            
            ->setDefault( 'taxonomy_code', null )
            ->setAllowedTypes( 'taxonomy_code', ['string'] )
            
            ->setDefault( 'locale', 'en_US' )
            ->setAllowedTypes( 'locale', ['string'] )
            
            ->setDefault( 'role', null )
            ->setAllowedTypes( 'role', ['string'] )
            
            ->setDefault( 'parent', null )
            ->setAllowedTypes( 'parent', ['string', 'null'] )
        ;
    }
    
}
