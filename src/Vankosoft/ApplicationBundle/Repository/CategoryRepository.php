<?php namespace Vankosoft\ApplicationBundle\Repository;

use Sylius\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository;

/**
 * Base Repository Use For Category Resources
 * In this moment used in PaymentBundle ProductCategoryRepository
 */
class CategoryRepository extends EntityRepository
{
    public function find( $id, $lockMode = null, $lockVersion = null ): ?object
    {
        if( ! is_numeric( $id ) ) {
            return $this->findOneBy( ['slug' => $id] );
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
}
