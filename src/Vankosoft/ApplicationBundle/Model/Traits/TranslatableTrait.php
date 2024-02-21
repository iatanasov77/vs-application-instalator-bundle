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
    
    public function getTranslations(): Collection
    {
        return $this->translations ?: new ArrayCollection();
    }
    
    protected function createTranslation(): TranslationInterface
    {
        
    }
}