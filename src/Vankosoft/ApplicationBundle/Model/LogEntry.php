<?php namespace Vankosoft\ApplicationBundle\Model;

use Gedmo\Loggable\Entity\MappedSuperclass\AbstractLogEntry;
use Vankosoft\ApplicationBundle\Model\Interfaces\LogEntryInterface;

class LogEntry extends AbstractLogEntry implements LogEntryInterface
{
    /**
     * @var string $locale
     */
    protected $locale;
    
    /**
     * Get locale
     *
     * @return string
     */
    public function getLocale()
    {
        return $this->locale;
    }
    
    /**
     * Set locale
     *
     * @param string $locale
     */
    public function setLocale( $locale )
    {
        $this->locale = $locale;
    }
}
