<?php namespace IA\UsersBundle\Controller;

use Sylius\Bundle\ResourceBundle\Controller\ResourceController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Spatie\Url\Url as SpatieUrl;

use App\Component\Url;
use IA\UsersBundle\Entity\Plan;
use IA\UsersBundle\Form\PlanFormType;

class MembershipPlansController extends ResourceController
{
    
    public function createAction( Request $request ): Response
    {
        $tplVars    = $this->processRequest( $request );
        return $this->render('IAUsersBundle:Plans:create.html.twig', $tplVars);
    }

    public function updateAction( Request $request ): Response
    {
        $tplVars    = $this->processRequest( $request );
        return $this->render('IAUsersBundle:Plans:update.html.twig', $tplVars);
    }
    
    protected function processRequest( Request $request )
    {
        //$id = Url::ProjectsUrlGetId();
        $id = $this->getId();
        
        $er = $this->getDoctrine()->getRepository( 'IA\UsersBundle\Entity\Plan' );
        $oPlan = $id ? $er->find($id) : new Plan();
        
        $form = $this->createForm(PlanFormType::class, $oPlan, ['data' => $oPlan]);
        
        //if($form->isSubmitted()) {
        if($request->isMethod('POST') || $request->isMethod('PUT')) {
            $form->handleRequest($request);
            $em = $this->getDoctrine()->getManager();
            $em->persist($form->getData());
            $em->flush();
            
            return $this->redirect($this->generateUrl('ia_paid_membership_plans_index'));
        }
        
        $tplVars = array(
            'form'          => $form->createView(),
            'item'          => $oPlan,
        );
        
        return $tplVars;
    }
    
    protected function getId()
    {
        $url = SpatieUrl::fromString( $_SERVER['REQUEST_URI'] );
        return intval( $url->getSegment( 3 ) );
    }
}
