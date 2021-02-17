<?php namespace VS\UsersBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use SymfonyCasts\Bundle\ResetPassword\Controller\ResetPasswordControllerTrait;
use SymfonyCasts\Bundle\ResetPassword\ResetPasswordHelperInterface;

class ForgotPasswordController extends AbstractController
{
    use ResetPasswordControllerTrait;
    
    private $resetPasswordHelper;
    
    public function __construct( ResetPasswordHelperInterface $helper )
    {
        $this->resetPasswordHelper  = $helper;
    }
    
    public function indexAction( Request $request, MailerInterface $mailer ) : Response
    {
        var_dump( $this->resetPasswordHelper ); die;
        
        if ( $request->isMethod( 'POST' ) ) {
            $user   = $this->getRepository()->findOneBy(  );
            if ( $user ) {
                $verifier = null;   // string
                
                $generator  = $this->container->get( 'symfonycasts.reset_password.token_generator' );
                $token      = $generator->createToken( ( new \DateTime() )->add( new \DateInterval( 'P1D' ) ), $user->getId(), $verifier );
                
                    
                    
                $this->addFlash( 'notice', 'Email sent with link to reset your password !' );
                return $this->redirectToRoute( 'app_login' );
            }
            
            $this->addFlash( 'error', 'This email not found !' );
        }
        
        return $this->render( '@VSUsers/Reseting/forgot_password.html.twig' );
    }
    
    public function resetAction( Request $request ) : Response
    {
        
    }
    
    protected function getRepository()
    {
        return $this->get( 'vs_users.repository.users' );
    }
}
