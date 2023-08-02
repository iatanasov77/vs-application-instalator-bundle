<?php namespace Vankosoft\ApplicationInstalatorBundle\DataFixtures\Factory;

use Symfony\Component\OptionsResolver\OptionsResolver;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;

use Vankosoft\ApplicationBundle\Component\SlugGenerator;
use Vankosoft\ApplicationBundle\Repository\TaxonRepository;
use Vankosoft\UsersBundle\Model\UserRoleInterface;
use Vankosoft\UsersBundle\Repository\UserRolesRepository;

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
    
    /** @var SlugGenerator */
    private $slugGenerator;
    
    public function __construct(
        RepositoryInterface $taxonomyRepository,
        FactoryInterface $taxonFactory,
        TaxonRepository $taxonRepository,
        FactoryInterface $userRolesFactory,
        UserRolesRepository $userRolesRepository,
        SlugGenerator $slugGenerator
    ) {
        $this->taxonomyRepository   = $taxonomyRepository;
        $this->taxonFactory         = $taxonFactory;
        $this->taxonRepository      = $taxonRepository;
        $this->userRolesFactory     = $userRolesFactory;
        $this->userRolesRepository  = $userRolesRepository;
        $this->slugGenerator        = $slugGenerator;
        
        $this->optionsResolver      = new OptionsResolver();
        $this->configureOptions( $this->optionsResolver );
    }
    
    public function create( array $options = [] ): UserRoleInterface
    {
        $options                    = $this->optionsResolver->resolve( $options );
        
        $userRoleEntity             = $this->userRolesFactory->createNew();
        $taxonEntity                = $this->taxonFactory->createNew();
        $slug                       = $this->slugGenerator->generate( $options['title'] );
        
        $taxonEntity->setCurrentLocale( $options['locale'] );
        $taxonEntity->setCode( $slug );
        $taxonEntity->getTranslation()->setName( $options['title'] );
        $taxonEntity->getTranslation()->setDescription( $options['description'] );
        $taxonEntity->getTranslation()->setSlug( $slug );
        $taxonEntity->getTranslation()->setTranslatable( $taxonEntity );
        
        $taxonomyRootTaxonEntity    = $this->taxonomyRepository->findByCode( $options['taxonomy_code'] )->getRootTaxon();
        $taxonParent                = $this->taxonRepository->findOneBy( ['code' => $options['parent']] );
        //echo "\n\nParent Code: " . $options['parent'] . "\nParent ID: " . ( $taxonParent ? $taxonParent->getId() : "NULL" );
        //echo "\nCount Taxons: " . $this->taxonRepository->count(['root' => 2]) . "\n\n";
        
        $taxonEntity->setParent( $taxonParent ?: $taxonomyRootTaxonEntity );
        $userRoleEntity->setParent( $this->userRolesRepository->findByTaxonCode( $options['parent'] ) );
        $userRoleEntity->setTaxon( $taxonEntity );
        $userRoleEntity->setRole( $options['role'] );
        $userRoleEntity->setDescription( $options['description'] );
        
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
