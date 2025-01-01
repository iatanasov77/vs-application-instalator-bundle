<?php namespace Vankosoft\ApplicationBundle\Model\Interfaces;

use Sylius\Component\Resource\Model\ResourceInterface;

interface ApplicationRelationInterface extends ResourceInterface
{
    public function getApplication() : ?ApplicationInterface;
}
