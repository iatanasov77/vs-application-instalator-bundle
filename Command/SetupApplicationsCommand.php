<?php namespace Vankosoft\ApplicationInstalatorBundle\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Symfony\Component\Console\Input\ArrayInput;

use Vankosoft\ApplicationBundle\Component\Slug;

final class SetupApplicationsCommand extends AbstractInstallCommand
{
    protected static $defaultName = 'vankosoft:install:setup-applications';
    
    protected function configure(): void
    {
        $this
            ->setDescription( 'VankoSoft Main Applications configuration setup.' )
            ->setHelp(<<<EOT
The <info>%command.name%</info> command allows user to configure basic VankoSoft Application data.
EOT
            )
            ->addOption( 'default-locale', 'l', InputOption::VALUE_REQUIRED, 'Prefered User Locale.', 'en_US' )
        ;
    }
    
    protected function execute( InputInterface $input, OutputInterface $output ): int
    {
        /** @var QuestionHelper $questionHelper */
        $questionHelper     = $this->getHelper( 'question' );
        
        $locale             = $input->getOption( 'default-locale' );
        
        // Setup Admin account for All Applications.
        $this->setupApplicationsAdminUser( $input, $output, $locale );
        
        // Setup an Application
        if ( $questionHelper->ask( $input, $output, new ConfirmationQuestion( 'Do you want to create a default application? (y/N) ', false ) ) ) {
            $this->setupApplication( $input, $output, $locale );
            
            // Setup Application Theme
            $outputStyle                = new SymfonyStyle( $input, $output );
            $outputStyle->newLine();
            $outputStyle->writeln( 'Setup Application Theme.' );
            
            $defaultApplicationTheme    = 'vankosoft/application-theme-2';
            $question                   = new Question( 'Application Theme (press enter to use "' . $defaultApplicationTheme . '"): ', $defaultApplicationTheme );
            $themeName                  = trim( $questionHelper->ask( $input, $output, $question ) );
 
            $applicationSlug            = $this->commandExecutor->getCommand( 'vankosoft:application:create' )->getApplicationSlug();
            //$theme   = $this->get( 'vs_app.theme_repository' )->findOneByName( $themeName );
            $this->setupApplicationTheme( $applicationSlug, $themeName );
            $outputStyle->newLine();
        }
        
        return Command::SUCCESS;
    }
    
    private function setupApplication( InputInterface $input, OutputInterface $output, string $localeCode )
    {
        $outputStyle        = new SymfonyStyle( $input, $output );
        
        $this->commandExecutor->runCommand( 'vankosoft:application:create', ['--new-project' => true, '--locale' => $localeCode], $output );
        
        $outputStyle->newLine();
        $outputStyle->writeln( '<info>Default Application created successfully.</info>' );
        $outputStyle->newLine();
    }
    
    private function setupApplicationsAdminUser( InputInterface $input, OutputInterface $output, string $localeCode ) : void
    {
        $outputStyle    = new SymfonyStyle( $input, $output );
        $outputStyle->writeln( 'Create Admin account for All Applications.' );
        
        $parameters     = [
            '--application' => 'Applications Admin',
            '--roles'       => ['ROLE_APPLICATION_ADMIN'],
            '--locale'      => $localeCode
        ];
        $this->commandExecutor->runCommand( 'vankosoft:application:create-user', $parameters, $output );
        
        $outputStyle->writeln( '<info>Admin account for All Applications successfully created.</info>' );
        $outputStyle->newLine();
    }
    
    private function setupApplicationTheme( $applicationSlug, $themeName )
    {
        $entityManager          = $this->getContainer()->get( 'doctrine.orm.entity_manager' );
        $applicationRepository  = $this->getContainer()->get( 'vs_application.repository.application' );
        $settingsRepository     = $this->getContainer()->get( 'vs_application.repository.settings' );
        
        $application            = $applicationRepository->findOneByCode( $applicationSlug );
        $settings               = $settingsRepository->getSettings( $application );
        if ( ! $settings ) {
            $settings   = $this->getContainer()->get( 'vs_application.factory.settings' )->createNew();
        }
        
        $settings->setTheme( $themeName );
        $settings->setApplication( $application );
        $settings->setMaintenanceMode( false );
        
        $entityManager->persist( $settings );
        $entityManager->flush();
    }
}