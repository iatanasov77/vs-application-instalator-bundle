<?php

namespace IA\CmsBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sylius\Bundle\ResourceBundle\Controller\ResourceController;

use IA\CmsBundle\Entity\Page;
use IA\CmsBundle\Form\PageForm;

class PagesController extends ResourceController
{
    public function listAction()
    {
        $er = $this->getDoctrine()->getRepository( 'IA\CmsBundle\Entity\Page' );
        
        return $this->render( '@IACms/Pages/index.html.twig', [
            'items' => $er->findAll(),
        ]);
    }
    
    public function createAction( Request $request ) : Response
    {
        return $this->editAction( 0, $request );
    }
    
    public function updateAction( Request $request ) : Response
    {
        return $this->editAction( 1, $request );
    }
    
    public function editAction( $id, Request $request )
    {
        $er = $this->getDoctrine()->getRepository( 'IA\CmsBundle\Entity\Page' );
        $oPage = $id ? $er->findOneBy(array('id' => $id)) : new Page();
        
        $form = $this->createForm( PageForm::class, $oPage );
        
        $form->handleRequest( $request );
        if( $form->isSubmitted() ) { // && $form->isValid()
            $em = $this->getDoctrine()->getManager();
            $em->persist( $form->getData() );
            $em->flush();
            
            return $this->redirect($this->generateUrl( 'ia_cms_pages_index' ) );
        }

        return $this->render( '@IACms/Pages/update.html.twig', [
            'form' => $form->createView(),
            'item' => $oPage
        ]);
    }
}
    