<?php namespace Vankosoft\ApplicationBundle\Model\Interfaces;

use Doctrine\Common\Collections\Collection;

interface TaxonLeafInterface
{
    public function getName(): ?string;
    public function getChildren(): Collection;
}
