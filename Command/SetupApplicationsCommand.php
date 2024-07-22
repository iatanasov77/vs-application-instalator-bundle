<?php namespace Vankosoft\ApplicationInstalatorBundle\Command;

use Symfony\Component\Console\Attribute\AsCommand;
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

use Vankosoft\ApplicationBundle\Component\Application\Project;

#[AsCommand(
    name: 'vankosoft:install:setup-applications',
    description: 'VankoSoft Main Applications configuration setup.',
    hidden: false
)]
final class SetupApplicationsCommand extends AbstractInstallCommand
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
        /** @var QuestionHelper $questionHelper */
        $questionHelper     = $this->getHelper( 'question' );
        
        $locale             = $input->getOption( 'default-locale' );
        
        // Setup Admin account for All Applications.
        $this->setupApplicationsAdminUser( $input, $output, $locale );
        
        // Setup a Catalog Application
        if ( $this->isCatalogProject() || $this->isExtendedProject() ) {
            return $this->setupApplication( $input, $output, $locale );
        }
        
        // Setup a Standard Application
        if ( $questionHelper->ask( $input, $output, new ConfirmationQuestion( 'Do you want to create a default application? (y/N) ', false ) ) ) {
            return $this->setupApplication( $input, $output, $locale );
        }
        
        return Command::SUCCESS;
    }
    
    private function setupApplication( InputInterface $input, OutputInterface $output, string $localeCode ): int
    {
        $outputStyle        = new SymfonyStyle( $input, $output );
        $commandParameters  = ['--new-project' => true, '--locale' => $localeCode, '--theme' => 'vankosoft/application-theme-2'];
        
        $appSetup           = $this->get( 'vs_application.installer.setup_application' );
        switch ( $appSetup->getProjectType() ) {
           case Project::PROJECT_TYPE_CATALOG:
               $commandParameters['--type']    = self::APPLICATION_TYPE_CATALOG;
               
               $this->commandExecutor->runCommand(
                   'vankosoft:application:create',
                    $commandParameters,
                    $output
                );
                break;
            case Project::PROJECT_TYPE_EXTENDED:
                $applicationType                = $this->createApplicationTypeQuestion( $input, $output );
                
                /** @var QuestionHelper $questionHelper */
                $questionHelper     = $this->getHelper( 'question' );
                
                $questionName                   = $this->createApplicationNameQuestion();
                $commandParameters['--name']    = $questionHelper->ask( $input, $output, $questionName );
                
                $questionUrl                    = $this->createApplicationUrlQuestion();
                $commandParameters['--url']     = $questionHelper->ask( $input, $output, $questionUrl );
                
                switch ( $applicationType ) {
                    case self::APPLICATION_TYPE_STANDRD:
                        $commandParameters['--type']    = self::APPLICATION_TYPE_STANDRD;
                        
                        $this->commandExecutor->runCommand(
                            'vankosoft:application:create',
                            $commandParameters,
                            $output
                        );
                        
                        $outputStyle->writeln( '<info>Create a Separate API Application.</info>' );
                        $commandParameters['--name']    = $commandParameters['--name'] . " API";
                        $commandParameters['--url']     = "api." . $commandParameters['--url'];
                        $commandParameters['--type']    = self::APPLICATION_TYPE_API;
                        
                        $this->commandExecutor->runCommand(
                            'vankosoft:application:create',
                            $commandParameters,
                            $output
                         );
                        
                        break;
                    case self::APPLICATION_TYPE_CATALOG:
                        $commandParameters['--type']    = self::APPLICATION_TYPE_CATALOG;
                        
                        $this->commandExecutor->runCommand(
                            'vankosoft:application:create',
                            $commandParameters,
                            $output
                        );
                        
                        $outputStyle->writeln( '<info>Create a Separate API Application.</info>' );
                        $commandParameters['--name']    = $commandParameters['--name'] . " API";
                        $commandParameters['--url']     = "api." . $commandParameters['--url'];
                        $commandParameters['--type']    = self::APPLICATION_TYPE_API;
                        
                        $this->commandExecutor->runCommand(
                            'vankosoft:application:create',
                            $commandParameters,
                            $output
                        );
                        
                        break;
                    default:
                        $commandParameters['--type']    = self::APPLICATION_TYPE_EXTENDED;
                        
                        $this->commandExecutor->runCommand(
                            'vankosoft:application:create',
                            $commandParameters,
                            $output
                        );
                }
                
                break;
            default:
                $commandParameters['--type']    = self::APPLICATION_TYPE_STANDRD;
                
                $this->commandExecutor->runCommand(
                    'vankosoft:application:create',
                    $commandParameters,
                    $output
                );
        }
        
        $outputStyle->newLine();
        $outputStyle->writeln( '<info>Default Application created successfully.</info>' );
        $outputStyle->newLine();
        
        return Command::SUCCESS;
    }
    
    private function setupApplicationsAdminUser( InputInterface $input, OutputInterface $output, string $localeCode ) : void
    {
        $outputStyle    = new SymfonyStyle( $input, $output );
        $outputStyle->writeln( 'Create Admin account for All Applications.' );
        
        $parameters     = [
            '--application' => 'Applications Admin',
            '--roles'       => ['ROLE_APPLICATION_ADMIN'],
            '--locale'      => $localeCode,
            '--designation' => 'Project Owner',
        ];
        $this->commandExecutor->runCommand( 'vankosoft:application:create-user', $parameters, $output );
        
        $outputStyle->writeln( '<info>Admin account for All Applications successfully created.</info>' );
        $outputStyle->newLine();
    }
}
