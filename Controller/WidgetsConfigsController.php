<?php namespace Vankosoft\ApplicationBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Psr\Cache\CacheItemPoolInterface;
use Doctrine\Persistence\ManagerRegistry;
use Sylius\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository;
use Sylius\Component\Resource\Factory\Factory;
use Vankosoft\ApplicationBundle\Component\Widget\WidgetInterface;

/**
 * CLONED FROM: \Pd\WidgetBundle\Controller\WidgetController
 */
class WidgetsConfigsController extends AbstractController
{
    /** @var CacheItemPoolInterface */
    protected $cache;
    
    /** @var ManagerRegistry */
    protected $doctrine;
    
    /** @var WidgetInterface */
    protected $widgets;
    
    /** @var EntityRepository */
    protected $widgetConfigRepository;
    
    /** @var Factory */
    protected $widgetConfigFactory;
    
    /** @var EntityRepository */
    protected $widgetRepository;
    
    /** @var EntityRepository */
    protected $usersRepository;
    
    public function __construct(
        CacheItemPoolInterface $cache,
        ManagerRegistry $doctrine,
        WidgetInterface $widgets,
        EntityRepository $widgetConfigRepository,
        Factory $widgetConfigFactory,
        EntityRepository $widgetRepository,
        EntityRepository $usersRepository
    ) {
        $this->cache                    = $cache;
        $this->doctrine                 = $doctrine;
        $this->widgets                  = $widgets;
        $this->widgetConfigRepository   = $widgetConfigRepository;
        $this->widgetConfigFactory      = $widgetConfigFactory;
        $this->widgetRepository         = $widgetRepository;
        $this->usersRepository          = $usersRepository;
    }
    
    public function index( Request $request ): Response
    {
        $widgets    = $this->widgetConfigRepository->findAll();
        
        return $this->render( '@VSApplication/Pages/WidgetsConfigs/index.html.twig', ['widgets' => $widgets] );
    }
    
    public function load( $widgetId, Request $request ): Response
    {
        $widget = $this->widgets->createWidgetItem( $widgetId );
        if ( $widget ) {
            $this->widgets->addWidget( $widget );
            $this->widgets->loadWidgets( $this->getUser() );
        }
        
        // Response
        return $this->redirect( $request->headers->get( 'referer', $this->generateUrl( $this->getParameter( 'vs_application.widgets.return_route' ) ) ) );
    }
    
    /**
     * Used to Load New Widgets into Database
     * 
     * @param Request $reques
     * @return Response
     */
    public function refresh( $all, Request $request ): Response
    {
        if ( $all ) {
            $this->widgets->loadWidgets( $this->getUser(), false, true );
        } else {
            $this->widgets->loadWidgets( $this->getUser() );
        }
        
        // Response
        return $this->redirect( $request->headers->get( 'referer', $this->generateUrl( $this->getParameter( 'vs_application.widgets.return_route' ) ) ) );
    }
    
    /**
     * Used to Load New Widgets into Database
     *
     * @param Request $reques
     * @return Response
     */
    public function refreshAllUsers( $all, Request $request ): Response
    {
        $users  = $this->usersRepository->findAll();
        foreach ( $users as $user ) {
            if ( $all ) {
                $this->widgets->loadWidgets( $user, false, true );
            } else {
                $this->widgets->loadWidgets( $user );
            }
        }
        
        // Response
        return $this->redirect( $request->headers->get( 'referer', $this->generateUrl( $this->getParameter( 'vs_application.widgets.return_route' ) ) ) );
    }
    
    /**
     * Change Widget Status.
     */
    public function status( Request $request, string $widgetId, bool $status = true ): RedirectResponse
    {
        // Build Widget
        $widgets = $this->widgets->getWidgets();
        
        if ( isset( $widgets[$widgetId] ) ) {
            // Get User Widgets
            $widgetConfig = $this->widgetConfigRepository->findOneBy( ['owner' => $this->getUser()] ) ??
                                ( $this->widgetConfigFactory->createNew() )->setOwner( $this->getUser() );
            
            // Add Config Parameters
            $widgetConfig->addWidgetConfig( $widgetId, ['status' => $status] );
            
            // Save
            $em = $this->doctrine->getManager();
            $em->persist( $widgetConfig );
            $em->flush();
        }
        
        // Response
        return $this->redirect( $request->headers->get( 'referer', $this->generateUrl( $this->getParameter( 'vs_application.widgets.return_route' ) ) ) );
    }
    
    /**
     * Change Widget Configuration.
     */
    public function configs( Request $request, string $widgetId ): RedirectResponse
    {
        // Build Widget
        $widgets = $this->widgets->getWidgets();
        
        if ( isset( $widgets[$widgetId] ) ) {
            // Get User Widgets
            $widgetConfig = $this->widgetConfigRepository->findOneBy( ['owner' => $this->getUser()] ) ??
                                ( $this->widgetConfigFactory->createNew() )->setOwner( $this->getUser() );
            
            // Add or Remove Config Parameters
            if ( $request->get( 'remove' ) ) {
                $widgetConfig->removeWidgetConfig( $widgetId, $widgets[$widgetId]->getConfigProcess( $request ) ?? [] );
            } else {
                $widgetConfig->addWidgetConfig( $widgetId, $widgets[$widgetId]->getConfigProcess( $request ) ?? [] );
            }
            
            // Save
            $em = $this->doctrine->getManager();
            $em->persist( $widgetConfig );
            $em->flush();
            
            // Flush Widget Cache
            $this->cache->delete( $widgetId . $this->getUser() ? $this->getUser()->getId() : '' );
        }
        
        // Response
        return $this->redirect( $request->headers->get( 'referer', $this->generateUrl( $this->getParameter( 'vs_application.widgets.return_route' ) ) ) );
    }
    
    /**
     * Change Widget Order.
     */
    public function order( Request $request, string $widgetId, int $order ): JsonResponse
    {
        // Build Widget
        $widgets = $this->widgets->getWidgets();
        
        if ( isset( $widgets[$widgetId] ) ) {
            // Get User Widgets
            $widgetConfig = $this->widgetConfigRepository->findOneBy( ['owner' => $this->getUser()] ) ??
                                ( $this->widgetConfigFactory->createNew() )->setOwner( $this->getUser() );
            
            // Add Config Parameters
            $widgetConfig->addWidgetConfig( $widgetId, ['order' => $order] );
            
            // Save
            $em = $this->doctrine->getManager();
            $em->persist( $widgetConfig );
            $em->flush();
        }
        
        // Response
        return $this->json([
            'result' => 'success',
        ]);
    }
}