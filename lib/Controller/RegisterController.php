<?php namespace VS\UsersBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

use Symfony\Component\Mailer\MailerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use SymfonyCasts\Bundle\VerifyEmail\VerifyEmailHelperInterface;

use VS\UsersBundle\Form\RegistrationFormType;
use VS\UsersBundle\Model\UserInterface;

class RegisterController extends AbstractController
{
    /**
     * @var VerifyEmailHelperInterface
     */
    private $verifyEmailHelper;

    public function __construct( VerifyEmailHelperInterface $helper )
    {        
        $this->verifyEmailHelper = $helper;
    }
    
    public function index( Request $request, MailerInterface $mailer ) : Response
    {
        $em         = $this->getDoctrine()->getManager();
        $factory    = $this->getFactory();
        $oUser      = $factory->createNew();
        $form       = $this->createForm( RegistrationFormType::class, $oUser, [
            'data'      => $oUser,
            'action'    => $this->generateUrl( 'vs_users_register_form' ),
            'method'    => 'POST',
        ]);
        
        $form->handleRequest( $request );
        if ( $form->isSubmitted() ) {
            $userManager    = $this->container->get( 'vs_users.manager.user' );
            $oUser          = $form->getData();
            
            $userManager->encodePassword( $oUser, $oUser->getPassword() );
            
            $oUser->setPreferedLocale( $request->getLocale() );
            $oUser->setRoles( ['ROLE_USER'] );
            $oUser->setVerified( false );
            $oUser->setEnabled( false );
            
            $em->persist( $oUser );
            $em->flush();
            
            $this->sendConfirmationMail( $oUser, $mailer );
            
            return $this->redirectToRoute( $this->container->getParameter( 'vs_users.default_redirect' ) );
        }
        
        return $this->render( '@VSUsers/Register/register.html.twig', [
            'errors' => $form->getErrors( true, false ),
            'form'  => $form->createView(),
        ]);
    }
    
    public function verify( Request $request ): Response
    {
//         $this->denyAccessUnlessGranted( 'IS_AUTHENTICATED_FULLY' );
//         $user = $this->getUser();
        $id = $request->get( 'id' ); // retrieve the user id from the url
        // Verify the user id exists and is not null
        if( null === $id ) {
            return $this->redirectToRoute( $this->container->getParameter( 'vs_users.default_redirect' ) );
        }

        $user = $this->get( 'vs_users.repository.users' )->find( $id );

        // Ensure the user exists in persistence
        if ( null === $user ) {
            return $this->redirectToRoute( $this->container->getParameter( 'vs_users.default_redirect' ) );
        }
                
        try {
            $this->verifyEmailHelper->validateEmailConfirmation( $request->getUri(), $user->getId(), $user->getEmail() );
        } catch ( VerifyEmailExceptionInterface $e ) {
            $this->addFlash( 'verify_email_error', $e->getReason() );
            
            return $this->redirectToRoute( 'vs_users_register_form' );
        }
        
        // Mark your user as verified.
        $user->setVerified( true );
        $user->setEnabled( true );
        $this->getDoctrine()->getManager()->persist( $user );
        $this->getDoctrine()->getManager()->flush();
        
        $this->addFlash( 'success', 'Your e-mail address has been verified.' );
        
        return $this->redirectToRoute( $this->container->getParameter( 'vs_users.default_redirect' ) );
    }
    
    protected function getRepository()
    {
        return $this->get( 'vs_users.repository.users' );
    }
    
    protected function getFactory()
    {
        return $this->get( 'vs_users.factory.users' );
    }
    
    private function sendConfirmationMail( UserInterface $oUser, MailerInterface $mailer )
    {
        $signatureComponents = $this->verifyEmailHelper->generateSignature(
                                    'vs_users_register_confirmation',
                                    $oUser->getId(),
                                    $oUser->getEmail(),
                                    ['id' => $oUser->getId()]
                                );
        
        $email = ( new TemplatedEmail() )
                ->from( $this->container->getParameter( 'mailer_user' ) )
                ->to( $oUser->getEmail() )
                ->htmlTemplate( '@VSUsers/Register/confirmation_email.html.twig' )
                ->context([
                    'signedUrl'             => $signatureComponents->getSignedUrl(),
                    'expiresAtMessageKey'   => $signatureComponents->getExpirationMessageKey(),
                    'expiresAtMessageData'  => $signatureComponents->getExpirationMessageData(),
                ]);
        
        $mailer->send( $email );
    }
}
