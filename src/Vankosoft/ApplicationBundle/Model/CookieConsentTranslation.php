<?php namespace Vankosoft\ApplicationBundle\Model;

use Vankosoft\ApplicationBundle\Model\Interfaces\CookieConsentTranslationInterface;

class CookieConsentTranslation implements CookieConsentTranslationInterface
{
    /** @var integer */
    protected $id;
    
    /** @var string */
    protected $languageCode;
    
    /** @var string */
    protected $localeCode;
    
    /** @var string */
    protected $btnAcceptAll;
    
    /** @var string */
    protected $btnRejectAll;
    
    /** @var string */
    protected $title;
    
    /** @var string */
    protected $description;
    
    /** @var string */
    protected $label;
    
    /** @var string */
    protected $btnAcceptNecessary;
    
    /** @var string */
    protected $btnShowPreferences;
    
    public function getId()
    {
        return $this->id;
    }
    
    public function getLanguageCode()
    {
        return $this->languageCode;
    }
    
    public function setLanguageCode( $languageCode ): self
    {
        $this->languageCode = $languageCode;
        
        return $this;
    }
    
    public function getLocaleCode()
    {
        return $this->localeCode;
    }
    
    public function setLocaleCode( $localeCode ): self
    {
        $this->localeCode = $localeCode;
        
        return $this;
    }
    
    public function getBtnAcceptAll()
    {
        return $this->btnAcceptAll;
    }
    
    public function setBtnAcceptAll( $btnAcceptAll ): self
    {
        $this->btnAcceptAll = $btnAcceptAll;
        
        return $this;
    }
    
    public function getBtnRejectAll()
    {
        return $this->btnRejectAll;
    }
    
    public function setBtnRejectAll( $btnRejectAll ): self
    {
        $this->btnRejectAll = $btnRejectAll;
        
        return $this;
    }
    
    public function getTitle()
    {
        return $this->title;
    }
    
    public function setTitle( $title ): self
    {
        $this->title = $title;
        
        return $this;
    }
    
    public function getDescription()
    {
        return $this->description;
    }
    
    public function setDescription( $description ): self
    {
        $this->description  = $description;
        
        return $this;
    }
    
    public function getLabel()
    {
        return $this->label;
    }
    
    public function setLabel( $label ): self
    {
        $this->label = $label;
        
        return $this;
    }
    
    public function getBtnAcceptNecessary()
    {
        return $this->btnAcceptNecessary;
    }
    
    public function setBtnAcceptNecessary( $btnAcceptNecessary ): self
    {
        $this->btnAcceptNecessary = $btnAcceptNecessary;
        
        return $this;
    }
    
    public function getBtnShowPreferences()
    {
        return $this->btnShowPreferences;
    }
    
    public function setBtnShowPreferences( $btnShowPreferences ): self
    {
        $this->btnShowPreferences = $btnShowPreferences;
        
        return $this;
    }
}