<?php namespace IA\CmsBundle\Controller;

use Sylius\Bundle\ResourceBundle\Controller\ResourceController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class TaxonomyTermsController extends ResourceController
{
 
    public function createAction(Request $request) : Response
    {
        $this->factory = new TermFactory($this->getDoctrine()->getRepository('IATaxonomyBundle:Vocabulary'));
        return parent::createAction($request);
    }
    
}
