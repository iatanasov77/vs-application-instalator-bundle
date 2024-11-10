<?php namespace Vankosoft\ApplicationBundle\Model;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Vankosoft\ApplicationBundle\Model\Traits\TaxonDescendentTrait;
use Vankosoft\ApplicationBundle\Model\Interfaces\WidgetGroupInterface;

class WidgetGroup implements WidgetGroupInterface
{
    use TaxonDescendentTrait;
    
    /** @var integer */
    protected $id;
    
    /** @var Collection|Widget[] */
    protected $widgets;
    
    public function __construct()
    {
        $this->widgets = new ArrayCollection();
    }
    
    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return $this->id;
    }
    
    /**
     * @return Collection|Widget[]
     */
    public function getWidgets(): Collection
    {
        return $this->widgets;
    }
    
    public function addWidget( Widget $widget ): self
    {
        if ( ! $this->widgets->contains( $widget ) ) {
            $this->widgets[] = $widget;
            $widget->setGroup( $this );
        }
        
        return $this;
    }
    
    public function removeWidget( Widget $widget ): self
    {
        if ( $this->widgets->contains( $widget ) ) {
            $this->widgets->removeElement( $widget );
            $widget->setGroup( null );
        }
        
        return $this;
    }
    
    public function __toString()
    {
        return $this->taxon ? $this->taxon->getName() : '';
    }
}