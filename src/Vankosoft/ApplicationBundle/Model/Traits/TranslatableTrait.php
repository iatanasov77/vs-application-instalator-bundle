<?php namespace Vankosoft\ApplicationBundle\Model\Traits;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Sylius\Component\Resource\Model\TranslationInterface;
use Sylius\Component\Resource\Model\TranslatableTrait as BaseTranslatableTrait;

trait TranslatableTrait
{
    use BaseTranslatableTrait {
        BaseTranslatableTrait::getTranslations as parentGetTranslations;
    }
    
    /** @var string */
    protected $locale;
    
    public function getTranslations(): Collection
    {
        return $this->translations ?: new ArrayCollection();
    }
    
    public function getLocale(): ?string
    {
        return $this->currentLocale;
    }
    
    public function getTranslatableLocale(): ?string
    {
        return $this->locale;
    }
    
    public function setTranslatableLocale( $locale ): self
    {
        $this->locale = $locale;
        
        return $this;
    }
    
    /*
     * @NOTE: Decalared abstract in BaseTranslatableTrait
     */
    protected function createTranslation(): TranslationInterface
    {
        
    }
}