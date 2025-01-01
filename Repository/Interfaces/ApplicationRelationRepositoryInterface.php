<?php namespace Vankosoft\ApplicationBundle\Repository\Interfaces;

use Sylius\Component\Resource\Repository\RepositoryInterface;
use Vankosoft\ApplicationBundle\Model\Interfaces\ApplicationInterface;

interface ApplicationRelationRepositoryInterface extends RepositoryInterface
{
    public function findByApplication( ApplicationInterface $application ) : iterable;
}
