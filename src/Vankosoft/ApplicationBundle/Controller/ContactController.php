<?php namespace Vankosoft\ApplicationBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

use Symfony\Component\Mailer\MailerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;

use Vankosoft\UsersBundle\Component\UserNotifications;
use Vankosoft\ApplicationBundle\Form\ContactForm;

class ContactController extends AbstractController
{
    /** @var array */
    protected $params;
    
    /** @var MailerInterface */
    protected $mailer;
    
    /** @var UserNotifications */
    protected $notifications;
    
    public function __construct( array $params, MailerInterface $mailer, UserNotifications $notifications )
    {
        $this->params           = $params;
        $this->mailer           = $mailer;
        $this->notifications    = $notifications;
    }
    
    public function index( Request $request ): Response
    {
        $form   = $this->createForm( ContactForm::class, null, ['method' => 'POST'] );
        
        $form->handleRequest( $request );
        if( $form->isSubmitted() && $form->isValid() ) {
            $this->sendEmail( $form->getData(), $this->params['contactEmail'] );
            
            return $this->redirect( $this->generateUrl( 'vs_application_contact' ) );
        }
        
        return $this->render( '@VSApplication/Pages/contact.html.twig', [
            'form'              => $form->createView(),
            'contactEmail'      => $this->params['contactEmail'],
            'showAddress'       => $this->params['showAddress'],
            'showPhone'         => $this->params['showPhone'],
            'showMap'           => $this->params['showMap'],
            'googleMap'         => $this->params['googleMap'],
            'googleLargeMap'    => $this->params['googleLargeMap'],
        ]);
    }
    
    protected function sendEmail( $data, $contactEmail )
    {
        $this->notifications->sentNotificationByRole( 'role-super-admin', 'Contact Form', 'You Have an Email from Contact Form' );
        
        $email = ( new TemplatedEmail() )
                ->from( $data['email'] )
                ->to( $contactEmail )
                ->subject( 'You have Contact Email From Vankosoft.Org' )
                ->htmlTemplate( 'email/contact_email.html.twig' )
                ->context([
                    'fromName'      => $data['name'],
                    'emailSubject'  => $data['subject'],
                    'emailBody'     => $data['message'],
                ]);
        
        $this->mailer->send( $email );
    }
}
