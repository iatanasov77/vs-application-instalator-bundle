<?php namespace Vankosoft\ApplicationBundle\Repository;

use Sylius\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository;

class TaxonomyRepository extends EntityRepository
{
    public function findByCode( $code )
    {
        return $this->findOneBy( ['code' => $code] );
    }
}
