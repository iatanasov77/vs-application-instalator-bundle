<?php namespace VS\CmsBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sylius\Bundle\ResourceBundle\Controller\ResourceController;

use VS\CmsBundle\Form\PageForm;

class PagesController extends ResourceController
{
    public function indexAction( Request $request ) : Response
    {   
        return $this->render( '@VSCms/Pages/index.html.twig', [
            'items' => $this->getPagesRepository()->findAll(),
        ]);
    }
    
    public function createAction( Request $request ) : Response
    {
        return $this->editAction( 0, $request );
    }
    
    public function updateAction( Request $request ) : Response
    {
        return $this->editAction( $request->attributes->get( 'id' ), $request );
    }
    
    public function editAction( $id, Request $request )
    {
        $er     = $this->getPagesRepository();
        $oPage  = $id ? $er->findOneBy( ['id' => $id] ) : $er->createNew();
        $form   = $this->createForm( PageForm::class, $oPage );
        
        $form->handleRequest( $request );
        if( $form->isSubmitted() ) { // && $form->isValid()
            $em     = $this->getDoctrine()->getManager();
            $entity = $form->getData();
            
            $entity->setTranslatableLocale( $form['locale']->getData() );
            
            $em->persist( $entity );
            $em->flush();
            
            if ($form->getClickedButton() && 'btnApply' === $form->getClickedButton()->getName()) {
                return $this->redirect( $this->generateUrl( 'vs_cms_page_categories_update', ['id' => $entity->getId()] ) );
            } else {
                return $this->redirect( $this->generateUrl( 'vs_cms_pages_index' ) );
            }
        }

        return $this->render( '@VSCms/Pages/update.html.twig', [
            'form' => $form->createView(),
            'item' => $oPage
        ]);
    }
    
    protected function getPagesRepository()
    {
        return $this->get( 'vs_cms.repository.pages' );
    }
}
    