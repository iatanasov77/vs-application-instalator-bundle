<?php namespace IA\UsersBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use IA\UsersBundle\Entity\UserSubscription;
use IA\UsersBundle\Entity\UserActivity;
use IA\UsersBundle\Entity\PackagePlan;
use IA\PaymentBundle\Entity\Payment;
/**
 * @TODO Ã�â€�Ã�Â° Ã‘ï¿½Ã�Âµ Ã�Â¿Ã‘â‚¬Ã�ÂµÃ�Â¼Ã�ÂµÃ‘ï¿½Ã‘â€šÃ‘ï¿½Ã‘â€š Ã�ÂºÃ�Â¾Ã�Â½Ã‘â€šÃ‘â‚¬Ã�Â¾Ã�Â»Ã�ÂµÃ‘â‚¬Ã�Â¸Ã‘â€šÃ�Âµ Ã�Â·Ã�Â° Ã�Â¿Ã�Â»Ã�Â°Ã‘â€°Ã�Â°Ã�Â½Ã�ÂµÃ‘â€šÃ�Â¾ Ã�Â² Ã‘â€šÃ�Â¾Ã�Â·Ã�Â¸ Ã�Â±Ã‘Å Ã�Â½Ã�Â´Ã‘Å Ã�Â» (Ã�ÂºÃ�Â°Ã‘â€šÃ�Â¾ Ã�Â½Ã�Â°Ã�Â¿Ã‘â‚¬Ã�Â¸Ã�Â¼Ã�ÂµÃ‘â‚¬ RecurringPaymentController).
 */
class SubscriptionController extends Controller
{
    public function subscribeAction( $planId, Request $request )
    {
        return $this->redirect($this->generateUrl('a_payment_paypal_express_checkout_prepare_recurring_payment_agreement', array(
            'planId' => $planId,
        )));
    }
    
    public function createAction( $paymentId, Request $request )
    {
        $pdr = $this->getDoctrine()->getRepository( Payment::class );
        $paymentDetails = $pdr->find($paymentId);
        if(!$paymentDetails) {
            throw new Exception('Error with payment.');
        }

        $subscription = new UserSubscription();
        $subscription->setPaymentDetails($paymentDetails);
        $subscription->setDate( new \DateTime() );
        $subscription->setPlan( $this->getDoctrine()->getRepository( PackagePlan::class )->find( $paymentId ) );
        
        $activity   = new UserActivity();
        $activity->setUser( $this->getUser() );
        $activity->setDate( new \DateTime() );
        $activity->setActivity( 
            sprintf( 'User subscribed to the "%s". Payed with "%s"', 
                $paymentDetails->getPackagePlan()->getDescription(), 
                $paymentDetails->getPaymentMethod() 
            )
        );
        
        $user = $this->getUser();
        $user->setSubscription( $subscription );
        
        $em = $this->getDoctrine()->getManager();
        $em->persist( $subscription );
        $em->persist( $activity );
        $em->flush();
        
        return $this->redirect($this->generateUrl('ia_users_profile_show'));
    }
    
    public function cancelAction( $subscriptionId, Request $request )
    {
        
    }
}

