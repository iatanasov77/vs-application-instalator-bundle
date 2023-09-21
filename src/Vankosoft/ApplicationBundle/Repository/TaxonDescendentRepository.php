<?php namespace Vankosoft\ApplicationBundle\Repository;

use Sylius\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository;
use Vankosoft\ApplicationBundle\Repository\Traits\TaxonRepositoryTrait;

class TaxonDescendentRepository extends EntityRepository
{
    use TaxonRepositoryTrait;
}