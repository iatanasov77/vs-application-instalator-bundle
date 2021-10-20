<?php namespace VS\ApplicationBundle\Component\Context;

use Symfony\Component\HttpFoundation\Request;
use VS\ApplicationBundle\Model\Interfaces\ApplicationInterface;

interface RequestResolverInterface
{
    public function findApplication( Request $request ) : ?ApplicationInterface;
}
