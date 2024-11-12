<?php namespace Vankosoft\ApplicationBundle\Model;

use Doctrine\ORM\Mapping as ORM;
use Sylius\Component\Taxonomy\Model\TaxonTranslation as BaseTaxonTranslation;
use Vankosoft\ApplicationBundle\Model\Interfaces\TaxonTranslationInterface;

class TaxonTranslation extends BaseTaxonTranslation implements TaxonTranslationInterface
{
    public function __construct()
    {
        $this->locale   = 'en_US';
    }
}
