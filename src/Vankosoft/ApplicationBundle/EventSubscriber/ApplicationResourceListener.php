<?php namespace Vankosoft\ApplicationBundle\EventSubscriber;

use Symfony\Component\EventDispatcher\GenericEvent;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

use Sylius\Bundle\ResourceBundle\EventListener\AbstractDoctrineSubscriber;
use Sylius\Component\Resource\Metadata\MetadataInterface;
use Sylius\Component\Resource\Metadata\RegistryInterface;
use Sylius\Component\Resource\Factory\Factory;
use Doctrine\ORM\EntityManager;

use Vankosoft\UsersBundle\Model\UserInterface;

class ApplicationResourceListener extends AbstractDoctrineSubscriber
{
    /** @var RegistryInterface */
    private $registry;
    
    /** @var Factory */
    private $userActivitiesFactory;
    
    /** @var EntityManager */
    private $entityManager;
    
    /** @var UserInterface|null */
    private $user;
    
    public function __construct(
        RegistryInterface $registry,
        Factory $userNotificationsFactory,
        EntityManager $entityManager,
        TokenStorageInterface $tokenStorage
    ) {
        $this->registry                 = $registry;
        $this->userActivitiesFactory    = $userNotificationsFactory;
        $this->entityManager            = $entityManager;
        
        $token          = $tokenStorage->getToken();
        if ( $token ) {
            $this->user         = $token->getUser();
        }
    }
    
    public function onCreate( GenericEvent $event )
    {
        //if ( ! is_object( $this->user ) || ! $this->user->hasRole( 'ROLE_ADMIN' ) ) {
        if ( $this->user ) {
            $this->addUserActivity( 'A Resource Created' );
        }
    }
    
    public function onUpdate( GenericEvent $event )
    {
        //if ( ! is_object( $this->user ) || ! $this->user->hasRole( 'ROLE_ADMIN' ) ) {
        if ( $this->user ) {
            $this->addUserActivity( 'A Resource Updated' );
        }
    }
    
    public function onDelete( GenericEvent $event )
    {
        //if ( ! is_object( $this->user ) || ! $this->user->hasRole( 'ROLE_ADMIN' ) ) {
        if ( $this->user ) {
            $this->addUserActivity( 'A Resource Deleted' );
        }
    }
    
    public function getSubscribedEvents(): array
    {
        $events =[];
        
        /** @var iterable<MetadataInterface> $resources */
        $resources = $this->registry->getAll();
        $resources = is_array( $resources ) ? $resources : iterator_to_array( $resources );
        ksort( $resources );
        
        foreach ( $resources as $resource ) {
            $events[$resource->getAlias() . '.post_create'] = 'onCreate';
            $events[$resource->getAlias() . '.post_update'] = 'onUpdate';
            $events[$resource->getAlias() . '.post_delete'] = 'onDelete';
        }
        
        return $events;
    }
    
    private function addUserActivity( string $activity )
    {
        $oActivity  = $this->userActivitiesFactory->createNew();
        $oActivity->setActivity( $activity );
        
        //$this->user->addActiviity( $oActivity );
        $oActivity->setUser( $this->user );
        
        $this->entityManager->persist( $oActivity );
        $this->entityManager->flush();
    }
}