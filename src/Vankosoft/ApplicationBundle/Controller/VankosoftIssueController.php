<?php namespace Vankosoft\ApplicationBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use Vankosoft\ApplicationBundle\Component\Application\ProjectIssue;
use Vankosoft\ApplicationBundle\Form\ProjectIssueForm;

class VankosoftIssueController extends AbstractController
{
    /** @var ProjectIssue */
    private $vsProject;
    
    public function __construct(
        ProjectIssue $vsProject
    ) {
        $this->vsProject    = $vsProject;
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
        $tagsContext    = $this->get( 'vs_application.repository.tags_whitelist_context' )->findByTaxonCode( 'project-issue-labels' );
        
        //$issue = $this->vsProject->createIssue();
        $form   = $this->createForm( ProjectIssueForm::class );
        
        return $this->render( '@VSApplication/Pages/ProjectIssues/create.html.twig', [
            'form'      => $form,
            'itemId'    => 0,
            
            'labelsWhitelist'   => $tagsContext->getTagsArray(),
        ]);
    }
    
    public function updateAction( $id, Request $request ): Response
    {
        $tagsContext    = $this->get( 'vs_application.repository.tags_whitelist_context' )->findByTaxonCode( 'project-issue-labels' );
        
        //$issue  = $this->vsProject->getIssue( $id );
        $form   = $this->createForm( ProjectIssueForm::class );
        
        return $this->render( '@VSApplication/Pages/ProjectIssues/update.html.twig', [
            'form'      => $form,
            'itemId'    => $id,
            
            'labelsWhitelist'   => $tagsContext->getTagsArray(),
        ]);
    }
}