<?php namespace VS\ApplicationInstalatorBundle\Command;

use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Symfony\Component\Console\Input\ArrayInput;
use Webmozart\Assert\Assert;

use VS\UsersBundle\Model\UserInterface;
use VS\UsersBundle\Repository\UsersRepositoryInterface;
use VS\ApplicationBundle\Component\Slug;

final class CreateApplicationCommand extends AbstractInstallCommand
{
    protected static $defaultName = 'vankosoft:application:create';
    
    protected function configure() : void
    {
        $this
            ->setDescription( 'VankoSoft Application Create Command.' )
            ->setHelp(<<<EOT
The <info>%command.name%</info> command allows user to create a VankoSoft Application.
EOT
            )
            ->addOption(
                'setup-kernel',
                null,
                InputOption::VALUE_OPTIONAL,
                'Whether to setup the AdminPanelKernel class.',
                false
            )
        ;
    }
    
    protected function execute( InputInterface $input, OutputInterface $output ) : int
    {
        $setupKernelOption  = $input->getOption( 'setup-kernel' );
        $setupKernel        = ( $setupKernelOption !== false );
        
        $this->setupApplication( $input, $output, $setupKernel );
        
        return 0;
    }
    
    protected function setupApplication( InputInterface $input, OutputInterface $output, $setupKernel = false ) : void
    {
        /** @var QuestionHelper $questionHelper */
        $questionHelper     = $this->getHelper( 'question' );
        
        $questionName       = $this->createApplicationNameQuestion();
        $applicationName    = $questionHelper->ask( $input, $output, $questionName );
        $questionUrl        = $this->createApplicationUrlQuestion();
        $applicationUrl     = $questionHelper->ask( $input, $output, $questionUrl );
        $applicationSlug    = Slug::generate( $applicationName );
        $applicationCreated = date( 'Y-m-d H:i:s' );
        
        $appSetup           = $this->getContainer()->get( 'vs_application_instalator.setup_application' );
        $outputStyle        = new SymfonyStyle( $input, $output );
        
        // Create Directories
        $outputStyle->writeln( 'Create Application Directories.' );
        $appSetup->setupApplication( $applicationName, $setupKernel );
        $outputStyle->writeln( '<info>Application Directories successfully created.</info>' );
        
        // Add Database Records
        $outputStyle->writeln( 'Create Application Database Records.' );
        // bin/console doctrine:query:sql "INSERT INTO VSAPP_Applications(title) VALUES('Test Application')"
        $command    = $this->getApplication()->find( 'doctrine:query:sql' );
        $returnCode = $command->run(
            new ArrayInput( ['sql' =>"INSERT INTO VSAPP_Applications(enabled, code, title, hostname, created_at) VALUES(1, '{$applicationSlug}', '{$applicationName}', '{$applicationUrl}', '{$applicationCreated}')"] ),
            $output
        );
        $outputStyle->writeln( '<info>Application Database Records successfully created.</info>' );
        
        $outputStyle->newLine();
    }
    
    private function createApplicationNameQuestion() : Question
    {
        return ( new Question( 'Application Name: ' ) )
            ->setValidator(
                function ( $value ): string {
                    /** @var ConstraintViolationListInterface $errors */
                    $errors = $this->getContainer()->get( 'validator' )->validate( (string) $value, [new Length([
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
    
    private function createApplicationUrlQuestion() : Question
    {
        return ( new Question( 'Application Url: ' ) )
            ->setValidator(
                function ( $value ): string {
                    /** @var ConstraintViolationListInterface $errors */
                    $errors = $this->getContainer()->get( 'validator' )->validate( (string) $value, [new Length([
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
