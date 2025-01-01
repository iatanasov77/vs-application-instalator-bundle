<?php namespace Vankosoft\ApplicationBundle\Model\Interfaces;

use Sylius\Component\Resource\Model\ResourceInterface;
use Vankosoft\ApplicationBundle\Model\Interfaces\TaxonInterface;
use Doctrine\Common\Collections\Collection;

interface VankosoftCategoryInterface extends ResourceInterface
{
    public function getParent();
    public function getChildren(): Collection;
}