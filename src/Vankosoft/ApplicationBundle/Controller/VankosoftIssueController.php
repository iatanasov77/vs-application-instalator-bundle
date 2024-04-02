<?php namespace Vankosoft\ApplicationBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sylius\Component\Resource\Repository\RepositoryInterface;

use Vankosoft\ApplicationBundle\Component\Application\ProjectIssue;
use Vankosoft\ApplicationBundle\Form\ProjectIssueForm;

class VankosoftIssueController extends AbstractController
{
    /** @var ProjectIssue */
    private $vsProject;
    
    /** @var RepositoryInterface */
    private $tagsWhitelistContextRepository;
    
    public function __construct(
        ProjectIssue $vsProject,
        RepositoryInterface $tagsWhitelistContextRepository
    ) {
        $this->vsProject                        = $vsProject;
        $this->tagsWhitelistContextRepository   = $tagsWhitelistContextRepository;
    }
    
    public function indexAction( Request $request ): Response
    {
        $issues = $this->vsProject->getIssues();
        //echo '<pre>'; var_dump( $issues ); die;
        
        return $this->render( '@VSApplication/Pages/ProjectIssues/index.html.twig', [
            'issues'    => $issues,
        ]);
    }
    
    public function createAction( Request $request ): Response
    {
        $tagsContext    = $this->tagsWhitelistContextRepository->findByTaxonCode( 'project-issue-labels' );
        
        //$issue = $this->vsProject->createIssue();
        $form           = $this->createForm( ProjectIssueForm::class );
        $form->handleRequest( $request );
        if( $form->isSubmitted() && $form->isValid() ) {
            $formData   = $form->getData();
            //echo '<pre>'; var_dump( $formData ); die;
            
            $response   = $this->vsProject->createIssue( $formData );
            echo '<pre>'; var_dump( $response ); die;
            
            return $this->redirect( $this->generateUrl( 'vs_application_project_issues_index' ) );
        }
        
        return $this->render( '@VSApplication/Pages/ProjectIssues/create.html.twig', [
            'form'      => $form,
            'itemId'    => 0,
            
            'labelsWhitelist'   => $tagsContext->getTagsArray(),
        ]);
    }
    
    public function updateAction( $id, Request $request ): Response
    {
        $tagsContext    = $this->tagsWhitelistContextRepository->findByTaxonCode( 'project-issue-labels' );
        
        //$issue  = $this->vsProject->getIssue( $id );
        $form           = $this->createForm( ProjectIssueForm::class );
        $form->handleRequest( $request );
        if( $form->isSubmitted() && $form->isValid() ) {
            $formData   = $form->getData();
            //echo '<pre>'; var_dump( $formData ); die;
            
            $response = $this->vsProject->updateIssue( $formData );
            echo '<pre>'; var_dump( $response ); die;
            
            return $this->redirect( $this->generateUrl( 'vs_application_project_issues_index' ) );
        }
        
        return $this->render( '@VSApplication/Pages/ProjectIssues/update.html.twig', [
            'form'      => $form,
            'itemId'    => $id,
            
            'labelsWhitelist'   => $tagsContext->getTagsArray(),
        ]);
    }
}