<?php namespace Vankosoft\ApplicationBundle\Repository;

use Doctrine\ORM\Query\ResultSetMappingBuilder;
use Gedmo\Tree\Entity\Repository\NestedTreeRepository;

class TaxonRepository extends NestedTreeRepository
{
    public function getTaxonsAsArray( $rootTaxonId, $parentId )
    {
        $em     = $this->getEntityManager();
        $sql    = "
            SELECT t.*, tt.locale, tt.slug, tt.name, tt.description
            FROM VSAPP_Taxons t 
            LEFT JOIN VSAPP_TaxonTranslations tt ON tt.translatable_id = t.id 
            WHERE tree_root=:rootTaxonId AND parent_id IS NOT NULL
        ";
        $params['rootTaxonId']  = $rootTaxonId;
        
        if ( $parentId ) {
            $sql    .= " AND parent_id = :parentId";
            $params['parentId'] = $parentId;
        }
        
        $statement = $em->getConnection()->prepare( $sql );
        $statement->execute( $params); 
        
        return $statement->fetchAll();
    }
    
    /*
     * @NOTE Native SQL: https://www.doctrine-project.org/projects/doctrine-orm/en/2.6/reference/native-sql.html
     *      ------------
     */
    public function getTaxons( $rootTaxon )
    {
        $em     = $this->getEntityManager();
        $rsm    = new ResultSetMappingBuilder( $em );
        
        $rsm->addRootEntityFromClassMetadata( get_class( $rootTaxon ), 'tt' );
        $sql = "
            SELECT * FROM VSAPP_Taxons WHERE tree_root=?
        ";
        $query = $em->createNativeQuery( $sql, $rsm );
        $query->setParameter( 1, $rootTaxon->getId() );
        
        return $query->getResult();
    }
    
    public function getTaxonsWithQueryBuilder( $rootTaxon, $level = null )
    {
        $qb = $this->createQueryBuilder( 'tt' )
                    ->select( 'tt' )
                    ->where( 'tt.root = :rootTaxon' )
                    ->setParameter( 'rootTaxon', $rootTaxon );
        
        if ( $level !== null ) {
            $qb->andWhere( 'tt.level = :level' )
                ->setParameter( 'level', $level );
        }
            
        return $qb->getQuery()->getResult();
    }
}
