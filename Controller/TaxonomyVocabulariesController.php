<?php namespace IA\CmsBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
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
    
    public function createAction( Request $request ): Response
    {
        $vocabulary = new TaxonomyVocabulary();
        $form       = $this->createForm( TaxonomyVocabularyForm::class, $vocabulary );
        
        if( $form->isSubmitted() ) {
            
        }
        
        return $this->render( 'IAUsersBundle:UsersCrud:create.html.twig', [
            'form'      => $form->createView(),
            'item'      => $user
        ]);
    }
    
    public function updateAction( Request $request ) : Response
    {
        
    }
}
