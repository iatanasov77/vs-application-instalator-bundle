<?php namespace Vankosoft\ApplicationBundle\EventListener;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;

use Vankosoft\ApplicationBundle\Component\Exception\TestException;
use Vankosoft\ApplicationBundle\Component\Context\ApplicationNotFoundException;

class ExceptionListener
{
    public function onKernelException( ExceptionEvent $event )
    {
        $exception  = $event->getThrowable();
        $response   = new Response();
        
        switch ( true ) {
            case  ( $exception instanceof TestException ):
                $message = sprintf(
                    'Getting following error: %s with code: %s',
                    $exception->getMessage(),
                    $exception->getCode()
                );
                
                // Customize your response object to display the exception details
                $response->setContent( $message );
                
                // sends the modified response object to the event
                $event->setResponse( $response );
                
                break;
            case ( $exception instanceof ApplicationNotFoundException ):
                $message = sprintf(
                    'VankoSoft Application Error: %s with code: %s',
                    $exception->getMessage(),
                    $exception->getCode()
                );
                
                // Customize your response object to display the exception details
                $response->setContent( $message );
                
                // sends the modified response object to the event
                $event->setResponse( $response );
                
                break;
                
            case ( $exception instanceof HttpExceptionInterface ):
                $response->setStatusCode( $exception->getStatusCode() );
                $response->headers->replace( $exception->getHeaders() );
                
                // sends the modified response object to the event
                $event->setResponse( $response );
                
                break;
        }
    }
}
