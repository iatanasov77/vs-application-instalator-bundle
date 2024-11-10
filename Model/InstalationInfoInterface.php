<?php namespace Vankosoft\ApplicationInstalatorBundle\Model;

use Sylius\Component\Resource\Model\ResourceInterface;
use Sylius\Component\Resource\Model\TimestampableInterface;

interface InstalationInfoInterface extends ResourceInterface, TimestampableInterface
{
    const VERSION_UNDEFINED                 = 'version_undefined';
    const VERSION_DATA_PROJECT_VERSION      = 'project_version';
    const VERSION_DATA_DOCTRINE_MIGRATION   = 'doctrine_migration';
    
    public function getVersion() : string;
    public function getData(): array;
}
