<?php namespace Vankosoft\ApplicationBundle\Repository;

use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Query\ResultSetMapping;
use Sylius\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository;
use Vankosoft\ApplicationBundle\Model\Interfaces\TagsWhitelistContextInterface;

class TagsWhitelistTagsRepository extends EntityRepository
{
    public function getTags(): array
    {
        $query  = $this->createQueryBuilder( 'vt' )->select( 'vt.tag AS tag' )->getQuery();
        
        $resultMapping = new ResultSetMapping(); 
        $resultMapping->addScalarResult( 'tag', 'tag' );
        
        return $query->setResultSetMapping( $resultMapping )->getSingleColumnResult();
    }
    
    public function updateTags( array $tags, TagsWhitelistContextInterface $context ): void
    {
        //$entityClass    = $this->getClassName();
        $entityClass    = $this->getEntityName();
        //$existingTags   = $this->getTags();
        $existingTags   = $context->getTagsArray();
        
        $newTags        = \array_diff( $tags, $existingTags );
        
        $em             = $this->getEntityManager();
        foreach ( $newTags as $tag ) {
            $oTag   = new $entityClass();
            $oTag->setTag( $tag );
            $oTag->setContext( $context );
            $em->persist( $oTag );
        }
        $em->flush();
    }
}