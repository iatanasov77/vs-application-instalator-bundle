<?php namespace Vankosoft\ApplicationBundle\Model\Traits;

use Vankosoft\UsersBundle\Model\Interfaces\UserInterface;

/**
 * @see \Vankosoft\ApplicationBundle\Model\Interfaces\UserAwareInterface
 */
trait UserAwareTrait
{
    /** @var UserInterface */
    protected $createdBy;
    
    /** @var UserInterface */
    protected $updatedBy;
    
    /** @var UserInterface */
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
