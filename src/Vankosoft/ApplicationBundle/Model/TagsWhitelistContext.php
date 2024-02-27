<?php namespace Vankosoft\ApplicationBundle\Model;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Vankosoft\ApplicationBundle\Model\Traits\TaxonDescendentTrait;
use Vankosoft\ApplicationBundle\Model\Interfaces\TagsWhitelistContextInterface;

class TagsWhitelistContext implements TagsWhitelistContextInterface
{
    use TaxonDescendentTrait;
    
    /** @var integer */
    protected $id;
    
    /** @var Collection|TagsWhitelistTag[] */
    protected $tags;
    
    public function __construct()
    {
        $this->tags = new ArrayCollection();
    }
    
    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return $this->id;
    }
    
    public function getTagsArray(): array
    {
        $tags   = [];
        foreach ( $this->tags as $tag ) {
            $tags[] = $tag->getTag();
        }
        
        return $tags;
    }
    
    /**
     * @return Collection|TagsWhitelistTag[]
     */
    public function getTags(): Collection
    {
        return $this->tags;
    }
    
    public function addTag( TagsWhitelistTag $tag ): TagsWhitelistContextInterface
    {
        if ( ! $this->tags->contains( $tag ) ) {
            $this->tags[] = $tag;
            $tag->setContext( $this );
        }
        
        return $this;
    }
    
    public function removeTag( TagsWhitelistTag $tag ): TagsWhitelistContextInterface
    {
        if ( $this->tags->contains( $tag ) ) {
            $this->tags->removeElement( $tag );
            $tag->setContext( null );
        }
        
        return $this;
    }
    
    public function __toString()
    {
        return $this->taxon ? $this->taxon->getName() : '';
    }
}