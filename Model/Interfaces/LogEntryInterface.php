<?php namespace Vankosoft\ApplicationBundle\Model\Interfaces;

use Sylius\Component\Resource\Model\ResourceInterface;

interface LogEntryInterface extends ResourceInterface
{
    public function getAction();
    public function getObjectClass();
    public function getObjectId();
    public function getUsername();
    public function getLoggedAt();
    public function getData();
    public function getVersion();
}
