<?php namespace Vankosoft\ApplicationBundle\Model\Interfaces;

use Sylius\Component\Resource\Model\ResourceInterface;
use Vankosoft\CmsBundle\Model\Interfaces\PageInterface;

interface SettingsInterface extends ResourceInterface
{
    public function getApplication();
    public function getMaintenanceMode();
    public function getMaintenancePage(): ?PageInterface;
    public function getTheme();
}
