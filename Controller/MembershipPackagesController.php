<?php namespace IA\UsersBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sylius\Bundle\ResourceBundle\Controller\ResourceController;
use Spatie\Url\Url as SpatieUrl;

use IA\UsersBundle\Entity\Package;
use IA\UsersBundle\Form\PackageFormType;
use App\Component\Url;

class MembershipPackagesController extends ResourceController
{
    
    public function createAction( Request $request ): Response
    {
        $tplVars    = $this->processRequest( $request );
        return $this->render('IAUsersBundle:Packages:create.html.twig', $tplVars);
    }
    
    public function updateAction( Request $request ) : Response
    {
        $tplVars    = $this->processRequest( $request );
        return $this->render( 'IAUsersBundle:Packages:update.html.twig', $tplVars );
    }
    
    protected function processRequest( Request $request )
    {
        //$id = Url::ProjectsUrlGetId();
        $id = $this->getId();
        
        $er = $this->getDoctrine()->getRepository( 'IA\UsersBundle\Entity\Package' );
        $oPackage = $id ? $er->find($id) : new Package();
        
        $form = $this->createForm(PackageFormType::class, $oPackage, ['data' => $oPackage]);
        
        //if($form->isSubmitted()) {
        if($request->isMethod('POST') || $request->isMethod('PUT')) {
            $form->handleRequest($request);
            $em = $this->getDoctrine()->getManager();
            $em->persist($form->getData());
            $em->flush();
            
            return $this->redirect($this->generateUrl('ia_paid_membership_packages_index'));
        }
        
        $tplVars = array(
            'form'          => $form->createView(),
            'item'          => $oPackage,
        );
        return $tplVars;
    }
    
    protected function getId()
    {
        $url = SpatieUrl::fromString( $_SERVER['REQUEST_URI'] );
        return intval( $url->getSegment( 3 ) );
    }
}

