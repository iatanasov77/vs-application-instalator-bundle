<?php namespace VS\ApplicationBundle\Component\Menu;

use Symfony\Component\HttpFoundation\Request;
class PathRolesService
{
    protected $accessMap;
    
    public function __construct( $accessMap )
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
