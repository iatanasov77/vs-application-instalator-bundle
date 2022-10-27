<?php namespace Vankosoft\ApplicationBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Sylius\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository;
use Vankosoft\ApplicationBundle\Component\Status;

class ApiTestController extends AbstractController
{
    /** @var EntityRepository */
    protected $applicationRepository;
    
    public function __construct(
        EntityRepository $applicationRepository
    ) {
        $this->applicationRepository    = $applicationRepository;
    }
    
    public function index( Request $request ): Response
    {
        $data   = [];
        foreach ( $this->applicationRepository->findAll() as $application ) {
            $data[] = [
                'code'      => $application->getCode(),
                'title'     => $application->getTitle(),
                'hostname'  => $application->getHostname()
            ];
        }
        
        return new JsonResponse([
            'status'    => Status::STATUS_OK,
            'data'      => $data
        ]);
    }
}