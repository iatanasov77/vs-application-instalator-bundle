<?php namespace Vankosoft\ApplicationBundle\Component\Application;

use Vankosoft\ApplicationBundle\Component\Exception\VankosoftApiException;

final class ProjectIssue extends ProjectApiClient
{
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
        
        return $payload;
    }
}