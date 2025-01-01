<?php namespace Vankosoft\ApplicationBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Form\FormInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;

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
        $labelsWhitelist    = $this->vsProject->getIssueLabelWhitelist();
        
        //$issue = $this->vsProject->createIssue();
        $form               = $this->createIssueForm();
        $form->handleRequest( $request );
        if( $form->isSubmitted() && $form->isValid() ) {
            $formData   = $form->getData();
            //echo '<pre>'; var_dump( $formData ); die;
            
            $response   = $this->vsProject->createIssue( $formData );
            //echo '<pre>'; var_dump( $response ); die;
            
            if ( $form->getClickedButton() && 'btnApply' === $form->getClickedButton()->getName() ) {
                return $this->redirect( $this->generateUrl( 'vs_application_project_issues_update', ['id' => $response['issue_id']] ) );
            } else {
                return $this->redirect( $this->generateUrl( 'vs_application_project_issues_index' ) );
            }
        }
        
        return $this->render( '@VSApplication/Pages/ProjectIssues/create.html.twig', [
            'form'              => $form,
            'itemId'            => 0,
            
            'labelsWhitelist'   => $labelsWhitelist,
        ]);
    }
    
    public function updateAction( $id, Request $request ): Response
    {
        $response           = $this->vsProject->getIssue( intval( $id ) );
        $labelsWhitelist    = $this->vsProject->getIssueLabelWhitelist();
        
        //$issue  = $this->vsProject->getIssue( $id );
        $form               = $this->createIssueForm( $response );
        $form->handleRequest( $request );
        if( $form->isSubmitted() && $form->isValid() ) {
            $formData   = $form->getData();
            //echo '<pre>'; var_dump( $formData ); die;
            
            $response = $this->vsProject->updateIssue( intval( $id ), $formData );
            //echo '<pre>'; var_dump( $response ); die;
            
            if ( $form->getClickedButton() && 'btnApply' === $form->getClickedButton()->getName() ) {
                return $this->redirect( $this->generateUrl( 'vs_application_project_issues_update', ['id' => $response['issue_id']] ) );
            } else {
                return $this->redirect( $this->generateUrl( 'vs_application_project_issues_index' ) );
            }
        }
        
        return $this->render( '@VSApplication/Pages/ProjectIssues/update.html.twig', [
            'form'              => $form,
            'itemId'            => $id,
            
            'labelsWhitelist'   => $labelsWhitelist,
        ]);
    }
    
    public function deleteAction( $id, Request $request ): Response
    {
        $response   = $this->vsProject->deleteIssue( intval( $id ) );
        
        return $this->redirect( $this->generateUrl( 'vs_application_project_issues_index' ) );
    }
    
    private function createIssueForm( ?array $issueData = null ): FormInterface
    {
        return $this->createForm( ProjectIssueForm::class, $issueData, [
            'ckeditor_uiColor'              => $this->getParameter( 'vs_cms.form.pages.ckeditor_uiColor' ),
            'ckeditor_toolbar'              => $this->getParameter( 'vs_cms.form.pages.ckeditor_toolbar' ),
            'ckeditor_extraPlugins'         => $this->getParameter( 'vs_cms.form.pages.ckeditor_extraPlugins' ),
            'ckeditor_removeButtons'        => $this->getParameter( 'vs_cms.form.pages.ckeditor_removeButtons' ),
            'ckeditor_allowedContent'       => $this->getParameter( 'vs_cms.form.pages.ckeditor_allowedContent' ),
            'ckeditor_extraAllowedContent'  => $this->getParameter( 'vs_cms.form.pages.ckeditor_extraAllowedContent' ),
        ]);
    }
}