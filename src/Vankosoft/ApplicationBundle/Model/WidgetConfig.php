<?php namespace Vankosoft\ApplicationBundle\Model;

use Vankosoft\ApplicationBundle\Model\Interfaces\WidgetConfigInterface;
use Vankosoft\ApplicationBundle\Model\Traits\WidgetUserTrait;

class WidgetConfig implements WidgetConfigInterface
{
    use WidgetUserTrait;
    
    /** @var integer */
    protected $id;
    
    public function getId()
    {
        return $this->id;
    }
}