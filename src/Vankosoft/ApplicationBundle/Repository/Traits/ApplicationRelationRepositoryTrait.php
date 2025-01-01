<?php namespace Vankosoft\ApplicationBundle\Repository\Traits;

use Vankosoft\ApplicationBundle\Model\Interfaces\ApplicationInterface;

trait ApplicationRelationRepositoryTrait
{
    public function findByApplication( ApplicationInterface $application ) : iterable
    {
        return $this->findBy( ['application' => $application->getId()] );
    }
}
