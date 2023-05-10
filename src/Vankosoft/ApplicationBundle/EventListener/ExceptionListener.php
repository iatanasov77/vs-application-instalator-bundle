<?php namespace Vankosoft\ApplicationBundle\EventListener;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Twig\Environment;

use Vankosoft\ApplicationBundle\Component\Exception\TestException;
use Vankosoft\ApplicationBundle\Component\Context\ApplicationNotFoundException;

/**
 * MANUAL: https://symfony.com/doc/current/event_dispatcher.html
 */
class ExceptionListener
{
    /**
     * @var Environment $twig
     */
    private $twig;
    
    public function __construct( Environment $twig )
    {
        $this->twig = $twig;
    }
    
    public function onKernelException( ExceptionEvent $event )
    {
        $exception  = $event->getThrowable();
        
        switch ( true ) {
            case ( $exception instanceof ApplicationNotFoundException ):
                $this->showFixApplicationForm( $event );
                
                break;
        }
    }
    
    private function showFixApplicationForm( &$event )
    {
        $response       = new Response();
        $pageContent    = $this->twig->render( '@VSApplication/Pages/Exception/application-not-found.html.twig',
            [
                
            ]
        );
        
        // Customize your response object to display the exception details
        $response->setContent( $pageContent  );
        
        // sends the modified response object to the event
        $event->setResponse( $response );
    }
}
