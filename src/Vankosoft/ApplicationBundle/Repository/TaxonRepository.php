<?php namespace Vankosoft\ApplicationBundle\Repository;

use Doctrine\ORM\Query\ResultSetMappingBuilder;
use Gedmo\Tree\Entity\Repository\NestedTreeRepository;

use Sylius\Component\Resource\Repository\RepositoryInterface;
use Sylius\Bundle\ResourceBundle\Doctrine\ORM\ResourceRepositoryTrait;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\ClassMetadata;
use Gedmo\Exception\UnexpectedValueException;

class TaxonRepository extends NestedTreeRepository implements RepositoryInterface
{
    use ResourceRepositoryTrait;
    
    protected string $rootDir;
    protected ?\Exception $exception;
    
    public function __construct( EntityManagerInterface $em, ClassMetadata $class )
    {
        $this->exception    = null;
        try {
            parent::__construct( $em, $class );
        } catch ( UnexpectedValueException $e ) {
            $this->exception    = $e;
        }
    }
    
    public function setRootDir( string $rootDir )
    {
        $this->rootDir	= $rootDir;
    }
    
    public function throwException( bool $throwException )
    {
        if( $this->exception ) {
            file_put_contents( $this->rootDir . '/var/dumpTaxonRepositoryException', $this->exception->getMessage() . "\n\n" . $this->exception->getTraceAsString() );
            if ( $throwException ) {
                throw $this->exception;
            }
        }
    }
    
    public function findByCode( $code )
    {
        return $this->findOneBy( ['code' => $code] );
    }
    
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
        foreach ( $params as $key => $val ) {
            $statement->bindParam( $key, $val );
        }
        
        return $statement->executeQuery()->fetchAllAssociative();
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
