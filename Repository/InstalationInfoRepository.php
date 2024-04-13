<?php namespace Vankosoft\ApplicationInstalatorBundle\Repository;

use Sylius\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository;
use Vankosoft\ApplicationInstalatorBundle\Model\InstalationInfoInterface;

class InstalationInfoRepository extends EntityRepository implements InstalationInfoRepositoryInterface
{
    public function getLatestInstallation() : ?InstalationInfoInterface
    {
        $this->findOneBy( [], ['id' => 'DESC'] );
    }
}
