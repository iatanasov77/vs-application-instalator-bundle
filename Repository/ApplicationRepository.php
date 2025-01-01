<?php namespace Vankosoft\ApplicationBundle\Repository;

use Sylius\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository;
use Vankosoft\ApplicationBundle\Repository\Interfaces\ApplicationRepositoryInterface;
use Vankosoft\ApplicationBundle\Model\Interfaces\ApplicationInterface;

class ApplicationRepository extends EntityRepository implements ApplicationRepositoryInterface
{
    public function findOneByHostname( string $hostname ) : ?ApplicationInterface
    {
        return $this->findOneBy( ['hostname' => $hostname] );
    }
    
    public function findOneByCode( string $code ) : ?ApplicationInterface
    {
        return $this->findOneBy(['code' => $code]);
    }
    
    public function findByName( string $name ) : iterable
    {
        return $this->findBy( ['title' => $name] );
    }
}
