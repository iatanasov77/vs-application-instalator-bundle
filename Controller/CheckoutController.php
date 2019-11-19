<?php namespace IA\UsersBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckoutController extends Controller
{
    public function selectMethodAction($planId, Request $request)
    {
        $paymentMethods = $this->container->getParameter('ia_payment.methods');
        
        $tplVars = array('paymentMethods' => $paymentMethods, 'planId' => $planId);
        return $this->render('IAUsersBundle:Payment:select-method.html.twig', $tplVars);
    }

}


