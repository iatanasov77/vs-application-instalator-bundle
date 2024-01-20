<?php namespace Vankosoft\ApplicationBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Contracts\Cache\CacheInterface;
use Doctrine\Persistence\ManagerRegistry;
use Sylius\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository;
use Sylius\Component\Resource\Factory\Factory;
use Vankosoft\ApplicationBundle\Component\Widget\WidgetInterface;

/**
 * CLONED FROM: \Pd\WidgetBundle\Controller\WidgetController
 */
class WidgetsConfigsController extends AbstractController
{
    /** @var CacheInterface */
    protected $cache;
    
    /** @var ManagerRegistry */
    protected $doctrine;
    
    /** @var WidgetInterface */
    protected $widgets;
    
    /** @var EntityRepository */
    protected $widgetRepository;
    
    /** @var Factory */
    protected $widgetFactory;
    
    public function __construct(
        CacheInterface $cache,
        ManagerRegistry $doctrine,
        WidgetInterface $widgets,
        EntityRepository $widgetRepository,
        Factory $widgetFactory
    ) {
        $this->cache            = $cache;
        $this->doctrine         = $doctrine;
        $this->widgets          = $widgets;
        $this->widgetRepository = $widgetRepository;
        $this->widgetFactory    = $widgetFactory;
    }
    
    public function index( Request $request ): Response
    {
        $widgets    = $this->widgetRepository->findAll();
        
        return $this->render( '@VSApplication/Pages/WidgetsConfigs/index.html.twig', ['widgets' => $widgets] );
    }
    
    /**
     * Used to Load New Widgets into Database
     * 
     * @param Request $reques
     * @return Response
     */
    public function refresh( Request $request ): Response
    {
        $this->widgets->loadWidgets( $this->getUser() );
        
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
            $widgetConfig = $this->widgetRepository->findOneBy( ['owner' => $this->getUser()] ) ??
                            ( $this->widgetFactory->createNew() )->setOwner( $this->getUser() );
            
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
            $widgetConfig = $this->widgetRepository->findOneBy( ['owner' => $this->getUser()] ) ??
                            ( $this->widgetFactory->createNew() )->setOwner( $this->getUser() );
            
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
            $this->cache->delete( $widgetId . $this->getUser()->getId() );
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
            $widgetConfig = $this->widgetRepository->findOneBy( ['owner' => $this->getUser()] ) ??
                            ( $this->widgetFactory->createNew() )->setOwner( $this->getUser() );
            
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