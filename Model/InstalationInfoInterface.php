<?php namespace VS\ApplicationInstalatorBundle\Model;

use Sylius\Component\Resource\Model\ResourceInterface;
use Sylius\Component\Resource\Model\TimestampableInterface;

interface InstalationInfoInterface extends ResourceInterface, TimestampableInterface
{
    public function getVersion() : string;
}
