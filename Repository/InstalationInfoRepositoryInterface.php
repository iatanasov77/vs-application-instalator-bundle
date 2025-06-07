<?php namespace Vankosoft\ApplicationInstalatorBundle\Repository;

use Vankosoft\ApplicationInstalatorBundle\Model\InstalationInfoInterface;
use Sylius\Resource\Doctrine\Persistence\RepositoryInterface;

interface InstalationInfoRepositoryInterface extends RepositoryInterface
{
    public function getLatestInstallation() : ?InstalationInfoInterface;
}
