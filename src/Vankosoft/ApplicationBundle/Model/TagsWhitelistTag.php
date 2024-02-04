<?php namespace Vankosoft\ApplicationBundle\Model;

use Vankosoft\ApplicationBundle\Model\Interfaces\TagsWhitelistTagInterface;
use Vankosoft\ApplicationBundle\Model\Interfaces\TagsWhitelistContextInterface;

class TagsWhitelistTag implements TagsWhitelistTagInterface
{
    /** @var integer */
    protected $id;
    
    /** @var TagsWhitelistContextInterface */
    protected $context;
    
    /** @var string */
    protected $tag;
    
    public function getId()
    {
        return $this->id;
    }
    
    public function getContext(): ?TagsWhitelistContextInterface
    {
        return $this->context;
    }
    
    public function setContext( TagsWhitelistContextInterface $context )
    {
        $this->context  = $context;
        
        return $this;
    }
    
    public function getTag(): ?string
    {
        return $this->tag;
    }
    
    public function setTag( string $tag )
    {
        $this->tag  = $tag;
        
        return $this;
    }
}