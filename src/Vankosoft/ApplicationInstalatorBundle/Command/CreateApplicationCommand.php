<?php namespace VS\ApplicationInstalatorBundle\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Symfony\Component\Console\Input\ArrayInput;
use Webmozart\Assert\Assert;
use Gedmo\Sluggable\Util\Urlizer;

use VS\ApplicationBundle\Component\Slug;
use VS\ApplicationBundle\Model\Interfaces\ApplicationInterface;
use VS\UsersBundle\Model\UserRoleInterface;

final class CreateApplicationCommand extends AbstractInstallCommand
{
    protected static $defaultName = 'vankosoft:application:create';
    
    protected function configure(): void
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
            ->addOption(
                'locale',
                'l',
                InputOption::VALUE_REQUIRED,
                'Prefered User Locale.',
                'en_US'
            )
        ;
    }
    
    protected function execute( InputInterface $input, OutputInterface $output ): int
    {
        $localeCode         = $input->getOption( 'locale' );
        $setupKernelOption  = $input->getOption( 'setup-kernel' );
        $setupKernel        = ( $setupKernelOption !== false );
        
        /** @var QuestionHelper $questionHelper */
        $questionHelper     = $this->getHelper( 'question' );
        
        $questionName       = $this->createApplicationNameQuestion();
        $applicationName    = $questionHelper->ask( $input, $output, $questionName );
        
        $appSetup           = $this->getContainer()->get( 'vs_application_instalator.setup_application' );
        $outputStyle        = new SymfonyStyle( $input, $output );
        
        // Create Directories
        $outputStyle->writeln( 'Create Application Directories.' );
        $appSetup->setupApplication( $applicationName, $setupKernel );
        $outputStyle->writeln( '<info>Application Directories successfully created.</info>' );
        
        // Add Database Records
        $this->createApplicationDatabaseRecords( $input, $output, $applicationName, $localeCode );
        
        $outputStyle->newLine();
        
        return Command::SUCCESS;
    }
    
    private function createApplicationDatabaseRecords( InputInterface $input, OutputInterface $output, $applicationName, $localeCode )
    {
        $entityManager      = $this->getContainer()->get( 'doctrine.orm.entity_manager' );
        
        /** @var QuestionHelper $questionHelper */
        $questionHelper     = $this->getHelper( 'question' );
        
        $outputStyle        = new SymfonyStyle( $input, $output );
        $outputStyle->writeln( 'Create Application Database Records.' );
        
        /*
         * Create Application
         */
        $application        = $this->createApplication( $input, $output, $applicationName );
        $entityManager->persist( $application );
        
        /*
         * Create Application Base Role
         */
        $baseRole   = $this->createApplicationBaseRole( $input, $output, $applicationName );
        $entityManager->persist( $baseRole );
        
        $entityManager->flush();
        
        /*
         * Create Application Users
         */
        if ( $questionHelper->ask( $input, $output, new ConfirmationQuestion( 'Do you want to create application users? (y/N) ', false ) ) ) {
            $this->createApplicationUsers( $input, $output, $applicationName, $baseRole->getRole(), $localeCode );
        } else {
            $outputStyle->writeln( 'Cancelled creating application users.' );
        }
        
        /* OLD WAY
         * ===========
         *
         // bin/console doctrine:query:sql "INSERT INTO VSAPP_Applications(title) VALUES('Test Application')"
         $command    = $this->getApplication()->find( 'doctrine:query:sql' );
         
         // Create Records
         $returnCode = $command->run(
         new ArrayInput( ['sql' =>"INSERT INTO VSAPP_Applications(enabled, code, title, hostname, created_at) VALUES(1, '{$applicationSlug}', '{$applicationName}', '{$applicationUrl}', '{$applicationCreated}')"] ),
         $output
         );
         */
        
        $outputStyle->writeln( '<info>Application Database Records successfully created.</info>' );
    }
    
    private function createApplication( InputInterface $input, OutputInterface $output, $applicationName ): ApplicationInterface
    {
        /** @var QuestionHelper $questionHelper */
        $questionHelper     = $this->getHelper( 'question' );
        
        $questionUrl        = $this->createApplicationUrlQuestion();
        $applicationUrl     = $questionHelper->ask( $input, $output, $questionUrl );
        $applicationSlug    = Slug::generate( $applicationName );
        $applicationCreated = new \DateTime;
        
        $application        = $this->getContainer()->get( 'vs_application.factory.application' )->createNew();
        $application->setCode( $applicationSlug );
        $application->setTitle( $applicationName );
        $application->setHostname( $applicationUrl );
        $application->setCreatedAt( $applicationCreated );
        
        return $application;
    }
    
    private function createApplicationBaseRole( InputInterface $input, OutputInterface $output, $applicationName ): UserRoleInterface
    {
        /*
         * Create Application Base Role Taxon
         */
        $taxonSlug          = Slug::generate( 'Role ' . $applicationName );
        $roleTaxon          = $this->getContainer()->get( 'vs_application.factory.taxon' )->createNew();
        $taxonomyRootTaxon  = $this->getContainer()->get( 'vs_application.repository.taxonomy' )
                                                    ->findByCode( 'user-roles' )
                                                    ->getRootTaxon();
        $taxonParent        = $this->getContainer()->get( 'vs_application.repository.taxon' )
                                                    ->findOneBy( ['code' => 'role-application-admin'] );
        
        $roleTaxon->setParent( $taxonParent ?: $taxonomyRootTaxon );
        $roleTaxon->setCode( $taxonSlug );
        $roleTaxon->setCurrentLocale( 'en_US' );
        $roleTaxon->getTranslation()->setName( 'Role ' . $applicationName );
        $roleTaxon->getTranslation()->setDescription( $applicationName );
        $roleTaxon->getTranslation()->setSlug( $taxonSlug );
        $roleTaxon->getTranslation()->setTranslatable( $roleTaxon );
        
        /*
         * Create Application Base Role
         */
        $role               = $this->getContainer()->get( 'vs_users.factory.user_roles' )->createNew();
        $roleParent         = $this->getContainer()->get( 'vs_users.repository.user_roles' )
                                                    ->findByTaxonCode( 'role-application-admin' );
        $role->setParent( $roleParent );
        $role->setTaxon( $roleTaxon );
        
        $adminRole          = 'ROLE_' . \strtoupper( Urlizer::urlize( $applicationName, '_' ) ) . '_ADMIN';
        $role->setRole( $adminRole );
        
        return $role;
    }
    
    private function createApplicationUsers( InputInterface $input, OutputInterface $output, $applicationName, $baseRole, $localeCode )
    {
        $outputStyle    = new SymfonyStyle( $input, $output );
        $outputStyle->writeln( 'Create Application Admin account.' );
        
        $parameters     = [
            '--application' => $applicationName,
            '--roles'       => [$baseRole],
            '--locale'      => $localeCode
        ];
        $this->commandExecutor->runCommand( 'vankosoft:application:create-user', $parameters, $output );
        
        $outputStyle->writeln( '<info>Application Admin account successfully created.</info>' );
        $outputStyle->newLine();
    }
    
    private function createApplicationNameQuestion(): Question
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
    
    private function createApplicationUrlQuestion(): Question
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
