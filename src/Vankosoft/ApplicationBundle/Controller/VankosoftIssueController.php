<?php namespace Vankosoft\ApplicationBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Psr\Cache\CacheItemPoolInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Exception\JWTEncodeFailureException;
use Vankosoft\ApiBundle\Exception\ApiLoginException;

class VankosoftIssueController extends AbstractController
{
    /** @var HttpClientInterface */
    private $httpClient;
    
    /** @var CacheItemPoolInterface */
    private $cache;
    
    public function __construct(
        HttpClientInterface $httpClient,
        CacheItemPoolInterface $cache
    ) {
        $this->httpClient   = $httpClient;
        $this->cache        = $cache;
    }
    
    public function indexAction( Request $request ): Response
    {
        $vankosoftApiHost   = $this->getParameter( 'vs_application.vankosoft_api.host' );
        $apiLoginUrl        = $vankosoftApiHost . '/login_check';
        //$apiLoginUrl    = 'http://vankosoft.lh/api/project-issues';
        
        try {
            $response       = $this->httpClient->request( 'POST', $apiLoginUrl, [
                'json' => [
                    'username' => 'admin',
                    'password' => 'admin'
                ],
            ]);
        }  catch ( JWTEncodeFailureException $e ) {
            //throw new ApiLoginException( 'JWTEncodeFailureException: ' . $e->getMessage() );
        }
        
        //echo '<pre>'; var_dump( $response ); die;
        $payload = $response->toArray( false );
        echo '<pre>'; var_dump( $payload ); die;
        
        return $this->render( '@VSApplication/Pages/ProjectIssues/index.html.twig', [
            'issues'    => [],
        ]);
    }
}