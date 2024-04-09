<?php namespace Vankosoft\ApplicationBundle\Repository\Traits;

trait TaxonRepositoryTrait
{
    public function find( $id, $lockMode = null, $lockVersion = null ): ?object
    {
        if ( ! \intval( $id ) ) {
            return null;
        }
        
        if( ! is_numeric( $id ) ) {
            return $this->findOneBy( ['code'=>$id] );
        }
        
        return parent::find( $id, $lockMode, $lockVersion );
    }
    
    public function findByTaxonId( $taxonId )
    {
        $allCategories  = $this->findAll();
        foreach ( $allCategories as $cat ) {
            if ( $cat->getTaxon()->getId() == $taxonId ) {
                return $cat;
            }
        }
        
        return null;
    }
    
    public function findByTaxonCode( $code )
    {
        $allCategories  = $this->findAll();
        foreach ( $allCategories as $cat ) {
            if ( $cat->getTaxon()->getCode() == $code ) {
                return $cat;
            }
        }
        
        return null;
    }
    
    public function findBySlug( $slug )
    {
        return $this->findByTaxonCode( $slug );
    }
}