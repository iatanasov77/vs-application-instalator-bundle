<?php namespace Vankosoft\ApplicationBundle\Model;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Vankosoft\ApplicationBundle\Model\Interfaces\WidgetGroupInterface;
use Vankosoft\ApplicationBundle\Model\Interfaces\TaxonInterface;

class WidgetGroup implements WidgetGroupInterface
{
    /** @var integer */
    protected $id;
    
    /** @var TaxonInterface */
    protected $taxon;
    
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
    
    public function getCode(): string
    {
        return $this->taxon ? $this->taxon->getCode() : '';
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