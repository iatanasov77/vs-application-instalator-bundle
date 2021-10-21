<?php namespace VS\ApplicationBundle\Model;

use VS\ApplicationBundle\Model\Interfaces\TaxonomyInterface;

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
    
    protected $rootTaxon;
    
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
    
    public function getRootTaxon()
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
    
    public function getName()
    {
        return $this->name;
    }
    
    public function setName( $name )
    {
        $this->name = $name;
        
        return $this;
    }
    
    public function getDescription()
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
