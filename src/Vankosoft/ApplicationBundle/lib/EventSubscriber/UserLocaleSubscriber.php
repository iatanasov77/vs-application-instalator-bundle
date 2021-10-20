<?php namespace VS\ApplicationBundle\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;
use Symfony\Component\Security\Http\SecurityEvents;

/**
 * Stores the locale of the user in the session after the
 * login. This can be used by the LocaleSubscriber afterwards.
 */
class UserLocaleSubscriber implements EventSubscriberInterface
{
    private $session;
    
    //public function __construct( SessionInterface $session )
    public function __construct()
    {
       // $this->session = $session;
    }
    
    public function onInteractiveLogin( InteractiveLoginEvent $event )
    {
        $user = $event->getAuthenticationToken()->getUser();
        $session    = $event->getRequest()->getSession();
        if ( null !== $user->getPreferedLocale() ) {
            //$this->session->set( '_locale', $user->getPreferedLocale() );
            $session->set( '_locale', $user->getPreferedLocale() );
            
        }
    }
    
    public static function getSubscribedEvents()
    {
        return [
            SecurityEvents::INTERACTIVE_LOGIN => 'onInteractiveLogin',
        ];
    }
}
