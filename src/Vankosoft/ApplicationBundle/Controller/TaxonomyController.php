<?php namespace VS\ApplicationBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use VS\ApplicationBundle\Controller\AbstractCrudController;
use VS\ApplicationBundle\Component\Slug;

class TaxonomyController extends AbstractCrudController
{ 
    protected function prepareEntity( &$entity, &$form, Request $request )
    {
        $formData   = $request->request->get( 'taxonomy_form' );
        
        $entity->setCode( Slug::generate( $formData['name'] ) );
        
        if ( ! $entity->getRootTaxon() ) {
            $entity->setRootTaxon( $this->createRootTaxon( $entity, $request->getLocale() ) );
        }
    }
    
    protected function createRootTaxon( $taxonomy, $requestLocale )
    {
        $locale     = $taxonomy->getLocale() ?: $requestLocale;
        $rootTaxon  = $this->get( 'vs_application.factory.taxon' )->createNew();
        
        // @NOTE Force generation of slug
        $rootTaxon->setCurrentLocale( $locale );
        $rootTaxon->getTranslation()->setName( $taxonomy->getName() );
        $rootTaxon->getTranslation()->setDescription( 'Root taxon of Taxonomy: "' . $taxonomy->getName() . '"' );
        
        $slug   = Slug::generate( $taxonomy->getName() );
        $rootTaxon->setCode( $slug );
        $rootTaxon->getTranslation()->setSlug( $slug );
        
        $rootTaxon->getTranslation()->setTranslatable( $rootTaxon );
        
        return $rootTaxon;
    }
}
