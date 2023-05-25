<?php namespace Vankosoft\ApplicationBundle\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\EventDispatcher\GenericEvent;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

use Doctrine\ORM\EntityManager;
use Sylius\Component\Resource\Metadata\MetadataInterface;
use Sylius\Component\Resource\Metadata\RegistryInterface;
use Sylius\Component\Resource\Factory\Factory;

use Vankosoft\UsersBundle\Model\UserInterface;

class ApplicationResourceListener implements EventSubscriberInterface
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
    
    public function postCreate( GenericEvent $event )
    {
        //if ( ! is_object( $this->user ) || ! $this->user->hasRole( 'ROLE_ADMIN' ) ) {
        if ( $this->user ) {
            $this->addUserActivity( 'A Resource Created' );
        }
    }
    
    public function postUpdate( GenericEvent $event )
    {
        //if ( ! is_object( $this->user ) || ! $this->user->hasRole( 'ROLE_ADMIN' ) ) {
        if ( $this->user ) {
            $this->addUserActivity( 'A Resource Updated' );
        }
    }
    
    public static function getSubscribedEvents(): array
    {
        $events =[];
        
        /** @var iterable<MetadataInterface> $resources */
        $resources = $this->registry->getAll();
        $resources = is_array( $resources ) ? $resources : iterator_to_array( $resources );
        ksort( $resources );
        
        foreach ( $resources as $resource ) {
            $events[]   = $resource->getAlias() . '.post_create';
            $events[]   = $resource->getAlias() . '.post_update';
            $events[]   = $resource->getAlias() . '.post_delete';
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