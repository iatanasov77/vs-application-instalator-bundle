<?php namespace Vankosoft\ApplicationInstalatorBundle\DataFixtures\Factory;

use Symfony\Component\OptionsResolver\OptionsResolver;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Sylius\Resource\Doctrine\Persistence\RepositoryInterface;

use Vankosoft\ApplicationBundle\Model\Interfaces\WidgetInterface;
use Vankosoft\ApplicationBundle\Component\SlugGenerator;

class WidgetsExampleFactory extends AbstractExampleFactory implements ExampleFactoryInterface
{
    /** @var RepositoryInterface */
    private $widgetsGroupsRepository;
    
    /** @var FactoryInterface */
    private $widgetsFactory;
    
    /** @var OptionsResolver */
    private $optionsResolver;
    
    /** @var SlugGenerator */
    private $slugGenerator;
    
    /** @var RepositoryInterface */
    private $userRolesRepository;
    
    public function __construct(
        RepositoryInterface $widgetsGroupsRepository,
        FactoryInterface $widgetsFactory,
        SlugGenerator $slugGenerator,
        RepositoryInterface $userRolesRepository
    ) {
        $this->widgetsGroupsRepository  = $widgetsGroupsRepository;
        $this->widgetsFactory           = $widgetsFactory;
        $this->slugGenerator            = $slugGenerator;
        $this->userRolesRepository      = $userRolesRepository;
        
        $this->optionsResolver          = new OptionsResolver();
        $this->configureOptions( $this->optionsResolver );
    }
    
    public function create( array $options = [] ): WidgetInterface
    {
        $options        = $this->optionsResolver->resolve( $options );
        
        $widgetEntity   = $this->widgetsFactory->createNew();
        $widgetEntity->setTranslatableLocale( $options['locale'] );
        $widgetEntity->setFallbackLocale( 'en_US' );
        
        $slug           = $this->slugGenerator->generate( $options['name'] );
        $widgetEntity->setCode( $slug );
        
        $widgetEntity->setName( $options['name'] );
        $widgetEntity->setDescription( $options['description'] );
        $widgetEntity->setActive( $options['active'] );
        
        $group          = $this->widgetsGroupsRepository->findByTaxonCode( $options['group_code'] );
        $widgetEntity->setGroup( $group );
        
        $widgetEntity->setAllowAnonymous( $options['allowAnonymous'] );
        
        if ( isset( $options['allowedRoles'] ) && null !== $options['allowedRoles'] ) {
            $this->addWidgetAllowedRoles( $widgetEntity, $options['allowedRoles'] );
        }
        
        return $widgetEntity;
    }
    
    protected function configureOptions( OptionsResolver $resolver ): void
    {
        $resolver
            ->setDefault( 'group_code', null )
            ->setAllowedTypes( 'group_code', ['string'] )
            
            ->setDefault( 'locale', 'en_US' )
            ->setAllowedTypes( 'locale', ['string'] )
        
            ->setDefault( 'name', null )
            ->setAllowedTypes( 'name', ['string'] )
            
            ->setDefault( 'description', null )
            ->setAllowedTypes( 'description', ['string'] )
            
            ->setDefault( 'active', true )
            ->setAllowedTypes( 'active', ['bool'] )
            
            ->setDefault( 'allowAnonymous', false )
            ->setAllowedTypes( 'allowAnonymous', ['bool'] )
            
            ->setDefault( 'allowedRoles', null )
        ;
    }
    
    private function addWidgetAllowedRoles( &$entity, array $allowedRoles )
    {
        foreach( $allowedRoles as $ar ) {
            $role   = $this->userRolesRepository->findOneBy( ['role' => $ar['role']] );
            
            if ( $role ) {
                $entity->addAllowedRole( $role );
            }
        }
    }
}