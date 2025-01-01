<?php namespace Vankosoft\ApplicationBundle\Repository;

use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;
use Sylius\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository;
use Vankosoft\ApplicationBundle\Repository\Interfaces\TaxonDescendentRepositoryInterface;
use Vankosoft\ApplicationBundle\Repository\Traits\TaxonRepositoryTrait;

/**
 * Nodes Tree Hierarchy can to be got from Taxon Repository
 * $taxonRepository->getNodesHierarchy( $taxonomyRootNode );
 * 
 * If The Repository that extend this class need service container:
 * ------------------------------------------------------------------
 * vsorg.repository.blogposts_categories:
 *     class: App\Repository\BlogPostCategoryRepository
 *     factory: ["@doctrine.orm.entity_manager", getRepository]
 *     arguments:
 *         - '%vsorg.model.blogposts_categories.class%'
 *     calls:
 *         - [ setContainer, [ '@service_container' ] ]
 */
class TaxonDescendentRepository extends EntityRepository implements TaxonDescendentRepositoryInterface, ContainerAwareInterface
{
    use ContainerAwareTrait;
    use TaxonRepositoryTrait;
}