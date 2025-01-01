<?php namespace Vankosoft\ApplicationBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Vankosoft\ApplicationBundle\Controller\AbstractCrudController;
use Vankosoft\ApplicationBundle\Controller\Traits\TaxonomyHelperTrait;

class WidgetsGroupsController extends AbstractCrudController
{
    use TaxonomyHelperTrait;
    
    protected function customData( Request $request, $entity = null ): array
    {
        $taxonomy   = $this->getTaxonomy( 'vs_application.widgets_groups.taxonomy_code' );
        
        if ( $entity && $entity->getTaxon() ) {
            $entity->getTaxon()->setCurrentLocale( $request->getLocale() );
        }
        
        return [
            'taxonomyId'    => $taxonomy ? $taxonomy->getId() : 0,
        ];
    }
    
    protected function prepareEntity( &$entity, &$form, Request $request )
    {
        $categoryName           = $form['name']->getData();
        
        
        if ( $entity->getTaxon() ) {
            $entityTaxon    = $entity->getTaxon();
            
        } else {
            /*
             * @WORKAROUND Create Taxon If not exists
             */
            $taxonomy   = $this->getTaxonomy( 'vs_application.widgets_groups.taxonomy_code' );
            
            $newTaxon   = $this->createTaxon(
                $categoryName,
                $request->getLocale(),
                null,
                $taxonomy->getId()
            );
            
            $entity->setTaxon( $newTaxon );
        }
    }
}