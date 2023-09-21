<?php namespace Vankosoft\ApplicationBundle\Model\Interfaces;

use Sylius\Component\Resource\Model\ResourceInterface;

interface TagsWhitelistTagInterface extends ResourceInterface
{
    public function getContext(): ?TagsWhitelistContextInterface;
    public function getTag(): ?string;
}
