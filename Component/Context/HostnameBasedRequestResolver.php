<?php namespace Vankosoft\ApplicationBundle\Component\Context;

use Symfony\Component\HttpFoundation\Request;
use Vankosoft\ApplicationBundle\Model\Interfaces\ApplicationInterface;
use Vankosoft\ApplicationBundle\Repository\Interfaces\ApplicationRepositoryInterface;

final class HostnameBasedRequestResolver implements RequestResolverInterface
{
    private ApplicationRepositoryInterface $applicationRepository;

    public function __construct( ApplicationRepositoryInterface $applicationRepository )
    {
        $this->applicationRepository = $applicationRepository;
    }

    public function findApplication( Request $request ) : ?ApplicationInterface
    {
        return $this->applicationRepository->findOneByHostname( $request->getHost() );
    }
}
