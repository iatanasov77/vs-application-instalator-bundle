<?php namespace Vankosoft\ApplicationBundle\Model\Traits;

use Vankosoft\ApplicationBundle\Model\Interfaces\ApplicationInterface;

/**
 * @see \Vankosoft\ApplicationBundle\Model\Interfaces\ApplicationRelationInterface
 */
trait ApplicationRelationTrait
{
    /** @var \Vankosoft\ApplicationBundle\Model\Interfaces\ApplicationInterface */
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
