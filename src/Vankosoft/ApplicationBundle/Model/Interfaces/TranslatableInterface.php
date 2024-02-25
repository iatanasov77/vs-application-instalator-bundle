<?php namespace Vankosoft\ApplicationBundle\Model\Interfaces;

use Sylius\Component\Resource\Model\TranslatableInterface;

interface TranslatableInterface extends TranslatableInterface
{
    public function getLocale(): ?string;
    public function getTranslatableLocale(): ?string;
    public function setTranslatableLocale( $locale ): self;
}