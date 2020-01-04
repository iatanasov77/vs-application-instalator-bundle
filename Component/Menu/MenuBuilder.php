<?php namespace IA\CmsBundle\Component\Menu;

use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;
use Symfony\Component\Security\Core\Authorization\AuthorizationChecker;
use Symfony\Component\Yaml\Yaml;

use Knp\Menu\FactoryInterface;
use Knp\Menu\Matcher\Voter\RouteVoter;

class MenuBuilder implements ContainerAwareInterface
{
    use ContainerAwareTrait;
    
    protected $securityContext;
    
    protected $isLoggedIn;
    
    protected $isAdmin;
    
    protected $menuConfig;
    
    protected $request;
    
    public function __construct( AuthorizationChecker $securityContext, string $config_file )
    {
        $this->securityContext  = $securityContext;
        
        $config                 = Yaml::parse( file_get_contents( $config_file ) );
        $this->menuConfig       = $config['ia_cms']['menu'];
        
        $this->isLoggedIn       = $this->securityContext->isGranted( 'IS_AUTHENTICATED_FULLY' );
        $this->isAdmin          = $this->securityContext->isGranted( 'ROLE_ADMIN' );
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
                'uri'       => isset( $mg['uri'] ) ? $mg['uri'] : null,
                'route'     => isset( $mg['route'] ) ? $mg['route'] : null,
                'attributes'=> isset( $mg['attributes'] ) ? $mg['attributes'] : [],
            ];
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
}
