<?php namespace Vankosoft\ApplicationBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;

use Doctrine\Persistence\ManagerRegistry;
use Sylius\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository;
use Sylius\Component\Resource\Factory\Factory;

use Vankosoft\ApplicationBundle\Form\ApplicationForm;
use Vankosoft\ApplicationBundle\Component\Status;

class ApplicationExtController extends AbstractController
{
    /** @var ManagerRegistry */
    protected $doctrine;
    
    /** @var EntityRepository */
    protected $applicationRepository;
    
    /** @var Factory */
    protected $applicationFactory;
    
    public function __construct(
        ManagerRegistry $doctrine,
        EntityRepository $applicationRepository,
        Factory $applicationFactory
    ) {
        $this->doctrine                 = $doctrine;
        $this->applicationRepository    = $applicationRepository;
        $this->applicationFactory       = $applicationFactory;
    }
    
    public function index( int $applicationId, Request $request ): Response
    {
        $application    = $this->applicationRepository->find( $applicationId );
        $form           = $this->createForm( ApplicationForm::class, $application ?: $this->applicationFactory->createNew() );
        
        return $this->render( '@VSApplication/Pages/Settings/partial/application-form.html.twig', [
            'applicationId' => $applicationId,
            'form'          => $form->createView(),
        ]);
    }
    
    public function handle( int $applicationId, Request $request ): Response
    {
        $application    = $this->applicationRepository->find( $applicationId );
        $form           = $this->createForm( ApplicationForm::class, $application );
        $form->handleRequest( $request );
        if( $form->isSubmitted() && $form->isValid() ) {
            $entity = $form->getData();
            
            $em = $this->doctrine->getManager();
            $em->persist( $entity );
            $em->flush();
            
            return $this->redirect( $this->generateUrl( 'vs_application_settings_index' ) );
        }
        
        throw new \Exception( 'Application Edit Form Not Submited properly!' );
    }
    
    public function remove( int $applicationId, Request $request ): Response
    {
        $application    = $this->applicationRepository->find( $applicationId );
        $em             = $this->doctrine->getManager();
        
        $em->remove( $application );
        $em->flush();
        
        return new JsonResponse([
            'status'    => Status::STATUS_OK
        ]);
    }
}
