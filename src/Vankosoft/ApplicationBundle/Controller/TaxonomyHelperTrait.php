<?php namespace Vankosoft\ApplicationBundle\Controller;

trait TaxonomyHelperTrait
{
    protected function createTaxon( $name, $locale, $parent, $taxonomyId )
    {
        $taxon  = $this->get( 'vs_application.factory.taxon' )->createNew();
        
        $taxon->setCurrentLocale( $locale );
        $taxon->setName( $name );
        
        $slug   = $this->get( 'vs_application.slug_generator' )->generate( $name );
        $taxon->setCode( $slug );
        $taxon->setSlug( $slug );
        
        if ( ! $parent ) {
            $taxonomy   = $this->get( 'vs_application.repository.taxonomy' )->find( $taxonomyId );
            $parent     = $taxonomy->getRootTaxon();
        }
        $taxon->setParent( $parent );
        
        return $taxon;
    }
}
