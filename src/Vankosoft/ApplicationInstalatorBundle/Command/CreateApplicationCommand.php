<?php namespace Vankosoft\ApplicationInstalatorBundle\Command;

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

use Vankosoft\ApplicationBundle\Component\Slug;
use Vankosoft\ApplicationBundle\Model\Interfaces\ApplicationInterface;
use Vankosoft\UsersBundle\Model\UserRoleInterface;

final class CreateApplicationCommand extends AbstractInstallCommand
{
    private $appSlug;
    
    protected static $defaultName = 'vankosoft:application:create';
    
    public function getApplicationSlug()
    {
        return $this->appSlug;
    }
    
    protected function configure(): void
    {
        $this
            ->setDescription( 'VankoSoft Application Create Command.' )
            ->setHelp(<<<EOT
The <info>%command.name%</info> command allows user to create a VankoSoft Application.
EOT
            )
            ->addOption(
                'new-project',
                'p',
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
        $newProjectOption   = $input->getOption( 'new-project' );
        $newProject         = ( $newProjectOption !== false );
        
        /** @var QuestionHelper $questionHelper */
        $questionHelper     = $this->getHelper( 'question' );
        
        $questionName       = $this->createApplicationNameQuestion();
        $applicationName    = $questionHelper->ask( $input, $output, $questionName );
        
        $appSetup           = $this->getContainer()->get( 'vs_application.installer.setup_application' );
        $outputStyle        = new SymfonyStyle( $input, $output );
        
        // Add Database Records
        $outputStyle->writeln( 'Create Application Database Records.' );
        $this->createApplicationDatabaseRecords( $input, $output, $applicationName, $localeCode );
        $outputStyle->writeln( '<info>Application Database Records successfully created.</info>' );
        $outputStyle->newLine();
        
        // Create Directories
        $outputStyle->writeln( 'Create Application Directories.' );
        $appSetup->setupApplication( $applicationName, $newProject );
        $outputStyle->writeln( '<info>Application Directories successfully created.</info>' );
        $outputStyle->newLine();
        
        return Command::SUCCESS;
    }
    
    private function createApplicationDatabaseRecords( InputInterface $input, OutputInterface $output, $applicationName, $localeCode )
    {
        /** @var QuestionHelper $questionHelper */
        $questionHelper     = $this->getHelper( 'question' );
        
        $outputStyle        = new SymfonyStyle( $input, $output );
        
        /*
         * Create Application
         */
        $application        = $this->createApplication( $input, $output, $applicationName );
        $this->appSlug      = $application->getCode();
        
        /*
         * Create Application Base Role
         */
        $baseRole           = $this->createApplicationBaseRole( $input, $output, $applicationName );
        
        /*
         * Create Application Users
         */
        if ( $questionHelper->ask( $input, $output, new ConfirmationQuestion( 'Do you want to create application users? (y/N) ', false ) ) ) {
            $this->createApplicationUsers( $input, $output, $applicationName, $baseRole->getRole(), $localeCode );
        } else {
            $outputStyle->writeln( 'Cancelled creating application users.' );
        }
    }
    
    private function createApplication( InputInterface $input, OutputInterface $output, $applicationName ): ApplicationInterface
    {
        $entityManager      = $this->getContainer()->get( 'doctrine.orm.entity_manager' );
        
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
        
        $entityManager->persist( $application );
        $entityManager->flush();
        
        return $application;
    }
    
    private function createApplicationBaseRole( InputInterface $input, OutputInterface $output, $applicationName ): UserRoleInterface
    {
        $entityManager      = $this->getContainer()->get( 'doctrine.orm.entity_manager' );
        
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
        
        $entityManager->persist( $roleTaxon );
        $entityManager->persist( $role );
        $entityManager->flush();
        
        return $role;
    }
    
    private function createApplicationUsers( InputInterface $input, OutputInterface $output, $applicationName, $baseRole, $localeCode )
    {
        $outputStyle    = new SymfonyStyle( $input, $output );
        $outputStyle->writeln( 'Create Admin account for this Application Only.' );
        
        $parameters     = [
            '--application' => $applicationName,
            '--roles'       => [$baseRole],
            '--locale'      => $localeCode
        ];
        $this->commandExecutor->runCommand( 'vankosoft:application:create-user', $parameters, $output );
        
        $outputStyle->writeln( '<info>Admin account for this Application successfully created.</info>' );
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
