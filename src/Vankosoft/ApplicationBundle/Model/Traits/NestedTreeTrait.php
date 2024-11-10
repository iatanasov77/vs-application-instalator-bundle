<?php namespace Vankosoft\ApplicationBundle\Model\Traits;

use Doctrine\Common\Collections\Collection;
use Vankosoft\ApplicationBundle\Model\Interfaces\NestedTreeInterface;

trait NestedTreeTrait
{
    /** @var int */
    protected $root;
    
    /** @var int */
    protected $level;
    
    /** @var int */
    protected $left;
    
    /** @var int */
    protected $right;
    
    /** @var NestedTreeInterface|null */
    protected $parent;
    
    /** @var Collection<int, NestedTreeInterface> */
    protected $children;
    
    public function getRoot(): ?self
    {
        return $this->root;
    }
    
    public function getParent(): ?self
    {
        return $this->parent;
    }
    
    public function setParent(self $parent = null): void
    {
        $this->parent = $parent;
    }
    
    public function getChildren(): Collection
    {
        return $this->children;
    }
    
    public function hasChild(NestedTreeInterface $entity): bool
    {
        return $this->children->contains($entity);
    }
    
    public function hasChildren(): bool
    {
        return !$this->children->isEmpty();
    }
    
    public function addChild(NestedTreeInterface $entity): void
    {
        if (!$this->hasChild($entity)) {
            $this->children->add($entity);
        }
        
        if ($this !== $entity->getParent()) {
            $entity->setParent($this);
        }
    }
    
    public function removeChild(NestedTreeInterface $entity): void
    {
        if ($this->hasChild($entity)) {
            $entity->setParent(null);
            
            $this->children->removeElement($entity);
        }
    }
}