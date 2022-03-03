<?php namespace Vankosoft\ApplicationBundle\Model\Interfaces;

use Sylius\Component\Resource\Model\ResourceInterface;

interface ApplicationInterface extends ResourceInterface
{
    public function getCode();
    public function getTitle();
    public function getHostname();
}
