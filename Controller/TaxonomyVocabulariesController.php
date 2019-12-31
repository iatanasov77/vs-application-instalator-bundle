<?php namespace IA\CmsBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Sylius\Bundle\ResourceBundle\Controller\ResourceController;

use IA\CmsBundle\Entity\TaxonomyVocabulary;
use IA\CmsBundle\Form\TaxonomyVocabularyForm;

class TaxonomyVocabulariesController extends ResourceController
{
    public function indexAction( Request $request ): Response
    {
        $er = $this->getDoctrine()->getRepository( TaxonomyVocabulary::class );
        
        return $this->render( '@IACms/TaxonomyVocabularies/index.html.twig', [
            'vocabularies' => $er->findAll(),
        ]);
    }
    
    public function createAction( Request $request ) : Response
    {
        $vocabulary = new TaxonomyVocabulary();
        $form       = $this->createForm( TaxonomyVocabularyForm::class, $vocabulary );
        
        $form->handleRequest( $request );
        if( $form->isSubmitted() ) {
            $em = $this->getDoctrine()->getManager();
            $em->persist( $form->getData() );
            $em->flush();
            
            return $this->redirect($this->generateUrl( 'ia_cms_taxonomy_vocabularies_index' ) );
        }
        
        return $this->render( '@IACms/TaxonomyVocabularies/create.html.twig', [
            'form'      => $form->createView(),
            'item'      => $vocabulary
        ]);
    }
    
    public function updateAction( Request $request ) : Response
    {
        $er         = $this->getDoctrine()->getRepository( TaxonomyVocabulary::class );
        $vocabulary = $er->find( $request->attributes->get( 'id' ) );
        
        return $this->editVocabulary( $request, $vocabulary );
    }
    
    protected function editVocabulary(  Request $request, TaxonomyVocabulary $vocabulary ) : Response
    {
        $form       = $this->createForm( TaxonomyVocabularyForm::class, $vocabulary );
        
        $form->handleRequest( $request );
        if( $form->isSubmitted() ) {
            $vocabulary = $form->getData();
            $em         = $this->getDoctrine()->getManager();
            
            $em->persist( $vocabulary );
            $em->flush();
            
            $formData   = $request->request->get( 'taxonomy_vocabulary_form' );
            
            return $this->redirect($this->generateUrl( 'ia_cms_taxonomy_vocabularies_index' ) );
        }
        
        return $this->render( '@IACms/TaxonomyVocabularies/edit.html.twig', [
            'form'      => $form->createView(),
            'item'      => $vocabulary
        ]);
    }
}
