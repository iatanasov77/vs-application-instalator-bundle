<?php namespace Vankosoft\ApplicationBundle\Component\Application;

use Symfony\Contracts\HttpClient\HttpClientInterface;
use Psr\Cache\CacheItemPoolInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Exception\JWTEncodeFailureException;
use Vankosoft\ApplicationBundle\Component\Exception\VankosoftApiException;

class ProjectApiClient implements ProjectApiClientInterface
{
    /** @var HttpClientInterface */
    protected $httpClient;
    
    /** @var CacheItemPoolInterface */
    protected $cache;
    
    public function __construct(
        HttpClientInterface $httpClient,
        CacheItemPoolInterface $cache
    ) {
        $this->httpClient   = $httpClient;
        $this->cache        = $cache;
    }
    
    /**
     * @inheritdoc
     */
    public function login(): string
    {
        $vankosoftApiHost   = $this->getParameter( 'vs_application.vankosoft_api.host' );
        $apiLoginUrl        = $vankosoftApiHost . '/login_check';
        
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
        
        try {
            echo '<pre>'; var_dump( $response ); die;
            $payload = $response->toArray( false );
        } catch ( \JsonException $e ) {
            throw new VankosoftApiException( 'Invalid JSON Payload !!!' );
        }
        echo '<pre>'; var_dump( $payload ); die;
        
        return $payload['payload']['token'];
    }
}