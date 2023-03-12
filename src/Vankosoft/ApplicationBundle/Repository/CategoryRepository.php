<?php namespace Vankosoft\ApplicationBundle\Repository;

use Sylius\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository;

/**
 * Base Repository Use For Category Resources
 * In this moment used in PaymentBundle ProductCategoryRepository
 */
class CategoryRepository extends EntityRepository
{
    public function find( $id ): ?object
    {
        if( ! is_numeric( $id ) ) {
            return $this->findOneBy( ['slug' => $id] );
        }
        
        return parent::find( $id );
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
}
