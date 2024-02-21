<?php namespace Vankosoft\ApplicationBundle\Model\Traits;

use Vankosoft\ApplicationBundle\Model\Traits\TranslatableTrait;
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
}
