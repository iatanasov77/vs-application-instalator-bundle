<?php namespace Vankosoft\ApplicationBundle\Model;

use Vankosoft\ApplicationBundle\Model\Interfaces\WidgetInterface;
use Vankosoft\ApplicationBundle\Model\Traits\WidgetUserTrait;

class Widget implements WidgetInterface
{
    use WidgetUserTrait;
    
    /** @var integer */
    protected $id;
    
    public function getId()
    {
        return $this->id;
    }
}