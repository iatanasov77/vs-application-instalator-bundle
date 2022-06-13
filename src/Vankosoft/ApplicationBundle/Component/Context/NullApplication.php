<?php namespace Vankosoft\ApplicationBundle\Component\Context;

use Vankosoft\ApplicationBundle\Model\Interfaces\ApplicationInterface;

class NullApplication implements ApplicationInterface
{
    public function getId()
    {
        return 0;
    }
    
    public function getCode()
    {
        return 'null-application';
    }
    
    public function getTitle()
    {
        return 'Null Application';
    }
    
    public function getHostname()
    {
        return 'null-application';
    }
}
