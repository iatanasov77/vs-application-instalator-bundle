<?php namespace IA\CmsBundle\Component\Menu;

use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Security\Core\Authorization\AuthorizationChecker;
use Symfony\Component\Yaml\Yaml;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authorization\Voter\VoterInterface;

use Knp\Menu\FactoryInterface;
use Knp\Menu\Matcher\Voter\RouteVoter;

class MenuBuilder implements ContainerAwareInterface
{
    use ContainerAwareTrait;
    
    protected $security;
    
    protected $router;
    
    protected $menuConfig;
    
    protected $request;
    
    // ContainerBuilder
    protected $cb;
    
    public function __construct( string $config_file, AuthorizationChecker $security, RouterInterface $router )
    {
        $config             = Yaml::parse( file_get_contents( $config_file ) );
        $this->menuConfig   = $config['ia_cms']['menu'];
        
        $this->security     = $security;
        $this->router       = $router;
        
        $this->cb           = new ContainerBuilder();
    }
    
    public function mainMenu( FactoryInterface $factory )
    {
        $this->request  = $this->container->get( 'request_stack' )->getCurrentRequest();
        $menu           = $factory->createItem( 'root' );
        
        if ( ! isset( $this->menuConfig['mainMenu'] ) ) {
            throw new \Exception( '"mainMenu" node must be provided at "ia_cms.yaml" config file.' );
        }
        $this->build( $menu, $this->menuConfig['mainMenu'] );

        return $menu;
    }
    
    public function profileMenu( FactoryInterface $factory )
    {
        $menu = $factory->createItem( 'root' );
        
        if ( ! isset( $this->menuConfig['profileMenu'] ) ) {
            throw new \Exception( '"profileMenu" node must be provided at "ia_cms.yaml" config file.' );
        }
        $this->build( $menu, $this->menuConfig['profileMenu'] );
        
        return $menu;
    }
    
    public function breadcrumbsMenu( FactoryInterface $factory )
    {
        $bcmenu     = $this->mainMenu( $factory );
        $breadcrumb = $this->getCurrentMenuItem( $bcmenu );
        
        return $breadcrumb ? $breadcrumb : $factory->createItem( 'root' );
    }
    
    public function getCurrentMenuItem( $menu )
    {
        $voter = new RouteVoter( $this->container->get( 'request_stack' ) );
        
        foreach ( $menu as $item ) {
            if ( $voter->matchItem( $item ) ) {
                return $item;
            }
            
            if ( $item->getChildren() && $currentChild = $this->getCurrentMenuItem( $item ) ) {
                return $currentChild;
            }
        }
        
        return null;
    }
    
    private function build( &$menu, $config )
    {
        foreach ( $config as $mg ) {
            $params = [
                'uri'               => isset( $mg['uri'] ) ? $mg['uri'] : null,
                'route'             => isset( $mg['route'] ) ? $mg['route'] : null,
                'routeParameters'   => isset( $mg['routeParameters'] ) ? $mg['routeParameters'] : [],
                'attributes'        => isset( $mg['attributes'] ) ? $mg['attributes'] : [],
            ];
            
            //@NOTE Resolve ENV['HOST'] in the uri's
            if ( $params['uri'] ) {
                $params['uri'] = $this->cb->resolveEnvPlaceholders( $params['uri'], true );
            }
            
            if ( $params['route'] ) {
                $path       = $this->router->generate( $params['route'], $params['routeParameters'], RouterInterface::ABSOLUTE_PATH );
                if ( $this->security->isGranted( $path ) === VoterInterface::ACCESS_DENIED )
                    continue;
            }

            if ( isset( $mg['routeParameters'] ) && is_array( $mg['routeParameters'] ) ) {
                foreach( $mg['routeParameters'] as $rp => $type ) {
                    if ( $type == 'int' ) {
                        $params['routeParameters'][$rp] = (int)$this->request->get( $rp );
                    } else {
                        $params['routeParameters'][$rp] = $this->request->get( $rp );
                    }
                }
            }
            
            $child  = $menu->addChild( $mg['name'], $params );
            if ( isset( $mg['display'] ) && $mg['display'] == false ) {
                $child->setDisplay( false );
            }
            
            if ( isset( $mg['childs'] ) && is_array( $mg['childs'] ) ) {
                $this->build( $menu[$mg['name']], $mg['childs'] );
            }
        }
    }
    
    protected function routeAllowed( $route, $routeParams )
    {
        $security       = ['ROLE_ADMIN','ROLE_SUPPORT']; // can be empty array as well or security expression packed into an array
        $rolesToCheck   = ['ROLE_USER', 'ROLE_EDITOR', 'ROLE_PEER', 'ROLE_SUPPORT', 'ROLE_ADMIN'];
        
        $allowed_roles = $this->pathRoles->getRolesForRoute( $route, $routeParams, $rolesToCheck, $security );
    }
}
