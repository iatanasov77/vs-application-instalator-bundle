<?php namespace Vankosoft\ApplicationBundle\Model\Interfaces;

use Sylius\Component\Resource\Model\ResourceInterface;

interface TranslationInterface extends ResourceInterface
{
    public function getLocale();
    public function getField();
    public function getObjectClass();
    public function getForeignKey();
    public function getContent();
}
