<?php namespace Vankosoft\ApplicationBundle\Model\Interfaces;

use Sylius\Component\Resource\Model\ResourceInterface;
use Vankosoft\ApplicationBundle\Model\Interfaces\TaxonInterface;

interface TaxonomyInterface extends ResourceInterface
{
    public function getName(): ?string;
    public function getDescription(): ?string;
    public function getRootTaxon(): ?TaxonInterface;
}
