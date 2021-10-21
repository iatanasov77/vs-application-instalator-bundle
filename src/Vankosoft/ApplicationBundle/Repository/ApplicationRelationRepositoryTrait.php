<?php namespace VS\ApplicationBundle\Repository;

use VS\ApplicationBundle\Model\Interfaces\ApplicationInterface;

trait ApplicationRelationRepositoryTrait
{
    public function findByApplication( ApplicationInterface $application ) : iterable
    {
        return $this->findBy( ['application' => $application->getId()] );
    }
}
