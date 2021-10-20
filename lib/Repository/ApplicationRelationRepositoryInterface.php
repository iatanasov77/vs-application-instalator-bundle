<?php namespace VS\ApplicationBundle\Repository;

use Sylius\Component\Resource\Repository\RepositoryInterface;
use VS\ApplicationBundle\Model\Interfaces\ApplicationInterface;

interface ApplicationRelationRepositoryInterface extends RepositoryInterface
{
    public function findByApplication( ApplicationInterface $application ) : iterable;
}
