<?php namespace Vankosoft\ApplicationBundle\Model;

use Vankosoft\ApplicationBundle\Model\Interfaces\WidgetsRegistryInterface;
use Vankosoft\ApplicationBundle\Model\Traits\WidgetUserTrait;

class WidgetsRegistry implements WidgetsRegistryInterface
{
    use WidgetUserTrait;
    
    /** @var integer */
    protected $id;
    
    public function getId()
    {
        return $this->id;
    }
}