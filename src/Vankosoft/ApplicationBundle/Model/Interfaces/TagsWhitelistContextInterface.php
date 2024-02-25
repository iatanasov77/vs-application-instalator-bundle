<?php namespace Vankosoft\ApplicationBundle\Model\Interfaces;

use Sylius\Component\Resource\Model\ResourceInterface;
use Vankosoft\ApplicationBundle\Model\Interfaces\TaxonDescendentInterface;
use Doctrine\Common\Collections\Collection;

interface TagsWhitelistContextInterface extends ResourceInterface, TaxonDescendentInterface
{
    public function getTags(): Collection;
    public function getTagsArray(): array;
}
