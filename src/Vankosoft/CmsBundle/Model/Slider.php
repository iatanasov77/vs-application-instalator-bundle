<?php namespace Vankosoft\CmsBundle\Model;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Vankosoft\ApplicationBundle\Model\Traits\TaxonDescendentTrait;
use Vankosoft\ApplicationBundle\Model\Interfaces\TaxonDescendentInterface;

class Slider implements TaxonDescendentInterface
{
    use TaxonDescendentTrait;
    
    /** @var integer */
    protected $id;
    
    /** @var Collection|SliderItem[] */
    protected $items;
    
    public function __construct()
    {
        $this->items    = new ArrayCollection();
    }
    
    public function getId()
    {
        return $this->id;
    }
    
    public function getItems(): Collection
    {
        return $this->items;
    }
    
    public function addItem( SliderItemInterface $item ): self
    {
        if ( ! $this->items->contains( $item ) ) {
            $this->items[] = $item;
            $item->setSlider( $this );
        }
        
        return $this;
    }
    
    public function removeItem( SliderItemInterface $item ): self
    {
        if ( $this->items->contains( $item ) ) {
            $this->items->removeElement( $item );
            $item->setSlider( $this );
        }
        
        return $this;
    }
}