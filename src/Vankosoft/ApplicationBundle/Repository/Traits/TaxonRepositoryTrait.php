<?php namespace Vankosoft\ApplicationBundle\Repository\Traits;

use Vankosoft\ApplicationBundle\Model\Interfaces\TaxonDescendentInterface;
use Vankosoft\ApplicationBundle\Model\Taxon;

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
    
    public function getPathAsString( TaxonDescendentInterface $category ): string
    {
        $categoryPath       = '';
        //$taxonRepository    = $this->_em->getRepository( Taxon::class );
        //$taxonRepository    = $this->container->get( 'vs_application.repository.taxon' );
        $taxonRepository    = $this->_em->getRepository( get_class( $category->getTaxon() ) );
        
        $categoryPathArray  = $taxonRepository->getPath( $category->getTaxon() );
        \array_shift( $categoryPathArray );
        foreach ( $categoryPathArray as $key => $pathPart ) {
            $categoryPath   .= $pathPart->getName();
            if ( $key !== \array_key_last( $categoryPathArray ) ) {
                $categoryPath   .= ' / ';
            }
        }
        
        return $categoryPath;
    }
    
    public function getPathAsPath( TaxonDescendentInterface $category ): string
    {
        $categoryPath       = '';
        //$taxonRepository    = $this->_em->getRepository( Taxon::class );
        //$taxonRepository    = $this->container->get( 'vs_application.repository.taxon' );
        $taxonRepository    = $this->_em->getRepository( get_class( $category->getTaxon() ) );
        
        $categoryPathArray  = $taxonRepository->getPath( $category->getTaxon() );
        \array_shift( $categoryPathArray );
        foreach ( $categoryPathArray as $key => $pathPart ) {
            $categoryPath   .= $pathPart->getCode();
            if ( $key !== \array_key_last( $categoryPathArray ) ) {
                $categoryPath   .= '/';
            }
        }
        
        return $categoryPath;
    }
    
    public function findByPath( string $path ): TaxonDescendentInterface
    {
        $pathParts  = \explode( '/', $path );
        $slug       = \end( $pathParts );
        $category   = $this->findBySlug( $slug );
        
        return $category;
    }
}