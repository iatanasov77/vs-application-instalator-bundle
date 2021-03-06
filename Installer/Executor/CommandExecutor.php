<?php namespace Vankosoft\ApplicationInstalatorBundle\Installer\Executor;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\NullOutput;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Exception\RuntimeException;

final class CommandExecutor
{
    /** @var InputInterface */
    private $input;

    /** @var OutputInterface */
    private $output;

    /** @var Application */
    private $application;

    public function __construct( InputInterface $input, OutputInterface $output, Application $application )
    {
        $this->input        = $input;
        $this->output       = $output;
        $this->application  = $application;
    }

    public function getCommand( string $command ): Command
    {
        return $this->application->get( $command );
    }
    
    /**
     * @throws \Exception
     */
    public function runCommand( string $command, array $parameters = [], ?OutputInterface $output = null ): self
    {
        $parameters = array_merge(
            ['command' => $command],
            $this->getDefaultParameters(),
            $parameters
        );

        $this->application->setAutoExit( false );
        $exitCode   = $this->application->run( new ArrayInput( $parameters ), $output ?: new NullOutput() );

        if ( 1 === $exitCode ) {
            throw new RuntimeException( 'This command terminated with a permission error.' );
        }

        if ( 0 !== $exitCode ) {
            $this->application->setAutoExit( true );

            $errorMessage   = sprintf( 'The command terminated with an error code: %u.', $exitCode );
            $this->output->writeln( "<error>$errorMessage</error>" );

            throw new \Exception( $errorMessage, $exitCode );
        }

        return $this;
    }

    private function getDefaultParameters(): array
    {
        $defaultParameters = ['--no-debug' => true];

        if ( $this->input->hasOption( 'env' ) ) {
            $defaultParameters['--env'] = $this->input->hasOption( 'env' ) ? $this->input->getOption( 'env' ) : 'dev';
        }

        if ( $this->input->hasOption( 'no-interaction' ) && true === $this->input->getOption( 'no-interaction' ) ) {
            $defaultParameters['--no-interaction']  = true;
        }

        if ( $this->input->hasOption( 'verbose' ) && true === $this->input->getOption( 'verbose' ) ) {
            $defaultParameters['--verbose'] = true;
        }

        return $defaultParameters;
    }
}
