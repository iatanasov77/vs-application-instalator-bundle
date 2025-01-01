<?php namespace Vankosoft\ApplicationBundle\EventSubscriber\Mapping\Event\Adapter;

use Gedmo\Mapping\Event\Adapter\ORM as BaseAdapterORM;
use Gedmo\Loggable\Mapping\Event\LoggableAdapter;

/**
 * Doctrine event adapter for ORM adapted
 * for Loggable behavior
 */
final class ORM extends BaseAdapterORM implements LoggableAdapter
{
    private $locale;
    
    public function __construct( string $locale)
    {
        $this->locale   = $locale;
    }
    
    public function setLocale( $locale )
    {
        $this->locale   = $locale;
    }
    
    /**
     * {@inheritDoc}
     */
    public function getDefaultLogEntryClass()
    {
        return 'Gedmo\\Loggable\\Entity\\LogEntry';
    }
    
    /**
     * {@inheritDoc}
     */
    public function isPostInsertGenerator($meta)
    {
        return $meta->idGenerator->isPostInsertGenerator();
    }
    
    /**
     * {@inheritDoc}
     */
    public function getNewVersion($meta, $object)
    {
        $em = $this->getObjectManager();
        $objectMeta = $em->getClassMetadata(get_class($object));
        $identifierField = $this->getSingleIdentifierFieldName($objectMeta);
        $objectId = (string) $objectMeta->getReflectionProperty($identifierField)->getValue($object);
        
        $dql = "SELECT MAX(log.version) FROM {$meta->name} log";
        $dql .= " WHERE log.objectId = :objectId";
        $dql .= " AND log.objectClass = :objectClass";
        $dql .= " AND log.locale = :locale";
        
        $q = $em->createQuery($dql);
        $q->setParameters(array(
            'objectId' => $objectId,
            'objectClass' => $objectMeta->name,
            'locale' => $this->locale
        ));
        
        return $q->getSingleScalarResult() + 1;
    }
}
