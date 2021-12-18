<?php namespace Vankosoft\ApplicationBundle\Component\Context;

use Vankosoft\ApplicationBundle\Model\Interfaces\ApplicationInterface;

interface ApplicationContextInterface
{
    /**
     * @throws ApplicationNotFoundException
     */
    public function getApplication() : ApplicationInterface;
}
