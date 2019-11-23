<?php namespace IA\CmsBundle\Component\Menu;

use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;
use Symfony\Component\Security\Core\Authorization\AuthorizationChecker;
use Symfony\Component\HttpFoundation\RequestStack;

use Knp\Menu\FactoryInterface;
use Knp\Menu\Matcher\Voter\RouteVoter;

class MenuBuilder implements ContainerAwareInterface
{
    use ContainerAwareTrait;
    
    protected $securityContext;
    
    protected $isLoggedIn;
    
    protected $isAdmin;
    
    public function __construct( AuthorizationChecker $securityContext, RequestStack $requestStack )
    {
        $this->requestStack = $requestStack;
        $this->securityContext = $securityContext;
        $this->isLoggedIn = $this->securityContext->isGranted('IS_AUTHENTICATED_FULLY');
        $this->isAdmin = $this->securityContext->isGranted('ROLE_ADMIN');
    }
    
    public function mainMenu( FactoryInterface $factory, $menuConfig )
    {
        $request    = $this->requestStack->getCurrentRequest();
        $menu       = $factory->createItem('root');
        
        foreach ( $menuConfig as $mg ) { // Menu Groups
            $menu->addChild( $mg['name'], [
                'uri' => 'javascript:;', 'attributes' => ['iconClass' => 'icon_document_alt']
            ]);
            
            foreach ( $mg['childs'] as $mi ) { // Menu Group Items
                $menu[$mg['name']]->addChild( $mi['name'], ['route' => $mi['route']] );
            }
        }
        
        return $menu;
    }
    
    public function profileMenu( FactoryInterface $factory )
    {
        $menu = $factory->createItem('root');
        
        $menu->addChild('My Profile', array('route' => 'ia_users_profile_show', 'attributes' => array('iconClass' => 'fas fa-user mr-2')));
        $menu->addChild('Log Out', array('route' => 'app_logout', 'attributes' => array('iconClass' => 'fas fa-power-off mr-2')));
        $menu->addChild('Documentation', array('uri' => 'javascript:;', 'attributes' => array('iconClass' => 'fas fa-cog mr-2')));
        
        return $menu;
    }
    
    public function breadcrumbsMenu( FactoryInterface $factory, $menuConfig )
    {
        $bcmenu = $this->mainMenu( $factory, $menuConfig );
        return $this->getCurrentMenuItem($bcmenu) ?: $factory->createItem('Edit');
    }
    
    public function getCurrentMenuItem($menu)
    {
        $voter = new RouteVoter($this->container->get('request_stack'));
        
        foreach ($menu as $item) {
            if ($voter->matchItem($item)) {
                return $item;
            }
            
            if ($item->getChildren() && $currentChild = $this->getCurrentMenuItem($item)) {
                return $currentChild;
            }
        }
        
        return null;
    }
    
}
