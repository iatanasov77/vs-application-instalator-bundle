<?php namespace Vankosoft\ApplicationBundle\Model\Traits;

use Doctrine\ORM\Mapping as ORM;
use Vankosoft\UsersBundle\Model\Interfaces\UserInterface;

/**
 * @see \Vankosoft\ApplicationBundle\Model\Interfaces\UserAwareInterface
 */
trait UserAwareEntity
{
    /**
     * @var UserInterface
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\UserManagement\User")
     */
    #[ORM\ManyToOne(targetEntity: "App\Entity\UserManagement\User")]
    protected $createdBy;
    
    /**
     * @var UserInterface
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\UserManagement\User")
     */
    #[ORM\ManyToOne(targetEntity: "App\Entity\UserManagement\User")]
    protected $updatedBy;
    
    /**
     * @var UserInterface
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\UserManagement\User")
     */
    #[ORM\ManyToOne(targetEntity: "App\Entity\UserManagement\User")]
    protected $deletedBy;
    
    public function getCreatedBy() : ?UserInterface
    {
        return $this->createdBy;
    }
    
    public function setCreatedBy( ?UserInterface $user ) : self
    {
        $this->createdBy  = $user;
        
        return $this;
    }
    
    public function getUpdatedBy() : ?UserInterface
    {
        return $this->updatedBy;
    }
    
    public function setUpdatedBy( ?UserInterface $user ) : self
    {
        $this->updatedBy  = $user;
        
        return $this;
    }
    
    public function getDeletedBy() : ?UserInterface
    {
        return $this->deletedBy;
    }
    
    public function setDeletedBy( ?UserInterface $user ) : self
    {
        $this->deletedBy  = $user;
        
        return $this;
    }
}
