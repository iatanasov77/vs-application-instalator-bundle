<?php namespace Vankosoft\ApplicationBundle\Component\Context;

use Symfony\Component\HttpFoundation\Request;
use Vankosoft\ApplicationBundle\Model\Interfaces\ApplicationInterface;

interface RequestResolverInterface
{
    public function findApplication( Request $request ) : ?ApplicationInterface;
}
