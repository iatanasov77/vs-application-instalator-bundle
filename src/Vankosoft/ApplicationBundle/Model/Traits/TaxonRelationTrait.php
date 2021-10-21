<?php namespace VS\ApplicationBundle\Model\Traits;

use Sylius\Component\Resource\Model\TranslatableTrait;
use Sylius\Component\Resource\Model\TranslationInterface;
/**
 * @see TaxonRelationInterface
 */
trait TaxonRelationTrait
{
    use TranslatableTrait;
    
    public function setCurrentLocale( string $currentLocale ): void
    {
        $this->taxon->setCurrentLocale( $currentLocale );
    }
    
    public function setFallbackLocale( string $fallbackLocale ): void
    {
        $this->taxon->setFallbackLocale( $fallbackLocale );
    }
    
    /**
     * Create resource translation model.
     */
    protected function createTranslation(): TranslationInterface
    {
        
    }
}
