<?php namespace Vankosoft\ApplicationBundle\Model\Interfaces;

use Sylius\Component\Resource\Model\TranslatableInterface as BaseTranslatableInterface;

interface TranslatableInterface extends BaseTranslatableInterface
{
    public function getLocale(): ?string;
    public function getTranslatableLocale(): ?string;
    public function setTranslatableLocale( $locale ): self;
}