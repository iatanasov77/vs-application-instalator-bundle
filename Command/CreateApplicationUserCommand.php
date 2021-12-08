<?php namespace VS\ApplicationInstalatorBundle\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Config\FileLocatorInterface;
use Webmozart\Assert\Assert;

use VS\CmsBundle\Component\Uploader\FileUploaderInterface;
use VS\UsersBundle\Model\UserInterface;
use VS\UsersBundle\Repository\UsersRepositoryInterface;

final class CreateApplicationUserCommand extends AbstractInstallCommand
{
    protected static $defaultName = 'vankosoft:application:create-user';
    
    private ?FileLocatorInterface $fileLocator;
    private ?FileUploaderInterface $imageUploader;
    
    public function __construct(
        ContainerInterface $container,
        ?FileLocatorInterface $fileLocator = null,
        ?FileUploaderInterface $imageUploader = null
    ) {
            parent::__construct( $container );
            
            $this->fileLocator      = $fileLocator;
            $this->imageUploader    = $imageUploader;
    }
    
    protected function configure(): void
    {
        $this
            ->setDescription( 'VankoSoft Application User Setup.' )
            ->setHelp(<<<EOT
The <info>%command.name%</info> command allows user to configure Admin User for the VankoSoft Application.
EOT
            )
            ->addOption(
                'application',
                'a',
                InputOption::VALUE_REQUIRED,
                'Application Name.',
                null
            )
            ->addOption(
                'roles',
                'r',
                InputOption::VALUE_REQUIRED | InputOption::VALUE_IS_ARRAY,
                'Assign User Roles.',
                ['ROLE_SUPER_ADMIN', 'ROLE_APPLICATION_ADMIN']
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
        $application    = $input->getOption( 'application' );
        $roles          = $input->getOption( 'roles' );
        $locale         = $input->getOption( 'locale' );
        $userManager    = $this->getContainer()->get( 'vs_users.manager.user' );
        
        // Setup UserInfo Object
        try {
            $user = $this->createNewUser( $userManager, $input, $output );
            
            $userNames  = explode( ' ', $application );
            $user->getInfo()->setFirstName( $userNames[0] );
            $user->getInfo()->setLastName( end( $userNames ) );
        } catch ( \InvalidArgumentException $exception ) {
            return;
        }
        $this->setupAdministratorsAvatar( $user );
        $this->setupUserRoles( $user, $roles );
        
        // Setup User Properties
        $user->setEnabled( true );
        $user->setVerified( true );
        $user->setPreferedLocale( $locale );
        
        // Save User
        $userManager->saveUser( $user );
        
        return Command::SUCCESS;
    }
    
    private function createNewUser(
        $userManager,
        InputInterface $input,
        OutputInterface $output
    ): UserInterface {
        $email          = $this->getAdministratorEmail( $input, $output );
        $username       = $this->getAdministratorUsername( $input, $output, $email );
        $plainPassword  = $this->getAdministratorPassword( $input, $output );
        
        $userRepository = $this->getContainer()->get( 'vs_users.repository.users' );
        Assert::null( $userRepository->findOneByEmail( $email ) );
        //Assert::null( $userRepository->findOneByUsername( $username ) );
        $user   = $userManager->createUser( $username, $email, $plainPassword );
        
        return $user;
    }
    
    private function setupUserRoles( UserInterface &$user, array $roles )
    {
        $user->setRolesArray( $roles ); // For Backward Compatibility
        
        $rolesRepo  = $this->get( 'vs_users.repository.user_roles' );
        //$userRole   = $rolesRepo->findByTaxonCode( 'role-super-admin' );
        foreach ( $roles as $r ) {
            $userRole   = $rolesRepo->findOneBy( ['role'=> $r] );
            if ( $userRole ) {
                $user->addRole( $userRole );
            }
        }
    }
    
    private function getAdministratorEmail( InputInterface $input, OutputInterface $output ): string
    {
        /** @var QuestionHelper $questionHelper */
        $questionHelper = $this->getHelper( 'question' );
        /** @var UsersRepositoryInterface $userRepository */
        $userRepository = $this->getContainer()->get( 'vs_users.repository.users' );
        
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
    
    private function getAdministratorUsername( InputInterface $input, OutputInterface $output, string $email ): string
    {
        /** @var QuestionHelper $questionHelper */
        $questionHelper = $this->getHelper( 'question' );
        /** @var UsersRepositoryInterface $userRepository */
        $userRepository = $this->getContainer()->get( 'vs_users.repository.users' );
        
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
    
    private function getAdministratorPassword( InputInterface $input, OutputInterface $output ): string
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
    
    private function setupAdministratorsAvatar( &$user )
    {
        if ( $this->fileLocator === null || $this->imageUploader === null ) {
            throw new \RuntimeException( 'You must configure a $fileLocator or/and $imageUploader' );
        }
        
        $imagePath      = $this->fileLocator->locate( '@VSApplicationInstalatorBundle/Resources/fixtures/adminAvatars/vankosoft.png' );
        $uploadedImage  = new UploadedFile( $imagePath, basename( $imagePath ) );
        
        $avatarImage    = $this->getContainer()->get( 'vs_users.factory.avatar_image' )->createNew();
        $avatarImage->setFile( $uploadedImage );
        
        $this->imageUploader->upload( $avatarImage );
        
        $user->getInfo()->setAvatar( $avatarImage );
    }
    
    private function createEmailQuestion(): Question
    {
        return ( new Question( 'E-mail: ' ) )->setValidator(
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
        )->setMaxAttempts( 3 );
    }
    
    private function getPasswordQuestionValidator(): \Closure
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
    
    private function createPasswordQuestion( string $message, \Closure $validator ): Question
    {
        return ( new Question( $message ) )
            ->setValidator( $validator )
            ->setMaxAttempts( 3 )
            ->setHidden( true )
            ->setHiddenFallback( false )
        ;
    }
}
