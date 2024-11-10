<?php namespace Vankosoft\ApplicationBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Doctrine\Persistence\ManagerRegistry;
use Sylius\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository;
use Vankosoft\ApplicationBundle\Form\TagsWhitelistContextTagsForm;

class TagsWhitelistContextsExtController extends AbstractController
{
    /** @var ManagerRegistry */
    protected $doctrine;
    
    /** @var EntityRepository */
    protected $tagsWhitelistContextRepository;
    
    public function __construct(
        ManagerRegistry $doctrine,
        EntityRepository $tagsWhitelistContextRepository
    ) {
        $this->doctrine                         = $doctrine;
        $this->tagsWhitelistContextRepository   = $tagsWhitelistContextRepository;
    }
    
    public function updateTagsWhitelistContextTagsAction( $contextId, Request $request ): Response
    {
        $context    = $this->tagsWhitelistContextRepository->find( $contextId );
        $tagsForm   = $this->createForm( TagsWhitelistContextTagsForm::class, $context, [
            'action' => $this->generateUrl( 'vs_application_whitelist_context_update_tags', ['contextId' => $contextId] ),
            'method' => 'POST',
        ]);
        
        $tagsForm->handleRequest( $request );
        if( $tagsForm->isSubmitted() && $tagsForm->isValid() ) {
            $entity = $tagsForm->getData();
            
            $em = $this->doctrine->getManager();
            $em->persist( $entity );
            $em->flush();
            
            return $this->redirect( $this->generateUrl( 'vs_application_tags_whitelist_context_update', ['id' => $contextId] ) );
        }
        
        throw new \RuntimeException( 'Whitelist Context Tags Form Not Submited properly!' );
    }
}