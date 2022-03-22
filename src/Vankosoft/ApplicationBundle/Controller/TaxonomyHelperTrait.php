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
    
    protected function createTaxonCode( $taxonName )
    {
        $code           = $this->get( 'vs_application.slug_generator' )->generate( $taxonName );
        $useThisCode    = $code;
        $i              = 0;
        
        while( $this->get( 'vs_application.repository.taxon' )->findByCode( $useThisCode ) ) {
            $i++;
            $useThisCode    = $code . '-' . $i;
        }
        
        return $useThisCode;
    }
}
