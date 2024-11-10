<?php namespace Vankosoft\ApplicationBundle\EventSubscriber;

use Gedmo\Loggable\LoggableListener as BaseLoggableListener;
use Gedmo\Loggable\Mapping\Event\LoggableAdapter;
use Gedmo\Loggable\Entity\MappedSuperclass\AbstractLogEntry;
use Gedmo\Tool\Wrapper\AbstractWrapper;
use Gedmo\Translatable\Mapping\Event\Adapter\ORM as TranslatableOrmAdapter;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Common\EventArgs;

use Vankosoft\ApplicationBundle\EventSubscriber\Mapping\Event\Adapter\ORM as LoggableOrmAdapter;

class LoggableListener extends BaseLoggableListener
{
    /** @var string $defaultLocale */
    private $defaultLocale;
    
    /** @var TranslatableOrmAdapter $transEa */
    private $transEa;
    
    public function __construct( $defaultLocale )
    {
        $this->defaultLocale    = $defaultLocale;
        
        parent::__construct();
    }
    
    /**
     * Looks for loggable objects being inserted or updated
     * for further processing
     * 
     * {@inheritDoc}
     * @see \Gedmo\Loggable\LoggableListener::onFlush(EventArgs $eventArgs)
     */
    public function onFlush( EventArgs $eventArgs ): void
    {
        $this->transEa  = new TranslatableOrmAdapter();
        $this->transEa->setEventArgs( $eventArgs );
        
        $this->logEa    = new LoggableOrmAdapter( $this->defaultLocale );
        $this->logEa->setEventArgs( $eventArgs );
        
        parent::onFlush( $eventArgs );
    }
    
    /**
     * {@inheritDoc}
     */
    /*
    protected function getNamespace()
    {
        return __NAMESPACE__;
    }
    */
    
    /**
     * Create a new Log instance with locale
     * 
     * {@inheritDoc}
     * @see \Gedmo\Loggable\LoggableListener::createLogEntry()
     */
    protected function createLogEntry( $action, $object, LoggableAdapter $ea ): ?AbstractLogEntry
    {
        // Default Adapter not used. Using custom adapter $this->logEa;
        $ea = $this->logEa;
        
        $om         = $this->logEa->getObjectManager();
        $wrapped    = AbstractWrapper::wrap( $object, $om );
        $meta       = $wrapped->getMetadata();
        
        // Filter embedded documents
        if ( isset( $meta->isEmbeddedDocument ) && $meta->isEmbeddedDocument ) {
            return null;
        }
        
        if ( $config = $this->getConfiguration( $om, $meta->name ) ) {
            if ( $object->getTranslatableLocale() ) {
                $this->logEa->setLocale( $object->getTranslatableLocale() );
            }
            
            if ( $action !== self::ACTION_CREATE ) {
                $this->submitTranslations( $meta->name, $object, $om );
            }
            
            $logEntryClass  = $this->getLogEntryClass( $this->logEa, $meta->name );
            $logEntryMeta   = $om->getClassMetadata( $logEntryClass );
            /** @var \Gedmo\Loggable\Entity\LogEntry $logEntry */
            $logEntry   = $logEntryMeta->newInstance();
           
            $locale     = $object->getTranslatableLocale() ?: $this->defaultLocale;
            $logEntry->setLocale( $locale );
            $logEntry->setAction( $action );
            $logEntry->setUsername( $this->username );
            $logEntry->setObjectClass( $meta->name );
            $logEntry->setLoggedAt();
            
            // check for the availability of the primary key
            $uow = $om->getUnitOfWork();
            if ( $action === self::ACTION_CREATE && $this->logEa->isPostInsertGenerator( $meta ) ) {
                $this->pendingLogEntryInserts[spl_object_id( $object )] = $logEntry;
            } else {
                $logEntry->setObjectId( $wrapped->getIdentifier() );
            }
            $newValues = array();
            if ( $action !== self::ACTION_REMOVE && isset( $config['versioned'] ) ) {
                $newValues = $this->getObjectChangeSetData( $this->logEa, $object, $logEntry );
                $logEntry->setData( $newValues );
            }
            
            if( $action === self::ACTION_UPDATE && 0 === count( $newValues ) ) {
                return null;
            }
            
            $version = 1;
            if ( $action !== self::ACTION_CREATE ) {
                $version = $this->logEa->getNewVersion( $logEntryMeta, $object );
                if ( empty( $version ) ) {
                    // was versioned later
                    $version = 1;
                }
            }
            $logEntry->setVersion( $version );
            
            $this->prePersistLogEntry( $logEntry, $object );
            
            $om->persist( $logEntry );
            $uow->computeChangeSet( $logEntryMeta, $logEntry );
            
            return $logEntry;
        }
        
        return null;
    }
    
    private function submitTranslations( $class, $object, ObjectManager $om )
    {
        if ( isset( self::$configurations['Translatable'][$class] ) ) {
            foreach ( self::$configurations['Translatable'][$class]['fields'] as $field ) {
                $this->persistTranslation( $class, $object, $field, $om );
            }
        }
    }
    
    private function persistTranslation( $class, $object, $field, ObjectManager $om )
    {
        $wrapped            = AbstractWrapper::wrap( $object, $om );
        $translationClass   = self::$configurations['Translatable'][$class]['translationClass'];
        $translationMeta    = $om->getClassMetadata( $translationClass );
        
        // set the translated field, take value using reflection
        $content            = $this->transEa->getTranslationValue( $object, $field );
        $locale             = $object->getTranslatableLocale() ?: $this->defaultLocale;
        
        $translation = $this->transEa->findTranslation(
            $wrapped,
            $locale,
            $field,
            $translationClass,
            $class
        );
        
        if ( $translation ) {
            $translation->setContent( $content );
        } else {
            /** @var \Vankosoft\ApplicationBundle\Model\Translation $translation */
            $translation    = $translationMeta->newInstance();
            
            $translation->setLocale( $locale );
            $translation->setField( $field );
            $translation->setObjectClass( $class );
            $translation->setForeignKey( $object->getId() );
            $translation->setContent( $content );
        }
        
        $om->persist( $translation );
        $om->getUnitOfWork()->computeChangeSet( $translationMeta, $translation );
        //$changeset = $om->getUnitOfWork()->getEntityChangeSet( $translation );
    }
}
