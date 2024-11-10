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
    
    public function isEnabled(): bool
    {
        return $this->taxon ? $this->taxon->isEnabled() : false;
    }
    
    /**
     * @param bool $enabled
     */
    public function setEnabled( ?bool $enabled ): self
    {
        if ( ! $this->taxon ) {
            // Create new taxon into the controller and set the properties passed from form
            return $this;
        }
        $this->taxon->setEnabled( $enabled );
        
        return $this;
    }
    
    public function getSlug(): string
    {
        return $this->getCode();
    }
    
    public function setSlug( ?string $code ): self
    {
        $this->setCode( $code );
        
        return $this;
    }
    
    public function getCode(): ?string
    {
        return $this->taxon ? $this->taxon->getCode() : '';
    }
    
    public function setCode( ?string $code ): self
    {
        if ( ! $this->taxon ) {
            // Create new taxon into the controller and set the properties passed from form
            return $this;
        }
        $this->taxon->setCode( $code );
        
        return $this;
    }
    
    public function getName(): ?string
    {
        if ( $this->taxon ) {
            /**
             * WORKAROUND
             * ==========
             * Taxons CurrentLocale is NULL Sometimes and throws an Exception
             */
            $taxonCurrentLocale = $this->taxon->getCurrentLocale();
            if ( ! $taxonCurrentLocale ) {
                $this->taxon->setCurrentLocale( 'en_US' );
            }
            
            return $this->taxon->getName();
        }
        
        return '';
    }
    
    public function setName( string $name ): self
    {
        if ( ! $this->taxon ) {
            // Create new taxon into the controller and set the properties passed from form
            return $this;
        }
        $this->taxon->setName( $name );
        
        return $this;
    }
    
    public function getDescription(): ?string
    {
        if ( $this->taxon ) {
            /**
             * WORKAROUND
             * ==========
             * Taxons CurrentLocale is NULL Sometimes and throws an Exception
             */
            $taxonCurrentLocale = $this->taxon->getCurrentLocale();
            if ( ! $taxonCurrentLocale ) {
                $this->taxon->setCurrentLocale( 'en_US' );
            }
            
            return $this->taxon->getDescription();
        }
        
        return null;
    }
    
    public function setDescription( ?string $description ): self
    {
        if ( ! $this->taxon ) {
            // Create new taxon into the controller and set the properties passed from form
            return $this;
        }
        $this->taxon->setDescription( $description );
        
        return $this;
    }
    
    public function getNameTranslated( string $locale ): ?string
    {
        return $this->taxon ? $this->taxon->getTranslation( $locale )->getName() : '';
    }
}