<?php namespace Vankosoft\ApplicationInstalatorBundle\Repository;

use Vankosoft\ApplicationInstalatorBundle\Model\InstalationInfoInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;

interface InstalationInfoRepositoryInterface extends RepositoryInterface
{
    public function getLatestInstallation() : ?InstalationInfoInterface;
}
