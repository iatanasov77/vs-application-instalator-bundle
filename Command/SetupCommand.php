<?php namespace VS\ApplicationInstalatorBundle\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Symfony\Component\Console\Input\ArrayInput;

use VS\ApplicationBundle\Component\Slug;

final class SetupCommand extends AbstractInstallCommand
{
    protected static $defaultName = 'vankosoft:install:setup';
    
    protected function configure(): void
    {
        $this
            ->setDescription( 'VankoSoft Application configuration setup.' )
            ->setHelp(<<<EOT
The <info>%command.name%</info> command allows user to configure basic VankoSoft Application data.
EOT
            )
        ;
    }
    
    protected function execute( InputInterface $input, OutputInterface $output ): int
    {
        $locale = $this->getContainer()->get( 'vs_app.setup.locale' )->setup( $input, $output, $this->getHelper( 'question' ) );
        $this->commandExecutor->runCommand( 'vankosoft:application:setup', [], $output );
        
        // Setup Super Admin Panel
        $this->setupSuperAdminPanelApplication( $input, $output, $locale->getCode() );
        
        // Setup an Application
        $this->setupApplication( $input, $output, $locale->getCode() );
        
        // Setup Super Admin User
        $this->setupSuperAdminUser( $input, $output, $locale->getCode() );
        
        // Setup Applications Admin User
        $this->setupApplicationsAdminUser( $input, $output, $locale->getCode() );
        
        return Command::SUCCESS;
    }
    
    private function setupSuperAdminPanelApplication( InputInterface $input, OutputInterface $output, string $localeCode ): void
    {
        $applicationName    = 'Admin Panel';
        $applicationSlug    = Slug::generate( $applicationName );
        
        $outputStyle    = new SymfonyStyle( $input, $output );
        $outputStyle->writeln( 'Create SuperAdminPanel Application.' );
        
        /** @var QuestionHelper $questionHelper */
        $questionHelper     = $this->getHelper( 'question' );
        $questionUrl        = $this->createAdminPanelUrlQuestion();
        $applicationUrl     = $questionHelper->ask( $input, $output, $questionUrl );
        $applicationCreated = date( 'Y-m-d H:i:s' );
        
        $command    = $this->getApplication()->find( 'doctrine:query:sql' );
        $returnCode = $command->run(
            new ArrayInput( ['sql' =>"INSERT INTO VSAPP_Applications(enabled, code, title, hostname, created_at) VALUES(1, '{$applicationSlug}', '{$applicationName}', '{$applicationUrl}', '{$applicationCreated}')"] ),
            $output
        );
        
        $outputStyle->writeln( '<info>SuperAdminPanel Application created successfully.</info>' );
        $outputStyle->newLine();
    }
    
    private function setupApplication( InputInterface $input, OutputInterface $output, string $localeCode )
    {
        $outputStyle    = new SymfonyStyle( $input, $output );
        
        $this->commandExecutor->runCommand( 'vankosoft:application:create', ['--setup-kernel' => true, '--locale' => $localeCode], $output );
        
        $outputStyle->newLine();
        $outputStyle->writeln( '<info>Default Application created successfully.</info>' );
        $outputStyle->newLine();
    }
    
    private function setupSuperAdminUser( InputInterface $input, OutputInterface $output, string $localeCode ) : void
    {
        $outputStyle    = new SymfonyStyle( $input, $output );
        $outputStyle->writeln( 'Create SuperAdmin account.' );
        
        $parameters     = [
            '--application' => 'Super Admin',
            '--roles'       => ['ROLE_SUPER_ADMIN'],
            '--locale'      => $localeCode
        ];
        $this->commandExecutor->runCommand( 'vankosoft:application:create-user', $parameters, $output );
        
        $outputStyle->writeln( '<info>SuperAdmin account successfully created.</info>' );
        $outputStyle->newLine();
    }
    
    private function setupApplicationsAdminUser( InputInterface $input, OutputInterface $output, string $localeCode ) : void
    {
        $outputStyle    = new SymfonyStyle( $input, $output );
        $outputStyle->writeln( 'Create Applications Admin account.' );
        
        $parameters     = [
            '--application' => 'Applications Admin',
            '--roles'       => ['ROLE_APPLICATION_ADMIN'],
            '--locale'      => $localeCode
        ];
        $this->commandExecutor->runCommand( 'vankosoft:application:create-user', $parameters, $output );
        
        $outputStyle->writeln( '<info>Applications Admin account successfully created.</info>' );
        $outputStyle->newLine();
    }
    
    private function createAdminPanelUrlQuestion(): Question
    {
        return ( new Question( 'AdminPanel Url: ' ) )
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
