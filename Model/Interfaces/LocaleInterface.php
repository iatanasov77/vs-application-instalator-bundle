<?php namespace Vankosoft\ApplicationBundle\Model\Interfaces;

use Sylius\Component\Locale\Model\LocaleInterface as BaseLocaleInterface;

interface LocaleInterface extends BaseLocaleInterface, TranslatableInterface
{
    public function setTranslatableLocale( $locale ): LocaleInterface;
    public function getTitle();
}
