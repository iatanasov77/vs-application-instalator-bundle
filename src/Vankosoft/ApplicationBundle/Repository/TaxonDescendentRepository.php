<?php namespace Vankosoft\ApplicationBundle\Repository;

use Sylius\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository;
use Vankosoft\ApplicationBundle\Repository\Traits\TaxonRepositoryTrait;

/**
 * Nodes Tree Hierarchy can to be got from Taxon Repository
 * $taxonRepository->getNodesHierarchy( $taxonomyRootNode );
 */
class TaxonDescendentRepository extends EntityRepository
{
    use TaxonRepositoryTrait;
}