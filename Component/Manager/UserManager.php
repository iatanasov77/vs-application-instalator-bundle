<?php namespace VS\UsersBundle\Component\Manager;

use Symfony\Component\Security\Core\Encoder\EncoderFactory;
use Doctrine\ORM\EntityManager;

use App\Entity\User;

class UserManager
{
    private $encoderFactory;
    private $entityManager;
    
    public function __construct( EncoderFactory $encoderFactory, EntityManager $entityManager )
    {
        $this->encoderFactory   = $encoderFactory;
        $this->entityManager    = $entityManager;
    }
    
    public function create( $username, $password )
    {
        $this->createByMail( $username, $password );
    }
    
    protected function createByMail( $email, $password )
    {
        //$factory = $this->get( 'security.encoder_factory' );
        $user       = new User();
        $encoder    = $this->encoderFactory->getEncoder( $user );
        $salt       = md5( time() );
        $pass       = $encoder->encodePassword( $password, $salt );
        
        $user->setEmail( $email );
        $user->setPassword( $pass );
        //$user->setSalt( $salt );
        //$user->setActive( 1 ); //enable or disable
        
        $this->entityManager->persist( $user );
        $this->entityManager->flush();
    }
}

