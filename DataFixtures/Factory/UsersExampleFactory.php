<?php namespace Vankosoft\ApplicationInstalatorBundle\DataFixtures\Factory;

use Symfony\Component\Config\FileLocatorInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;

use Vankosoft\UsersBundle\Security\UserManager;

use Vankosoft\CmsBundle\Component\Uploader\FileUploaderInterface;
use Vankosoft\UsersBundle\Model\UserInterface;
use Vankosoft\UsersBundle\Model\UserInfoInterface;

class UsersExampleFactory extends AbstractExampleFactory implements ExampleFactoryInterface
{
    /** @var UserManager */
    private $userManager;
    
    /** @var FactoryInterface */
    private $userInfoFactory;
    
    /** @var FactoryInterface */
    private $avatarImageFactory;
    
    /** @var RepositoryInterface */
    private $userRolesRepository;
    
    /** @var OptionsResolver */
    private $optionsResolver;
    
    private string $localeCode;
    
    private ?FileLocatorInterface $fileLocator;
    
    private ?FileUploaderInterface $imageUploader;
    
    public function __construct(
        UserManager $userManager,
        RepositoryInterface $userRolesRepository,
        FactoryInterface $userInfoFactory,
        FactoryInterface $avatarImageFactory,
        
        string $localeCode,
        ?FileLocatorInterface $fileLocator = null,
        ?FileUploaderInterface $imageUploader = null
    ) {
        $this->userManager          = $userManager;
        $this->userRolesRepository  = $userRolesRepository;
        
        $this->userInfoFactory      = $userInfoFactory;
        $this->avatarImageFactory   = $avatarImageFactory;
        $this->localeCode           = $localeCode;
        
        $this->fileLocator          = $fileLocator;
        $this->imageUploader        = $imageUploader;
        
        $this->optionsResolver      = new OptionsResolver();
        $this->configureOptions( $this->optionsResolver );
    }
    
    public function create( array $options = [] ): UserInterface
    {
        $options    = $this->optionsResolver->resolve( $options );

        $userEntity = $this->userManager->createUser( $options['username'], $options['username'], $options['password'] );
        
        $userEntity->setEnabled( true );
        $userEntity->setPreferedLocale( $this->localeCode );
        
        $userRole   = $this->userRolesRepository->findByTaxonCode( $options['role_code'] );
        if ( $userRole ) {
            $userEntity->addRole( $userRole );
        }
        
        $this->createUserInfo( $userEntity, $options );
        
        return $userEntity;
    }
    
    protected function configureOptions( OptionsResolver $resolver ): void
    {
        $resolver
            ->setDefault( 'role_code', null )
            ->setAllowedTypes( 'role_code', ['string', 'null'] )
        
            ->setDefault( 'email', null )
            ->setAllowedTypes( 'email', 'string' )
            
            ->setDefault( 'username', null )
            ->setAllowedTypes( 'username', 'string' )
            
            ->setDefault( 'enabled', true )
            ->setAllowedTypes( 'enabled', 'bool' )
            
            ->setDefault( 'password', 'password123' )
            ->setDefault( 'locale_code', $this->localeCode )
            ->setDefault( 'api', false )
            
            ->setDefined('first_name')
            ->setDefined('last_name')
            
            ->setDefault('avatar', '')
            ->setAllowedTypes('avatar', ['string', 'null'])
        ;
    }
    
    private function createUserInfo( UserInterface &$user, array $options ): void
    {
        $userInfo   = $this->userInfoFactory->createNew();
        
        if ( $options['avatar'] && ! empty( $options['avatar'] ) ) {
            $this->createAvatar( $userInfo, $options );
        }
        $userInfo->setFirstName( $options['first_name'] );
        $userInfo->setLastName( $options['last_name'] );
        
        $user->setInfo( $userInfo );
    }
    
    private function createAvatar( UserInfoInterface &$userInfo, array $options ): void
    {
        if ( $this->fileLocator === null || $this->imageUploader === null ) {
            throw new \RuntimeException( 'You must configure a $fileLocator or/and $imageUploader' );
        }
        
        $imagePath      = $this->fileLocator->locate( $options['avatar'] );
        $uploadedImage  = new UploadedFile( $imagePath, basename( $imagePath ) );
        
        $avatarImage    = $this->avatarImageFactory->createNew();
        $avatarImage->setFile( $uploadedImage );
        
        $this->imageUploader->upload( $avatarImage );
        
        $userInfo->setAvatar( $avatarImage );
    }
}
