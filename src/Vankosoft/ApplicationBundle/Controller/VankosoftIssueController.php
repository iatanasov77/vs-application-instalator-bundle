<?php namespace Vankosoft\ApplicationBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use Vankosoft\ApplicationBundle\Component\Application\ProjectIssue;

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
}