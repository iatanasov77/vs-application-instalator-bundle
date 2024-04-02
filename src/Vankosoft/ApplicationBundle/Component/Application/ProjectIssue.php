<?php namespace Vankosoft\ApplicationBundle\Component\Application;

use Vankosoft\ApplicationBundle\Component\Status;
use Vankosoft\ApplicationBundle\Component\Exception\VankosoftApiException;

final class ProjectIssue extends ProjectApiClient
{
    const ISSUE_OPENED      = 'opened';
    const ISSUE_CLOSED      = 'closed';
    const ISSUE_COMPLETED   = 'completed';
    
    const ISSUE_STATUS  = [
        self::ISSUE_OPENED      => 'vs_application.form.project_issue.status_opened',
        self::ISSUE_CLOSED      => 'vs_application.form.project_issue.status_closed',
        self::ISSUE_COMPLETED   => 'vs_application.form.project_issue.status_completed',
    ];
    
    
    
    public function getIssues(): array
    {
        $apiToken       = $this->login();
        $issuesEndpoint = $this->apiConnection['host'] . '/project-issues';
        
        $response       = $this->httpClient->request( 'GET', $issuesEndpoint, [
            'headers'   => [
                'Authorization' => 'Bearer ' . $apiToken,
            ],
            'query'      => [
                'projectSlug' => $this->projectSlug
            ],
        ]);
        
        try {
            //echo '<pre>'; var_dump( $response ); die;
            $payload = $response->toArray( false );
        } catch ( \JsonException $e ) {
            throw new VankosoftApiException( 'Invalid JSON Payload !!!' );
        }
        //echo '<pre>'; var_dump( $payload ); die;
        
        if ( ! isset( $payload['status'] )|| $payload['status'] == Status::STATUS_ERROR ) {
            throw new VankosoftApiException( 'ERROR: ' . $payload['message'] );
        }
        
        return $payload['payload'];
    }
    
    public function createIssue( array $formData )
    {
        $apiToken       = $this->login();
        $issuesEndpoint = $this->apiConnection['host'] . '/project-issues/new';
        
        $formData['projectSlug']    = $this->projectSlug;
        $response       = $this->httpClient->request( 'POST', $issuesEndpoint, [
            'headers'   => [
                'Authorization' => 'Bearer ' . $apiToken,
            ],
            'json'      => $formData,
        ]);
        
        try {
            //echo '<pre>'; var_dump( $response ); die;
            $payload = $response->toArray( false );
        } catch ( \JsonException $e ) {
            //echo '<pre>'; var_dump( $e ); die;
            throw new VankosoftApiException( 'Invalid JSON Payload !!!' );
        }
        //echo '<pre>'; var_dump( $payload ); die;
        
        if ( ! isset( $payload['status'] )|| $payload['status'] == Status::STATUS_ERROR ) {
            //echo '<pre>'; var_dump( $payload ); die;
            throw new VankosoftApiException( 'ERROR: ' . $payload['message'] );
        }
        
        return $payload['payload'];
    }
}