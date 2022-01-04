<?php namespace Vankosoft\ApplicationBundle\Model\Interfaces;

use Sylius\Component\Resource\Model\ResourceInterface;

interface TaxonomyInterface extends ResourceInterface
{
    public function getName();
    public function getDescription();
    public function getRootTaxon();
}
