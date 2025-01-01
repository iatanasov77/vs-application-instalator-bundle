<?php namespace Vankosoft\ApplicationBundle\Model\Traits;

use Doctrine\ORM\Mapping as ORM;
use Vankosoft\ApplicationBundle\Model\Interfaces\ApplicationInterface;

/**
 * @see \Vankosoft\ApplicationBundle\Model\Interfaces\ApplicationRelationInterface
 */
trait ApplicationRelationEntity
{
    /**
     * @var \Vankosoft\ApplicationBundle\Model\Interfaces\ApplicationInterface
     * 
     * @ORM\ManyToOne(targetEntity="App\Entity\Application\Application")
     */
    #[ORM\ManyToOne(targetEntity: "App\Entity\Application\Application")]
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
