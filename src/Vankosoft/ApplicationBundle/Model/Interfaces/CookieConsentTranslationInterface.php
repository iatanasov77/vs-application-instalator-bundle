<?php namespace Vankosoft\ApplicationBundle\Model\Interfaces;

use Sylius\Component\Resource\Model\ResourceInterface;

interface CookieConsentTranslationInterface extends ResourceInterface
{
    public function getLanguageCode();
    public function getBtnAcceptAll();
    public function getBtnRejectAll();
    public function getTitle();
    public function getDescription();
    public function getLabel();
}
