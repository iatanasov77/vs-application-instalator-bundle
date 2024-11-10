<?php namespace Vankosoft\ApplicationBundle\Model\Traits;

use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;

trait TaxonLeafTrait
{
    public function getChildren(): Collection
    {
        return new ArrayCollection();
    }
}
