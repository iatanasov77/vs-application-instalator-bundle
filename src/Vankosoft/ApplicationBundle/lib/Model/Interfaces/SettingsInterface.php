<?php namespace VS\ApplicationBundle\Model\Interfaces;

use Sylius\Component\Resource\Model\ResourceInterface;
use VS\CmsBundle\Model\PageInterface;

interface SettingsInterface extends ResourceInterface
{
    public function getApplication();
    public function getMaintenanceMode();
    public function getMaintenancePage(): ?PageInterface;
    public function getTheme();
}
