<?php namespace Vankosoft\ApplicationBundle\Controller\Traits;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\StreamOutput;
use Symfony\Component\Console\Output\BufferedOutput;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Process\Process;

/**
 * Call a Command from a Controller and Get Buffered or Streamed Response
 * 
 * ==================================================
 * To Showing Colorized Command Output require:
 *  $ composer require sensiolabs/ansi-to-html
 *  See Symfony Docs Below
 * ==================================================
 * 
 * @link https://symfony.com/doc/current/console/command_in_controller.html
 * @link https://gist.github.com/ayalon/7690311
 */
trait ConsoleCommandTrait
{
    public function bufferedCommandResponse( array $command ): Response
    {
        @ini_set( 'zlib.output_compression', 0 );
        @ini_set( 'implicit_flush', 1 );
        @ob_end_clean();
        set_time_limit( 0 );
        
        $application = new Application( $this->getKernel() );
        $application->setAutoExit( false );
        
        $input = new ArrayInput( $command );
        $input->setInteractive( false );
        
        // You can use NullOutput() if you don't need the output
        $output = new BufferedOutput();
        
        $application->run( $input, $output );
        
        // return the output, don't use if you used NullOutput()
        $content = $output->fetch();
        
        // return new Response(""), if you used NullOutput()
        return new Response( $content );
    }
    
    public function streamedCommandResponse( array $command ): StreamedResponse
    {
        @ini_set( 'zlib.output_compression', 0 );
        @ini_set( 'implicit_flush', 1 );
        @ob_end_clean();
        set_time_limit( 0 );
        
        $application = new Application( $this->getKernel() );
        $response = new StreamedResponse( function() use ( $application, $command ) {
            $application->setAutoExit( false );
            
            $input = new ArrayInput( $command );
            $input->setInteractive( false );
            
            $output = new StreamOutput( fopen( 'php://stdout', 'w' ) );
            
            $application->run( $input, $output );
        });
            
        return $response;
    }
    
    public function streamedProcessResponse( $callback ): StreamedResponse
    {
        return new StreamedResponse( $callback );
    }
    
    abstract protected function getKernel(): KernelInterface;
}
