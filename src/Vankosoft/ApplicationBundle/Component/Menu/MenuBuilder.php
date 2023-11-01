<?php namespace Vankosoft\ApplicationBundle\Component\Menu;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Security\Core\Authorization\AuthorizationChecker;
use Symfony\Component\Yaml\Yaml;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authorization\Voter\VoterInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\Component\HttpFoundation\RequestStack;

use Knp\Menu\FactoryInterface;
use Knp\Menu\Matcher\Voter\RouteVoter;

use Vankosoft\ApplicationBundle\Component\Menu\PathRolesService;
use Vankosoft\ApplicationBundle\Component\Menu\Item\DividerMenuItem;

class MenuBuilder
{
    /** @var AuthorizationChecker */
    protected $security;
    
    /** @var RouterInterface */
    protected $router;
    
    /** @var array */
    protected $menuConfig;
    
    protected $request;
    
    /** @var RequestStack */
    protected $requestStack;
    
    /** @var ContainerBuilder */
    protected $cb;
    
    /** @var PathRolesService */
    protected $pathRolesService;
    
    /** @var TranslatorInterface */
    protected $translator;
    
    /** @var FactoryInterface */
    protected FactoryInterface $factory;
    
    /** @var string */
    protected $currentPath;
    
    public function __construct(
        string $config_file,
        AuthorizationChecker $security,
        PathRolesService $pathRolesService,
        RouterInterface $router,
        ParameterBagInterface $parameterBag,
        TranslatorInterface $translator,
        RequestStack $requestStack
    ) {
        $config                 = Yaml::parse( file_get_contents( $config_file ) );
        $this->menuConfig       = $config['vs_application']['menu'];
        
        $this->security         = $security;
        $this->router           = $router;
        
        $this->cb               = new ContainerBuilder( $parameterBag );
        
        $this->pathRolesService = $pathRolesService;
        
        $this->translator       = $translator;
        
        $this->currentPath      = $requestStack->getMainRequest()->getRequestUri();
        
        $this->requestStack     = $requestStack;
    }
    
    public function mainMenu( FactoryInterface $factory, string $menuName = 'mainMenu' )
    {
        $this->factory  = $factory;
        $this->request  = $this->requestStack->getCurrentRequest();
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
        $voter = new RouteVoter( $this->requestStack );
        
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
        $path   = null;
        
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
            
            $menuName   = $this->translator->trans( $mg['name'], [], 'VSApplicationBundle' );
            $child      = $menu->addChild( $menuName, $params );
            if ( isset( $mg['display'] ) && $mg['display'] == false ) {
                $child->setDisplay( false );
            }
            
            if ( isset( $mg['childs'] ) && is_array( $mg['childs'] ) ) {
                $isGranted  = $this->build( $menu[$menuName], $mg['childs'] );
                
                if ( ! empty( $mg['childs'] ) && ! $isGranted ) {
                    // Not Sure if this should exist
                    //$menu->removeChild( $menuName );
                } else {
                    $child->setDisplay( $isGranted );
                }
            }
            
            if ( $path == $this->currentPath ) {
                $child->setCurrent( true );
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
