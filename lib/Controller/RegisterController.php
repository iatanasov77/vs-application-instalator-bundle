<?php namespace VS\UsersBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;
use SymfonyCasts\Bundle\VerifyEmail\VerifyEmailHelperInterface;

class RegisterController extends AbstractController
{
    private $verifyEmailHelper;
    private $mailer;
    
    public function __construct( VerifyEmailHelperInterface $helper, MailerInterface $mailer )
    {
        $this->verifyEmailHelper = $helper;
        $this->mailer = $mailer;
    }
    
    public function index()
    {
        $em         = $this->getDoctrine()->getManager();
        $repository = $this->getDoctrine()->getRepository( Project::class );
        $project    = $id ? $repository->find( $id ) : new Project();
        $form       = $this->_projectForm( $project );
        
        $form->handleRequest( $request );
        if ( $form->isSubmitted() ) {
            $user   = $form->getData();
            
            $em->persist( $user );
            $em->flush();
            
            $signatureComponents = $this->verifyEmailHelper->generateSignature(
                'registration_confirmation_route',
                $user->getId(),
                $user->getEmail()
            );
            
            $email = new TemplatedEmail();
            $email->to( $user->getEmail() );
            $email->htmlTemplate( 'registration/confirmation_email.html.twig' );
            $email->context( ['signedUrl' => $signatureComponents->getSignedUrl()] );
            
            $this->mailer->send($email);
        }
        
        return $this->render( '@VSUsers/Register/register.html.twig', [
            'error' => $form->getErrors( true, false ),
        ]);
    }
}
