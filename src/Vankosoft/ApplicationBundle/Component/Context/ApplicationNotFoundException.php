<?php namespace Vankosoft\ApplicationBundle\Component\Context;

class ApplicationNotFoundException extends \RuntimeException
{
    public function __construct( $messageOrPreviousException = null, ?\Throwable $previousException = null )
    {
        $message = 'Application could not be found!';

        if ( $messageOrPreviousException instanceof \Throwable ) {
            @trigger_error( 'Passing previous exception as the first argument is deprecated since 1.2 and will be prohibited since 2.0.', \E_USER_DEPRECATED );
            $previousException  = $messageOrPreviousException;
        }

        if ( is_string( $messageOrPreviousException ) ) {
            $message    = $messageOrPreviousException;
        }

        parent::__construct( $message, 0, $previousException );
    }
}
