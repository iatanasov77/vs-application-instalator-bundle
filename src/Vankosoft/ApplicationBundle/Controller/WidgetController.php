<?php namespace Vankosoft\ApplicationBundle\Controller;

use Symfony\Contracts\Cache\CacheInterface;
use Doctrine\Persistence\ManagerRegistry;
use Pd\WidgetBundle\Controller\WidgetController;
use Pd\WidgetBundle\Repository\WidgetUserRepository;

class WidgetController extends WidgetController
{
    /** @var CacheInterface */
    protected $cache;
    
    /** @var ManagerRegistry */
    protected $doctrine;
    
    /** @var WidgetUserRepository */
    protected $widgetUserRepo;
    
    public function __construct(
        CacheInterface $cache,
        ManagerRegistry $doctrine,
        WidgetUserRepository $widgetUserRepo
    ) {
        $this->cache            = $cache;
        $this->doctrine         = $doctrine;
        $this->widgetUserRepo   = $widgetUserRepo;
    }
    
    protected function getDoctrine()
    {
        return $this->doctrine;
    }
}