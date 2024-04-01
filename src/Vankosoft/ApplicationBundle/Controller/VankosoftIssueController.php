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
        $form   = $this->createForm( ProjectIssueForm::class );
        
        return $this->render( '@VSApplication/Pages/ProjectIssues/create.html.twig', [
            'form'    => $form,
        ]);
    }
    
    public function updateAction( $id, Request $request ): Response
    {
        $form   = $this->createForm( ProjectIssueForm::class );
        
        return $this->render( '@VSApplication/Pages/ProjectIssues/update.html.twig', [
            'form'    => $form,
        ]);
    }
}