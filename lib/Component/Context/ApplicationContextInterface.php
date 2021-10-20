<?php namespace VS\ApplicationBundle\Component\Context;

use VS\ApplicationBundle\Model\Interfaces\ApplicationInterface;

interface ApplicationContextInterface
{
    /**
     * @throws ApplicationNotFoundException
     */
    public function getApplication() : ApplicationInterface;
}
