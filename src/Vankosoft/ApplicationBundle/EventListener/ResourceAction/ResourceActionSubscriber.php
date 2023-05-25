<?php namespace Vankosoft\ApplicationBundle\EventListener\ResourceAction;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Sylius\Component\Resource\Factory\Factory;
use Doctrine\ORM\EntityManager;
use Vankosoft\UsersBundle\Model\UserInterface;

/**
 * MANUAL: https://q.agency/blog/custom-events-with-symfony5/
 */
final class ResourceActionSubscriber implements EventSubscriberInterface
{
    /** @var Factory */
    private $userActivitiesFactory;
    
    /** @var EntityManager */
    private $entityManager;
    
    /** @var UserInterface|null */
    private $user;
    
    public function __construct(
        Factory $userActivitiesFactory,
        EntityManager $entityManager,
        TokenStorageInterface $tokenStorage
    ) {
        $this->userActivitiesFactory    = $userActivitiesFactory;
        $this->entityManager            = $entityManager;
        
        $token          = $tokenStorage->getToken();
        if ( $token ) {
            $this->user         = $token->getUser();
        }
    }
    
    public static function getSubscribedEvents(): array
    {
        return [ResourceActionEvent::NAME => 'addUserActivity'];
    }
/*
    public static function getSubscribedEvents()
    {
        return [
            ResourceActionEvent::NAME => [
                //['sendNotification', 8],
                ['addUserActivity', 7],
            ],
        ];
    }
*/
    public function addUserActivity( ResourceActionEvent $event )
    {
        $this->_addUserActivity( 'Resource Action Triggered - ' . $event->getAction() );
    }
    
    private function _addUserActivity( string $activity )
    {
        $oActivity  = $this->userActivitiesFactory->createNew();
        $oActivity->setActivity( $activity );
        
        //$this->user->addActiviity( $oActivity );
        $oActivity->setUser( $this->user );
        
        $this->entityManager->persist( $oActivity );
        $this->entityManager->flush();
    }
}