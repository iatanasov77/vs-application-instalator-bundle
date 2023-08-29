<?php namespace Vankosoft\ApplicationBundle\Component\Menu;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Http\AccessMap;

class PathRolesService
{
    /** @var AccessMap */
    protected $accessMap;
    
    public function __construct( AccessMap $accessMap )
    {
        $this->accessMap = $accessMap;
    }
    
    /**
     * Get User Roles Is Granted for $path
     * 
     * @param string $path
     * @return array
     */
    public function getRoles( $path )
    {
        $request                    = Request::create( $path, 'GET' ); //build a request based on path to check access 
        list( $roles, $channel )    = $this->accessMap->getPatterns( $request ); //get access_control for this request
        
        return $roles;
    }
}
