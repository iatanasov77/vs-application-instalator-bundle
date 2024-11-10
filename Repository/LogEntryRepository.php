<?php namespace Vankosoft\ApplicationBundle\Repository;

use Gedmo\Loggable\Entity\Repository\LogEntryRepository as BaseRepository;
use Gedmo\Tool\Wrapper\EntityWrapper;

use Sylius\Component\Resource\Repository\RepositoryInterface;
use Sylius\Bundle\ResourceBundle\Doctrine\ORM\ResourceRepositoryTrait;

/**
 * The LogEntryRepository has some useful functions
 * to interact with log entries.
 *
 * @author Gediminas Morkevicius <gediminas.morkevicius@gmail.com>
 * @license MIT License (http://www.opensource.org/licenses/mit-license.php)
 */
class LogEntryRepository extends BaseRepository implements RepositoryInterface
{
    use ResourceRepositoryTrait;
    
    /**
     * @NOTE: Copied from Base Repository
     * 
     * Reverts given $entity to $revision by
     * restoring all fields from that $revision.
     * After this operation you will need to
     * persist and flush the $entity.
     *
     * @param object  $entity
     * @param integer $version
     *
     * @throws \Gedmo\Exception\UnexpectedValueException
     *
     * @return void
     */
    public function revertByLocale( $entity, $version = 1, $locale = 'en_US' )
    {
        $wrapped        = new EntityWrapper($entity, $this->_em);
        $objectMeta     = $wrapped->getMetadata();
        $objectClass    = $objectMeta->name;
        $meta           = $this->getClassMetadata();
        
        $dql = "SELECT log FROM {$meta->name} log";
        $dql .= " WHERE log.objectId = :objectId";
        $dql .= " AND log.objectClass = :objectClass";
        $dql .= " AND log.locale = :locale";
        $dql .= " AND log.version <= :version";
        $dql .= " ORDER BY log.version ASC";
        
        $objectId   = (string) $wrapped->getIdentifier();
        $q          = $this->_em->createQuery( $dql );
        $q->setParameters( compact( 'objectId', 'objectClass', 'locale', 'version' ) );
        $logs = $q->getResult();
        
        if ( $logs ) {
            $config = $this->getLoggableListener()->getConfiguration( $this->_em, $objectMeta->name );
            $fields = $config['versioned'];
            $filled = false;
            while ( ( $log = array_pop( $logs ) ) && !$filled ) {
                if ( $data = $log->getData() ) {
                    foreach ( $data as $field => $value ) {
                        if ( in_array( $field, $fields ) ) {
                            $this->mapValue( $objectMeta, $field, $value );
                            $wrapped->setPropertyValue( $field, $value );
                            unset( $fields[array_search( $field, $fields )] );
                        }
                    }
                }
                $filled = count( $fields ) === 0;
            }
            /*if (count($fields)) {
             throw new \Gedmo\Exception\UnexpectedValueException('Could not fully revert the entity to version: '.$version);
             }*/
        } else {
            throw new \Gedmo\Exception\UnexpectedValueException( 'Could not find any log entries under version: ' . $version );
        }
    }
}
