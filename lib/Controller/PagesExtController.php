<?php  namespace VS\CmsBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use VS\ApplicationBundle\Controller\TaxonomyTreeDataTrait;

class PagesExtController extends Controller
{
    use TaxonomyTreeDataTrait;
    
    public function tree( Request $request ): Response
    {
        return new JsonResponse( $this->easyuiComboTreeData( $this->getParameter( 'vs_cms.page_categories.taxonomy_id' ) ) );
    }
}
