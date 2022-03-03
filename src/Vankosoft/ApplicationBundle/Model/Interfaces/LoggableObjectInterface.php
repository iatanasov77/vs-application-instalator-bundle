<?php namespace Vankosoft\ApplicationBundle\Model\Interfaces;

use Sylius\Component\Resource\Model\ResourceInterface;

interface LoggableObjectInterface extends ResourceInterface
{
    public function getTranslatableLocale() : ?string;
}
