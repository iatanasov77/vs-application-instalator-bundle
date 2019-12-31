<?php namespace IA\CmsBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

use IA\CmsBundle\Entity\TaxonomyVocabulary;

class TaxonomyTermsController extends Controller
{
    public function terms( Request $request )
    {
        $er         = $this->getDoctrine()->getRepository( TaxonomyVocabulary::class );
        $vocabulary = $er->find( $request->attributes->get( 'id' ) );
        
        return new JsonResponse( $this->gtreetableData( $vocabulary ) );
    }
    
    protected function gtreetableData( $vocabulary )
    {
        $terms      = $vocabulary->getTerms();
        //var_dump( $terms->getValues() ); die;
        
        // Example
        $data = [
            'nodes' => [
                (object)[
                    "id" => "1",
                    "name" => "node name",
                    "level" => "1",
                    "type" => "default"
                ],
                (object)[
                    "id" => "2",
                    "name" => "node name 2",
                    "level" => "2",
                    "parent" => 1,
                    "type" => "default"
                ]
            ]
        ];
        
        return $data;
    }
}
