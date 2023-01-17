<?php namespace Vankosoft\ApplicationBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Vankosoft\ApplicationBundle\Controller\AbstractCrudController;
use Vankosoft\ApplicationBundle\Component\Slug;

class TaxonomyController extends AbstractCrudController
{
    protected function customData( Request $request, $entity = null ): array
    {
        $taxonTranslations   = $this->classInfo['action'] == 'updateAction' ? $this->getTaxonTranslations() : [];
        
        return [
            'taxonTranslations' => $taxonTranslations,
        ];
    }
    
    protected function prepareEntity( &$entity, &$form, Request $request )
    {
        $taxonomyName   = $request->request->get( 'name' );
        
        $entity->setCode( $this->get( 'vs_application.slug_generator' )->generate( $taxonomyName ) );
        
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
        
        $slug   = $this->get( 'vs_application.slug_generator' )->generate( $taxonomy->getName() );
        $rootTaxon->setCode( $slug );
        $rootTaxon->getTranslation()->setSlug( $slug );
        
        $rootTaxon->getTranslation()->setTranslatable( $rootTaxon );
        
        return $rootTaxon;
    }
    
    private function getTaxonTranslations()
    {
        $translations   = [];
        $taxonsRepo   = $this->get( 'vs_application.repository.taxon' );
        
        foreach ( $taxonsRepo->findAll() as $taxon ) {
            $translations[$taxon->getId()] = $taxon->getTranslations()->getKeys();
        }
        
        return $translations;
    }
}
