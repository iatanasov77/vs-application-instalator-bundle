<?php namespace Vankosoft\ApplicationBundle\Component\Menu;

use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Security\Core\Authorization\AuthorizationChecker;
use Symfony\Component\Yaml\Yaml;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authorization\Voter\VoterInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

use Knp\Menu\FactoryInterface;
use Knp\Menu\Matcher\Voter\RouteVoter;

use Vankosoft\ApplicationBundle\Component\Menu\PathRolesService;
use Vankosoft\ApplicationBundle\Component\Menu\Item\DividerMenuItem;

class MenuBuilder implements ContainerAwareInterface
{
    use ContainerAwareTrait;
    
    protected $security;
    
    protected $router;
    
    protected $menuConfig;
    
    protected $request;
    
    // ContainerBuilder
    protected $cb;
    
    // PathRolesService
    protected $pathRolesService;
    
    protected FactoryInterface $factory;
    
    public function __construct(
        string $config_file,
        AuthorizationChecker $security,
        PathRolesService $pathRolesService,
        RouterInterface $router,
        ParameterBagInterface $parameterBag
    ) {
            $config                 = Yaml::parse( file_get_contents( $config_file ) );
            $this->menuConfig       = $config['vs_application']['menu'];
            
            $this->security         = $security;
            $this->router           = $router;
            
            $this->cb               = new ContainerBuilder( $parameterBag );
            
            $this->pathRolesService = $pathRolesService;
    }
    
    public function mainMenu( FactoryInterface $factory, string $menuName = 'mainMenu' )
    {
        $this->factory  = $factory;
        $this->request  = $this->container->get( 'request_stack' )->getCurrentRequest();
        $menu           = $factory->createItem( 'root' );
        
        if ( ! isset( $this->menuConfig[$menuName] ) ) {
            throw new \Exception( '"' . $menuName . '" node must be provided at "vs_application.yaml" config file.' );
        }
        $this->build( $menu, $this->menuConfig[$menuName] );
        
        return $menu;
    }
    
    public function profileMenu( FactoryInterface $factory )
    {
        $this->factory  = $factory;
        $menu = $factory->createItem( 'root' );
        
        if ( ! isset( $this->menuConfig['profileMenu'] ) ) {
            throw new \Exception( '"profileMenu" node must be provided at "vs_application.yaml" config file.' );
        }
        $this->build( $menu, $this->menuConfig['profileMenu'] );
        
        return $menu;
    }
    
    public function breadcrumbsMenu( FactoryInterface $factory, array $menus )
    {
        $this->factory  = $factory;
        foreach ( $menus as $menuAlias ) {
            $bcmenu     = $this->mainMenu( $factory, $menuAlias );
            $breadcrumb = $this->getCurrentMenuItem( $bcmenu );
            if ( $breadcrumb ) {
                break;
            }
        }
        
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
    
    protected function build( &$menu, $config )
    {
        foreach ( $config as $id => $mg ) {
            $hasGrantedChild    = false;
            
            $params = [
                'id'                => $id,
                'uri'               => isset( $mg['uri'] ) ? $mg['uri'] : null,
                'route'             => isset( $mg['route'] ) ? $mg['route'] : null,
                'routeParameters'   => isset( $mg['routeParameters'] ) ? $mg['routeParameters'] : [],
                'attributes'        => isset( $mg['attributes'] ) ? $mg['attributes'] : [],
                'isDivider'         => isset( $mg['isDivider'] ) ? $mg['isDivider'] : false,
            ];
            
            if ( $params['isDivider'] ) {
                $menu->addChild( new DividerMenuItem( $id, $this->factory ) );
                continue;
            }
            
            //@NOTE Resolve ENV['HOST'] in the uri's
            if ( $params['uri'] ) {
                $params['uri'] = $this->cb->resolveEnvPlaceholders( $params['uri'], true );
            }
            
            if ( $params['route'] ) {
                $path           = $this->router->generate( $params['route'], $params['routeParameters'], RouterInterface::ABSOLUTE_PATH );
                $roles          = $this->pathRolesService->getRoles( $path );
                $pathGranted    = false;
                if ( is_array( $roles ) ) {
                    foreach ( $roles as $pathRole ) {
                        $pathGranted    = false;
                        if ( $this->security->isGranted( $pathRole ) ) { // VoterInterface::ACCESS_ABSTAIN
                                                                         //VoterInterface::ACCESS_DENIED
                            $pathGranted        = true;
                            $hasGrantedChild    = true;
                            break;
                        }
                    }
                }
                
                if ( ! $pathGranted ) {
                    continue;
                }
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
                $isGranted  = $this->build( $menu[$mg['name']], $mg['childs'] );
                $child->setDisplay( $isGranted );
            }
        }
        
        return isset( $hasGrantedChild ) ? $hasGrantedChild : false;
    }
    
    protected function routeAllowed( $route, $routeParams )
    {
        $security       = ['ROLE_ADMIN','ROLE_SUPPORT']; // can be empty array as well or security expression packed into an array
        $rolesToCheck   = ['ROLE_USER', 'ROLE_EDITOR', 'ROLE_AUTHOR', 'ROLE_PEER', 'ROLE_SUPPORT', 'ROLE_ADMIN'];
        
        $allowed_roles = $this->pathRoles->getRolesForRoute( $route, $routeParams, $rolesToCheck, $security );
    }
}
