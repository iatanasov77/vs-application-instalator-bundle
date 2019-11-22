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
        
        $tplVars = array(
            'items' => $er->findAll(),
        );
        return $this->render('IACmsBundle:Pages:index.html.twig', $tplVars);
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
        if( $request->isMethod('POST') ) {
            $form->handleRequest($request);
            if($form->isValid()) {
                // Ãƒï¿½Ã¢â‚¬â„¢Ãƒï¿½Ã‚Â°Ãƒï¿½Ã‚Â»Ãƒï¿½Ã‚Â¸Ãƒï¿½Ã‚Â´Ãƒï¿½Ã‚Â°Ãƒâ€˜Ã¢â‚¬Â Ãƒï¿½Ã‚Â¸Ãƒâ€˜Ã¯Â¿Â½Ãƒâ€˜Ã¢â‚¬Å¡Ãƒï¿½Ã‚Â° Ãƒï¿½Ã‚Â³Ãƒâ€˜Ã…Â Ãƒâ€˜Ã¢â€šÂ¬Ãƒï¿½Ã‚Â¼Ãƒï¿½Ã‚Â¸
            }
            
            $em = $this->getDoctrine()->getManager();
            $em->persist($form->getData());
            $em->flush();
            
            return $this->redirect($this->generateUrl('ia_web_content_thief_fieldsets_list'));
        }

        return $this->render( 'IACmsBundle:Pages:update.html.twig', [
            'form' => $form->createView(),
            'item' => $oPage
        ]);
    }
}
    