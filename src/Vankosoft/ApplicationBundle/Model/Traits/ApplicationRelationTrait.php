<?php namespace VS\ApplicationBundle\Model\Traits;

use VS\ApplicationBundle\Model\Interfaces\ApplicationInterface;

/**
 * @see \VS\ApplicationBundle\Model\Interfaces\ApplicationRelationInterface
 */
trait ApplicationRelationTrait
{
    /** @var \VS\ApplicationBundle\Model\Interfaces\ApplicationInterface */
    protected $application;
    
    public function getApplication() : ?ApplicationInterface
    {
        return $this->application;
    }
    
    public function setApplication( ?ApplicationInterface $application ) : self
    {
        $this->application  = $application;
        
        return $this;
    }
}
