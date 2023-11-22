<?php namespace Vankosoft\ApplicationBundle\Controller;

use Pd\WidgetBundle\Controller\WidgetController;
use Doctrine\Persistence\ManagerRegistry;

class WidgetController extends WidgetController
{
    /** @var ManagerRegistry */
    protected $doctrine;
    
    public function __construct(
        ManagerRegistry $doctrine
    ) {
        $this->doctrine = $doctrine;
    }
    
    protected function getDoctrine()
    {
        return $this->doctrine;
    }
}