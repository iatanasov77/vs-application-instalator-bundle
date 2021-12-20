<?php namespace Vankosoft\ApplicationBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class AuthController extends AbstractController
{
    public function login( AuthenticationUtils $authenticationUtils ): Response
    {
        if ( $this->getUser() && $this->isGranted( 'ROLE_SUPER_ADMIN', $this->getUser() ) ) {
            return $this->redirectToRoute( 'vs_application_dashboard' );
        }
        
        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        //var_dump($error); die;
        
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();
        
        $tplVars = array(
            'last_username' => $lastUsername,
            'error'         => $error,
        );
        
        return $this->render( '@VSApplication/Pages/login.html.twig', $tplVars );
    }
    
    public function logout()
    {
        // controller can be blank: it will never be executed!
        throw new \Exception( 'Don\'t forget to activate logout in security.yaml' );
    }
}

