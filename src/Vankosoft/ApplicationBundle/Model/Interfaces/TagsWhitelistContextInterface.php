<?php namespace Vankosoft\ApplicationBundle\Model\Interfaces;

use Sylius\Component\Resource\Model\ResourceInterface;
use Doctrine\Common\Collections\Collection;

interface TagsWhitelistContextInterface extends ResourceInterface
{
    public function getName(): ?string;
    public function getTags(): Collection;
    public function getTagsArray(): array;
}
