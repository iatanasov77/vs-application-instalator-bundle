<?php namespace Vankosoft\ApplicationInstalatorBundle\Repository;

use Doctrine\DBAL\Exception\InvalidFieldNameException;
use Sylius\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository;
use Vankosoft\ApplicationInstalatorBundle\Model\InstalationInfoInterface;

class InstalationInfoRepository extends EntityRepository implements InstalationInfoRepositoryInterface
{
    public function getLatestInstallation() : ?InstalationInfoInterface
    {
        try {
            $latestInstallation = $this->findOneBy( [], ['id' => 'DESC'] );
        } catch ( InvalidFieldNameException $e ) {
            /** @NOTE If The InstallationInfo Model is Not Valid */
            return null;
        }
        
        return $latestInstallation;
    }
}
