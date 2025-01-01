<?php namespace Vankosoft\ApplicationInstalatorBundle\Command;

use Symfony\Component\Console\Attribute\AsCommand;
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

#[AsCommand(
    name: 'vankosoft:install:setup-super-admin-application',
    description: 'VankoSoft Application SuperAdmin User setup.',
    hidden: false
)]
final class SetupSuperAdminApplicationCommand extends AbstractInstallCommand
{
    protected function configure(): void
    {
        $this
            ->setHelp(<<<EOT
The <info>%command.name%</info> command allows user to configure basic VankoSoft Application data.
EOT
            )
            ->addOption( 'default-locale', 'l', InputOption::VALUE_REQUIRED, 'Prefered User Locale.', 'en_US' )
        ;
    }
    
    protected function execute( InputInterface $input, OutputInterface $output ): int
    {
        $locale = $input->getOption( 'default-locale' );
        
        // Setup Super Admin Panel
        $this->setupSuperAdminPanelApplication( $input, $output, $locale );
        
        // Setup Super Admin User
        $this->setupSuperAdminUser( $input, $output, $locale );
        
        return Command::SUCCESS;
    }
    
    private function setupSuperAdminPanelApplication( InputInterface $input, OutputInterface $output, string $localeCode ): void
    {
        $outputStyle    = new SymfonyStyle( $input, $output );
        $appSetup       = $this->get( 'vs_application.installer.setup_application' );
        
        // Debug
//         $outputStyle->writeln( 'DEBUG APPLICATION VERSION:' );
//         $outputStyle->newLine();
//         $outputStyle->writeln( $appSetup->getApplicationVersion() );
//         $outputStyle->newLine();
//         $outputStyle->newLine();
//         die;
        
        // Add Database Records
        $outputStyle->writeln( 'Create SuperAdmin Application Database Records.' );
        $this->createApplicationDatabaseRecords( $input, $output, 'Admin Panel', $localeCode );
        $outputStyle->writeln( '<info>SuperAdmin Application Database Records successfully created.</info>' );
        $outputStyle->newLine();
        
        // Setup SuperAdmin Kernel
        $outputStyle->writeln( 'Create SuperAdmin Application Kernel.' );
        $appSetup->setupAdminPanelKernel();
        $outputStyle->writeln( '<info>SuperAdmin Application Kernel successfully created.</info>' );
        $outputStyle->newLine();
        
        // Setup SuperAdmin Default Locale
        $outputStyle->writeln( 'Setup SuperAdmin Application Default Locale.' );
        $appSetup->setupAdminPanelDefaultLocale( $localeCode );
        $outputStyle->writeln( '<info>SuperAdmin Application Default Locale successfully setuped.</info>' );
        $outputStyle->newLine();
        
        $outputStyle->newLine();
        $outputStyle->writeln( '<info>SuperAdminPanel Application created successfully.</info>' );
        $outputStyle->newLine();
    }
    
    private function createApplicationDatabaseRecords( InputInterface $input, OutputInterface $output, $applicationName, $localeCode )
    {
        $entityManager      = $this->get( 'doctrine' )->getManager();
        $applicationSlug    = $this->get( 'vs_application.slug_generator' )->generate( $applicationName );
        
        $outputStyle    = new SymfonyStyle( $input, $output );
        $outputStyle->writeln( 'Create SuperAdminPanel Application.' );
        
        /** @var QuestionHelper $questionHelper */
        $questionHelper     = $this->getHelper( 'question' );
        $questionUrl        = $this->createAdminPanelUrlQuestion();
        $applicationUrl     = $questionHelper->ask( $input, $output, $questionUrl );
        $applicationCreated = new \DateTime;
        
        $application        = $this->get( 'vs_application.factory.application' )->createNew();
        $application->setCode( $applicationSlug );
        $application->setTitle( $applicationName );
        $application->setHostname( $applicationUrl );
        $application->setCreatedAt( $applicationCreated );
        
        $entityManager->persist( $application );
        $entityManager->flush();
    }
    
    private function setupSuperAdminUser( InputInterface $input, OutputInterface $output, string $localeCode ) : void
    {
        $outputStyle    = new SymfonyStyle( $input, $output );
        $outputStyle->writeln( 'Create SuperAdmin account.' );
        
        $parameters     = [
            '--application' => 'Super Admin',
            '--roles'       => ['ROLE_SUPER_ADMIN'],
            '--locale'      => $localeCode,
            '--designation' => 'Lead Designer / Developer',
        ];
        $this->commandExecutor->runCommand( 'vankosoft:application:create-user', $parameters, $output );
        
        $outputStyle->writeln( '<info>SuperAdmin account successfully created.</info>' );
        $outputStyle->newLine();
    }
    
    private function createAdminPanelUrlQuestion(): Question
    {
        return ( new Question( 'AdminPanel Domain: ' ) )
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
