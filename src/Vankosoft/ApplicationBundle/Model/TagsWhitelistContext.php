<?php namespace Vankosoft\ApplicationBundle\Model;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Vankosoft\ApplicationBundle\Model\Interfaces\TagsWhitelistContextInterface;
use Vankosoft\ApplicationBundle\Model\Interfaces\TaxonInterface;

class TagsWhitelistContext implements TagsWhitelistContextInterface
{
    /** @var integer */
    protected $id;
    
    /** @var TaxonInterface */
    protected $taxon;
    
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
    
    /**
     * {@inheritdoc}
     */
    public function getTaxon(): ?TaxonInterface
    {
        return $this->taxon;
    }
    
    /**
     * {@inheritdoc}
     */
    public function setTaxon( ?TaxonInterface $taxon ): void
    {
        $this->taxon = $taxon;
    }
    
    public function getName(): string
    {
        return $this->taxon ? $this->taxon->getName() : '';
    }
    
    public function setName( string $name ) : self
    {
        if ( ! $this->taxon ) {
            // Create new taxon into the controller and set the properties passed from form
            return $this;
        }
        $this->taxon->setName( $name );
        
        return $this;
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