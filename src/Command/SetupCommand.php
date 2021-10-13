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

final class SetupCommand extends AbstractInstallCommand
{
    protected static $defaultName = 'vankosoft:install:setup';
    
    protected function configure() : void
    {
        $this
            ->setDescription( 'VankoSoft Application configuration setup.' )
            ->setHelp(<<<EOT
The <info>%command.name%</info> command allows user to configure basic VankoSoft Application data.
EOT
            )
        ;
    }
    
    protected function execute( InputInterface $input, OutputInterface $output ) : int
    {
        $locale = $this->getContainer()->get( 'vs_app.setup.locale' )->setup( $input, $output, $this->getHelper( 'question' ) );
        //$this->getContainer()->get('sylius.setup.channel')->setup($locale, $currency);
        $this->setupAdminPanelApplication( $input, $output, $locale->getCode() );
        $this->setupAdministratorUser( $input, $output, $locale->getCode() );
        
        $parameters = [];        
        $this->commandExecutor->runCommand( 'vankosoft:application:create', $parameters, $output );
        $this->commandExecutor->runCommand( 'vankosoft:install:application-configuration', $parameters, $output );
        
        return 0;
    }
    
    protected function setupAdminPanelApplication( InputInterface $input, OutputInterface $output, string $localeCode ) : void
    {
        $applicationName    = 'Admin Panel';
        $applicationSlug    = Slug::generate( $applicationName );
        
        $outputStyle    = new SymfonyStyle( $input, $output );
        $outputStyle->writeln( 'Create AdminPanel Application.' );
        
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
        
        $outputStyle->writeln( '<info>AdminPanel Application created successfully.</info>' );
        $outputStyle->newLine();
    }
    
    protected function setupAdministratorUser( InputInterface $input, OutputInterface $output, string $localeCode ) : void
    {
        $outputStyle    = new SymfonyStyle( $input, $output );
        $outputStyle->writeln( 'Create your administrator account.' );
        
        $userManager    = $this->getContainer()->get( 'vs_users.manager.user' );
        
        try {
            $user = $this->configureNewUser( $userManager, $input, $output );
        } catch ( \InvalidArgumentException $exception ) {
            return;
        }
        
        $user->setRoles( ['ROLE_SUPER_ADMIN'] );
        $user->setEnabled( true );
        $user->setVerified( true );
        //$user->setLocaleCode( $localeCode );
        $user->setPreferedLocale( $localeCode );
        
        $userManager->saveUser( $user );
        
        $outputStyle->writeln( '<info>Administrator account successfully registered.</info>' );
        $outputStyle->newLine();
    }
    
    private function configureNewUser(
        $userManager,
        InputInterface $input,
        OutputInterface $output
    ) : UserInterface {
        /** @var UsersRepositoryInterface $userRepository */
        $userRepository = $this->getUserRepository();
        
        if ( $input->getOption( 'no-interaction' ) ) {
            Assert::null( $userRepository->findOneByEmail( 'vankosoft@example.com' ) );
            $user   = $userManager->createUser( 'admin', 'vankosoft@example.com', 'admin' );
            
            return $user;
        }
        
        $email          = $this->getAdministratorEmail( $input, $output );
        $username       = $this->getAdministratorUsername( $input, $output, $email );
        $plainPassword  = $this->getAdministratorPassword( $input, $output );
        $user   = $userManager->createUser( $username, $email, $plainPassword );
        
        return $user;
    }
    
    private function createEmailQuestion() : Question
    {
        return ( new Question( 'E-mail: ' ) )
            ->setValidator(
                /**
                 * @param mixed $value
                 */
                function ( $value ): string {
                    /** @var ConstraintViolationListInterface $errors */
                    $errors = $this->getContainer()->get( 'validator' )->validate( (string) $value, [new Email(), new NotBlank()] );
                    foreach ( $errors as $error ) {
                        throw new \DomainException( $error->getMessage() );
                    }
                    
                    return $value;
                }
            )
            ->setMaxAttempts( 3 )
        ;
    }
    
    private function getAdministratorEmail( InputInterface $input, OutputInterface $output ) : string
    {
        /** @var QuestionHelper $questionHelper */
        $questionHelper = $this->getHelper( 'question' );
        /** @var UsersRepositoryInterface $userRepository */
        $userRepository = $this->getUserRepository();
        
        do {
            $question   = $this->createEmailQuestion();
            $email      = $questionHelper->ask( $input, $output, $question );
            $exists     = null !== $userRepository->findOneByEmail( $email );
            
            if ( $exists ) {
                $output->writeln( '<error>E-Mail is already in use!</error>' );
            }
        } while ( $exists );
        
        return $email;
    }
    
    private function getAdministratorUsername( InputInterface $input, OutputInterface $output, string $email ) : string
    {
        /** @var QuestionHelper $questionHelper */
        $questionHelper = $this->getHelper( 'question' );
        /** @var UsersRepositoryInterface $userRepository */
        $userRepository = $this->getUserRepository();
        
        do {
            $question   = new Question( 'Username (press enter to use email): ', $email );
            $username   = $questionHelper->ask( $input, $output, $question );
            $exists     = null !== $userRepository->findOneBy( ['username' => $username] );
            
            if ($exists) {
                $output->writeln( '<error>Username is already in use!</error>' );
            }
        } while ( $exists );
        
        return $username;
    }
    
    private function getAdministratorPassword( InputInterface $input, OutputInterface $output ) : string
    {
        /** @var QuestionHelper $questionHelper */
        $questionHelper = $this->getHelper( 'question' );
        $validator      = $this->getPasswordQuestionValidator();
        
        do {
            $passwordQuestion           = $this->createPasswordQuestion( 'Choose password:', $validator );
            $confirmPasswordQuestion    = $this->createPasswordQuestion( 'Confirm password:', $validator );
            
            $password                   = $questionHelper->ask( $input, $output, $passwordQuestion );
            $repeatedPassword           = $questionHelper->ask( $input, $output, $confirmPasswordQuestion );
            
            if ( $repeatedPassword !== $password ) {
                $output->writeln( '<error>Passwords do not match!</error>' );
            }
        } while ( $repeatedPassword !== $password );
        
        return $password;
    }
    
    private function getPasswordQuestionValidator() : \Closure
    {
        return
            /** @param mixed $value */
            function ( $value ): string {
                /** @var ConstraintViolationListInterface $errors */
                $errors     = $this->getContainer()->get( 'validator' )->validate( $value, [new NotBlank()] );
                foreach ( $errors as $error ) {
                    throw new \DomainException( $error->getMessage() );
                }
                
                return $value;
            }
        ;
    }
    
    private function createPasswordQuestion( string $message, \Closure $validator ) : Question
    {
        return ( new Question( $message ) )
            ->setValidator( $validator )
            ->setMaxAttempts( 3 )
            ->setHidden( true )
            ->setHiddenFallback( false )
        ;
    }
    
    private function getUserRepository() : UsersRepositoryInterface
    {
        return $this->getContainer()->get( 'vs_users.repository.users' );
    }
    
    private function createAdminPanelUrlQuestion() : Question
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
