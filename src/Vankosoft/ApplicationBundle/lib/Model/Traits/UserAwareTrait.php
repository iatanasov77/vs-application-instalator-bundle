<?php namespace VS\ApplicationBundle\Model\Traits;

use VS\UsersBundle\Model\UserInterface;

/**
 * @see \VS\ApplicationBundle\Model\Interfaces\UserAwareInterface
 */
trait UserAwareTrait
{
    /** @var \VS\UsersBundle\Model\UserInterface */
    protected $createdBy;
    
    /** @var \VS\UsersBundle\Model\UserInterface */
    protected $updatedBy;
    
    /** @var \VS\UsersBundle\Model\UserInterface */
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
