<?php namespace Vankosoft\ApplicationBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Doctrine\Persistence\ManagerRegistry;

use Vankosoft\ApplicationBundle\Controller\Traits\TaxonomyTreeDataTrait;
use Vankosoft\ApplicationBundle\Component\SlugGenerator;
use Vankosoft\ApplicationBundle\Form\TaxonForm;
use Vankosoft\ApplicationBundle\Repository\TaxonomyRepository;
use Vankosoft\ApplicationBundle\Repository\TaxonRepository;

class TaxonomyTaxonsController extends AbstractController
{
    use TaxonomyTreeDataTrait;
    
    /** @var ManagerRegistry */
    protected ManagerRegistry $doctrine;
    
    protected $slugGenerator;
    
    public function __construct(
        ManagerRegistry $doctrine,
        TaxonomyRepository $taxonomyRepository,
        TaxonRepository $taxonRepository,
        SlugGenerator $slugGenerator
    ) {
        $this->doctrine             = $doctrine;
        $this->taxonomyRepository   = $taxonomyRepository;
        $this->taxonRepository      = $taxonRepository;
        $this->slugGenerator        = $slugGenerator;
    }
    
    public function index( Request $request ): Response
    {
        return new Response( "NOT IMPLEMENTED !!!" );
    }
    
    public function editTaxon( $taxonomyId, Request $request ): Response
    {
        $locale                     = $request->getLocale();
        $rootTaxon                  = $this->getTaxonomyRepository()->find( $taxonomyId )->getRootTaxon();
        
        $oTaxon                     = $this->get( 'vs_application.factory.taxon' )->createNew();
        $oTaxon->setCurrentLocale( $locale );
        
        $form   = $this->createForm( TaxonForm::class, $oTaxon, [
            'data'      => $oTaxon,
            'method'    => 'POST',
            'rootTaxon' => $rootTaxon
        ]);
        
        return $this->render( '@VSApplication/Taxon/form/taxon.html.twig', [
            'form'          => $form->createView(),
            'taxonomyId'    => $request->attributes->get( 'taxonomyId' )
        ]);
    }
    
    public function handleTaxon( Request $request ): Response
    {        
        $locale                     = $request->getLocale();
        $form                       = $this->createForm( TaxonForm::class );
        
        if ( $request->isMethod( 'POST' ) ) {
            $parentTaxon    = $this->getTaxonRepository()->find( $_POST['taxon_form']['parentTaxon'] );
            
            $form->submit( $request->request->get( $form->getName() ) );
            
            if ( $form->isSubmitted()  ) { // && $form->isValid()
                $em         = $this->doctrine->getManager();
                $oTaxon     = $form->getData();
                $oTaxon->setParent( $parentTaxon );
                
                // @NOTE Force generation of slug
                $oTaxon->getTranslation( $locale )->setSlug( $this->slugGenerator->generate( $oTaxon->getTranslation()->getName() ) );
                $oTaxon->getTranslation( $locale )->setTranslatable( $oTaxon );
                
                $em->persist( $oTaxon );
                $em->flush();
                
                $taxonomyId = $request->attributes->get( 'taxonomyId' );
                return $this->redirect( $this->generateUrl( 'vs_application_taxonomy_update', ['id' => $taxonomyId] ) );
            }
        }
        
        return new Response( 'The form is not submited properly !!!', 500 );
    }
    
    public function gtreeTableSource( $taxonomyId, Request $request ): Response
    {
        $parentId   = (int)$request->query->get( 'parentTaxonId' );
        
        return new JsonResponse( $this->gtreeTableData( $taxonomyId, $parentId ) );
    }
    
    public function easyuiComboTreeSource( $taxonomyId, Request $request ): Response
    {
        return new JsonResponse( $this->easyuiComboTreeData( $taxonomyId ) );
    }
}
