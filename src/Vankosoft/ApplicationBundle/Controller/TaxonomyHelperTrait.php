<?php namespace Vankosoft\ApplicationBundle\Controller;

use Vankosoft\ApplicationBundle\Model\Interfaces\TaxonomyInterface;
use Vankosoft\ApplicationBundle\Model\Interfaces\TaxonInterface;
use Vankosoft\ApplicationBundle\Component\Exception\TaxonomyNotFoundException;

trait TaxonomyHelperTrait
{
    /**
     * 
     * @param string $taxonomyCodeParameter
     * @throws TaxonomyNotFoundException
     * @return TaxonomyInterface
     */
    protected function getTaxonomy( string $taxonomyCodeParameter ): TaxonomyInterface
    {
        $taxonomyCode   = $this->getParameter( $taxonomyCodeParameter );
        $taxonomy       = $this->get( 'vs_application.repository.taxonomy' )->findByCode( $taxonomyCode );
        if ( ! $taxonomy ) {
            $message    = \sprintf( 'Taxonomy with code "%s" Not Exists. Please create it before!', $taxonomyCode );
            throw new TaxonomyNotFoundException( $message );
        }
        
        return $taxonomy;
    }
    
    /**
     * @TODO Need Reorder Method Params as 'createTranslation' method
     * @TODO Need Replace parameter $taxonomyId with $taxonomy to reduce sql queries
     */
    protected function createTaxon( string $name, string $locale, ?TaxonInterface $parent, int $taxonomyId, $description = null )
    {
        $taxon  = $this->get( 'vs_application.factory.taxon' )->createNew();
        
        $taxon->setCurrentLocale( $locale );
        $taxon->setName( $name );
        
        $slug   = $this->createTaxonCode( $name );
        
        $taxon->setCode( $slug );
        $taxon->setSlug( $slug );
        
        if ( ! $parent ) {
            $taxonomy   = $this->get( 'vs_application.repository.taxonomy' )->find( $taxonomyId );
            $parent     = $taxonomy->getRootTaxon();
        }
        $taxon->setParent( $parent );
        
        $defaultLocale  = $this->getParameter( 'locale' );
        if ( $locale != $defaultLocale ) {
            $translation    = $this->createTranslation( $taxon, $defaultLocale, $name, $description );
            $taxon->addTranslation( $translation );
        }
        
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
    
    protected function createTranslation( $taxon, $locale, $name, $description = null )
    {
        $translation    = $taxon->createNewTranslation();
        
        $translation->setLocale( $locale );
        $translation->setName( $name );
        $translation->setDescription( $description );
        $translation->setSlug( $this->get( 'vs_application.slug_generator' )->generate( $name ) );
        
        return $translation;
    }
    
    protected function getTranslations( bool $paginated = true ): array
    {
        $locales        = $this->get( 'vs_application.repository.locale' )->findAll();
        
        $resources      = $paginated ? $this->resources->getCurrentPageResults() : $this->getRepository()->findAll();
        $translations   = [];
        foreach ( $resources as $category ) {
            foreach( $locales as $locale ) {
                $category->getTaxon()->getTranslation( $locale->getCode() );
            }
            
            $translations[$category->getId()] = $category->getTaxon()->getExistingTranslations();
        }
        //echo "<pre>"; var_dump( $translations ); die;
        
        return $translations;
    }
}
