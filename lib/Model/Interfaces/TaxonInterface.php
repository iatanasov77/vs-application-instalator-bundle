<?php namespace VS\ApplicationBundle\Model\Interfaces;

use Sylius\Component\Taxonomy\Model\TaxonInterface as BaseTaxonInterface;

interface TaxonInterface extends BaseTaxonInterface
{
    public function getTaxonomy();
}
