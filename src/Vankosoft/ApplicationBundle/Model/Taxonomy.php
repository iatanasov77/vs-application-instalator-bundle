<?php namespace Vankosoft\ApplicationBundle\Model;

use Vankosoft\ApplicationBundle\Model\Interfaces\TaxonomyInterface;
use Vankosoft\ApplicationBundle\Model\Interfaces\TaxonInterface;

class Taxonomy implements TaxonomyInterface
{
    /** @var integer */
    protected $id;
    
    /** @var string */
    protected $code;
    
    /** @var string */
    protected $name;
    
    /** @var string */
    protected $description;
    
    /** @var TaxonInterface */
    protected $rootTaxon;
    
    /** @var string */
    protected $locale;
    
    public function getCode()
    {
        return $this->code;
    }
    
    public function setCode( $code )
    {
        $this->code = $code;
        
        return $this;
    }
    
    public function getRootTaxon(): ?TaxonInterface
    {
        return $this->rootTaxon;    
    }
    
    public function setRootTaxon( $taxon )
    {
        $this->rootTaxon    = $taxon;
        
        return $this;
    }
    
    public function getTaxons()
    {
        return $this->rootTaxon->getChildren();
    }
    
    public function getId()
    {
        return $this->id;
    }
    
    public function getName(): ?string
    {
        return $this->name;
    }
    
    public function setName( $name )
    {
        $this->name = $name;
        
        return $this;
    }
    
    public function getDescription(): ?string
    {
        return $this->description;
    }
    
    public function setDescription( $description )
    {
        $this->description = $description;
        
        return $this;
    }
    
    public function getLocale()
    {
        return $this->locale;
    }
    
    public function setTranslatableLocale($locale)
    {
        $this->locale = $locale;
        
        return $this;
    }
}
