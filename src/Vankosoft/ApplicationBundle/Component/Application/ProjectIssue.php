<?php namespace Vankosoft\ApplicationBundle\Component\Application;

use Symfony\Contracts\HttpClient\ResponseInterface;
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
        
        return $this->processApiResponse( $response );
    }
    
    public function getIssue( int $id ): array
    {
        $apiToken       = $this->login();
        $issuesEndpoint = $this->apiConnection['host'] . '/project-issues/' . $id;
        
        $response       = $this->httpClient->request( 'GET', $issuesEndpoint, [
            'headers'   => [
                'Authorization' => 'Bearer ' . $apiToken,
            ],
            'query'      => [
                'projectSlug' => $this->projectSlug
            ],
        ]);
        
        return $this->processApiResponse( $response );
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
        
        return $this->processApiResponse( $response );
    }
    
    public function updateIssue( int $id, array $formData )
    {
        $apiToken       = $this->login();
        $issuesEndpoint = $this->apiConnection['host'] . '/project-issues/' . $id;
        
        $formData['projectSlug']    = $this->projectSlug;
        $response       = $this->httpClient->request( 'PUT', $issuesEndpoint, [
            'headers'   => [
                'Authorization' => 'Bearer ' . $apiToken,
            ],
            'json'      => $formData,
        ]);
        
        return $this->processApiResponse( $response );
    }
    
    public function deleteIssue( int $id )
    {
        $apiToken       = $this->login();
        $issuesEndpoint = $this->apiConnection['host'] . '/project-issues/' . $id;
        
        $formData       = [
            'projectSlug'   => $this->projectSlug,
        ];
        $response = $this->httpClient->request('DELETE', $issuesEndpoint, [
            'headers'   => [
                'Authorization' => 'Bearer ' . $apiToken,
            ],
            'json'      => $formData,
        ]);
        
        return $this->processApiResponse( $response );
    }
    
    public function getIssueLabelWhitelist(): array
    {
        $apiToken       = $this->login();
        $issuesEndpoint = $this->apiConnection['host'] . '/project-issue-label-whitelist';
        
        $response       = $this->httpClient->request( 'GET', $issuesEndpoint, [
            'headers'   => [
                'Authorization' => 'Bearer ' . $apiToken,
            ],
        ]);
        
        return $this->processApiResponse( $response );
    }
    
    private function processApiResponse( ResponseInterface $response ): array
    {
        try {
            //echo '<pre>'; var_dump( $response ); die;
            $payload = $response->toArray( false );
        } catch ( \JsonException $e ) {
            //echo '<pre>'; var_dump( $e ); die;
            throw new VankosoftApiException( 'Invalid JSON Payload !!!' );
        }
        //echo '<pre>'; var_dump( $payload ); die;
        
        if ( ! isset( $payload['status'] ) || $payload['status'] == Status::STATUS_ERROR ) {
            //echo '<pre>'; var_dump( $payload ); die;
            throw new VankosoftApiException( 'ERROR: ' . $payload['message'] );
        }
        
        return $payload['payload'];
    }
}