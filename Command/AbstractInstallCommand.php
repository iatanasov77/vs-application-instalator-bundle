<?php namespace Vankosoft\ApplicationInstalatorBundle\Command;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\NullOutput;
use Symfony\Component\Console\Output\OutputInterface;

use Vankosoft\ApplicationBundle\Command\ContainerAwareCommand;
use Vankosoft\ApplicationInstalatorBundle\Installer\Executor\CommandExecutor;

abstract class AbstractInstallCommand extends ContainerAwareCommand
{

    /** @var CommandExecutor */
    protected $commandExecutor;
    
    protected function initialize( InputInterface $input, OutputInterface $output )
    {
        $application    = $this->getApplication();
        $application->setCatchExceptions( false );
        
        $this->commandExecutor = new CommandExecutor( $input, $output, $application );
    }
    
    protected function getEnvironment(): string
    {
        return (string) $this->getParameter( 'kernel.environment' );
    }
    
    protected function isDebug(): bool
    {
        return (bool) $this->getParameter( 'kernel.debug' );
    }
    
    protected function renderTable( array $headers, array $rows, OutputInterface $output ): void
    {
        $table  = new Table( $output );
        
        $table
            ->setHeaders( $headers )
            ->setRows( $rows )
            ->render()
        ;
    }
    
    protected function createProgressBar( OutputInterface $output, int $length = 10 ): ProgressBar
    {
        $progress   = new ProgressBar( $output );
        $progress->setBarCharacter( '<info>░</info>' );
        $progress->setEmptyBarCharacter( ' ' );
        $progress->setProgressCharacter( '<comment>░</comment>' );
        
        $progress->start( $length );
        
        return $progress;
    }
    
    protected function runCommands( array $commands, OutputInterface $output, bool $displayProgress = true ): void
    {
        $progress   = $this->createProgressBar( $displayProgress ? $output : new NullOutput(), count( $commands ) );
        
        foreach ( $commands as $key => $value ) {
            if ( is_string( $key ) ) {
                $command    = $key;
                $parameters = $value;
            } else {
                $command    = $value;
                $parameters = [];
            }
            
            $this->commandExecutor->runCommand( $command, $parameters );
            
            // PDO does not always close the connection after Doctrine commands.
            // See https://github.com/symfony/symfony/issues/11750.
            /** @var EntityManagerInterface $entityManager */
            $entityManager  = $this->get( 'doctrine' )->getManager();
            $entityManager->getConnection()->close();
            
            $progress->advance();
        }
        
        $progress->finish();
    }
    
    protected function ensureDirectoryExistsAndIsWritable( string $directory, OutputInterface $output ): void
    {
        $checker    = $this->get( 'vs_app.installer.checker.command_directory' );
        $checker->setCommandName( $this->getName() );
        
        $checker->ensureDirectoryExists( $directory, $output );
        $checker->ensureDirectoryIsWritable( $directory, $output );
    }
}
