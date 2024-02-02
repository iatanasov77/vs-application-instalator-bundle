<?php namespace Vankosoft\ApplicationBundle\Model\Traits;

use Vankosoft\ApplicationBundle\Model\Interfaces\TaxonInterface;

trait TaxonDescendentTrait
{
    /** @var TaxonInterface */
    protected $taxon;
    
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
    
    public function getCode()
    {
        return $this->taxon ? $this->taxon->getCode() : '';
    }
    
    public function setCode( ?string $code ) : self
    {
        if ( ! $this->taxon ) {
            // Create new taxon into the controller and set the properties passed from form
            return $this;
        }
        $this->taxon->setCode( $code );
        
        return $this;
    }
    
    public function getName()
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
    
    public function getDescription()
    {
        return $this->taxon ? $this->taxon->getDescription() : null;
    }
    
    public function setDescription( ?string $description ) : self
    {
        if ( ! $this->taxon ) {
            // Create new taxon into the controller and set the properties passed from form
            return $this;
        }
        $this->taxon->setDescription( $description );
        
        return $this;
    }
}