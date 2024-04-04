<?php namespace Vankosoft\ApplicationBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Vankosoft\ApplicationBundle\Controller\AbstractCrudController;
use Vankosoft\ApplicationBundle\Controller\TaxonomyHelperTrait;
use Vankosoft\ApplicationBundle\Form\TagsWhitelistContextTagsForm;

class TagsWhitelistContextsController extends AbstractCrudController
{
    use TaxonomyHelperTrait;
    
    protected function customData( Request $request, $entity = null ): array
    {
        $taxonomy   = $this->getTaxonomy( 'vs_application.tags_whitelist_contexts.taxonomy_code' );
        
        if ( $entity && $entity->getTaxon() ) {
            $entity->getTaxon()->setCurrentLocale( $request->getLocale() );
        }
        
        $tagsForm   = $this->classInfo['action'] == 'updateAction' ?
                        $this->createForm( TagsWhitelistContextTagsForm::class, $entity ) :
                        null;
        
        return [
            'taxonomyId'    => $taxonomy ? $taxonomy->getId() : 0,
            'tagsForm'      => $tagsForm->createView(), 
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
            $taxonomy   = $this->get( 'vs_application.repository.taxonomy' )->findByCode(
                $this->getParameter( 'vs_application.tags_whitelist_contexts.taxonomy_code' )
            );
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