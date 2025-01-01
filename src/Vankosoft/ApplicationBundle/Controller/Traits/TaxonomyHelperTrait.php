<?php namespace Vankosoft\ApplicationBundle\Controller\Traits;

use Sylius\Component\Taxonomy\Model\TaxonTranslationInterface;
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
     * 
     * @TODO Need Reorder Method Params as 'createTranslation' method
     * @TODO Need Replace parameter $taxonomyId with $taxonomy to reduce sql queries
     * 
     * @param string $name
     * @param string $locale
     * @param TaxonInterface|null $parent
     * @param int $taxonomyId
     * @param string|null $description
     * @return TaxonInterface
     */
    protected function createTaxon( string $name, string $locale, ?TaxonInterface $parent, int $taxonomyId, $description = null ): TaxonInterface
    {
        $taxon  = $this->get( 'vs_application.factory.taxon' )->createNew();
        
        $taxon->setCurrentLocale( $locale );
        $taxon->setFallbackLocale( 'en_US' );
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
    
    /**
     * 
     * @param string $taxonName
     * @return string
     */
    protected function createTaxonCode( string $taxonName ): string
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
    
    /**
     * 
     * @param TaxonInterface $taxon
     * @param string $locale
     * @param string $name
     * @param string|null $description
     * @return TaxonTranslationInterface
     */
    protected function createTranslation( TaxonInterface $taxon, string $locale, string $name, $description = null ): TaxonTranslationInterface
    {
        $translation    = $taxon->createNewTranslation();
        
        $translation->setLocale( $locale );
        $translation->setName( $name );
        $translation->setDescription( $description );
        $translation->setSlug( $this->get( 'vs_application.slug_generator' )->generate( $name ) );
        
        return $translation;
    }
    
    /**
     * 
     * @param bool $paginated
     * @return array
     */
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
