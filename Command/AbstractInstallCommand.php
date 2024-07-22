<?php namespace Vankosoft\ApplicationInstalatorBundle\Command;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\NullOutput;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Console\Question\ChoiceQuestion;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\ConstraintViolationListInterface;

use Vankosoft\ApplicationBundle\Component\Application\Project;
use Vankosoft\ApplicationBundle\Command\ContainerAwareCommand;
use Vankosoft\ApplicationInstalatorBundle\Installer\Executor\CommandExecutor;

abstract class AbstractInstallCommand extends ContainerAwareCommand
{
    const APPLICATION_TYPE_STANDRD  = 'standard';
    const APPLICATION_TYPE_CATALOG  = 'catalog';
    const APPLICATION_TYPE_EXTENDED = 'extended';
    const APPLICATION_TYPE_API      = 'api';
    
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
    
    protected function getProjectType(): ?string
    {
        return $this->getParameter( 'vs_application.project_type' );
    }
    
    protected function isBaseProject(): bool
    {
        return $this->getProjectType() == Project::PROJECT_TYPE_APPLICATION;
    }
    
    protected function isCatalogProject(): bool
    {
        // \array_key_exists( 'VSPaymentBundle', $this->getParameter( 'kernel.bundles' ) );
        return $this->getProjectType() == Project::PROJECT_TYPE_CATALOG;
    }
    
    protected function isExtendedProject(): bool
    {
        // \array_key_exists( 'VSPaymentBundle', $this->getParameter( 'kernel.bundles' ) );
        return $this->getProjectType() == Project::PROJECT_TYPE_EXTENDED;
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
    
    protected function createApplicationTypeQuestion( InputInterface $input, OutputInterface $output ): ?string
    {
        $applicationTypes   = [
            self::APPLICATION_TYPE_STANDRD,
            self::APPLICATION_TYPE_CATALOG,
            self::APPLICATION_TYPE_EXTENDED,
            self::APPLICATION_TYPE_API,
        ];
        
        $default            = $applicationTypes[2];
        $questionMessage    = sprintf( 'Please select an application type to be created (defaults to %s): ', $default );
        
        $choiceQuestion     = new ChoiceQuestion(
            $questionMessage,
            // choices can also be PHP objects that implement __toString() method
            $applicationTypes,
            $default
        );
        
        $applicationType    = $this->getHelper( 'question' )->ask(
            $input,
            $output,
            $choiceQuestion
        );
        
        return $applicationType;
    }
    
    protected function createApplicationNameQuestion(): Question
    {
        return ( new Question( 'Application Name: ' ) )
            ->setValidator(
                function ( $value ): string {
                    /** @var ConstraintViolationListInterface $errors */
                    $errors = $this->get( 'validator' )->validate( (string) $value, [new Length([
                        'min' => 3,
                        'max' => 50,
                        'minMessage' => 'Your application name must be at least {{ limit }} characters long',
                        'maxMessage' => 'Your application name cannot be longer than {{ limit }} characters',
                    ])]);
                    foreach ( $errors as $error ) {
                        throw new \DomainException( $error->getMessage() );
                    }
                    
                    return $value;
                }
            )
            ->setMaxAttempts( 3 )
        ;
    }
    
    protected function createApplicationUrlQuestion(): Question
    {
        return ( new Question( 'Application Domain: ' ) )
            ->setValidator(
                function ( $value ): string {
                    /** @var ConstraintViolationListInterface $errors */
                    $errors = $this->get( 'validator' )->validate( (string) $value, [new Length([
                        'min' => 6,
                        'max' => 256,
                        'minMessage' => 'Your application url must be at least {{ limit }} characters long',
                        'maxMessage' => 'Your application url cannot be longer than {{ limit }} characters',
                    ])]);
                    foreach ( $errors as $error ) {
                        throw new \DomainException( $error->getMessage() );
                    }
                    
                    return $value;
                }
            )
            ->setMaxAttempts( 3 )
        ;
    }
}
