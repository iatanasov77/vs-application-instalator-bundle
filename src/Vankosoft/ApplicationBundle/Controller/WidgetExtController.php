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

/**
 * CLONED FROM: \Pd\WidgetBundle\Controller\WidgetController
 */
class WidgetExtController extends AbstractController
{
    /** @var CacheInterface */
    protected $cache;
    
    /** @var ManagerRegistry */
    protected $doctrine;
    
    /** @var EntityRepository */
    protected $widgetRepository;
    
    /** @var Factory */
    protected $widgetFactory;
    
    public function __construct(
        CacheInterface $cache,
        ManagerRegistry $doctrine,
        EntityRepository $widgetRepository,
        Factory $widgetFactory
    ) {
        $this->cache            = $cache;
        $this->doctrine         = $doctrine;
        $this->widgetRepository = $widgetRepository;
        $this->widgetFactory    = $widgetFactory;
    }
    
    public function index( Request $reques ): Response
    {
        $widgets    = $this->widgetRepository->findAll();
        
        return $this->render( '@VSApplication/Pages/Widgets/index.html.twig', ['widgets' => $widgets] );
    }
    
    /**
     * Change Widget Status.
     */
    public function status( Request $request, WidgetInterface $widget, string $widgetId, bool $status = true ): RedirectResponse
    {
        // Build Widget
        $widgets = $widget->getWidgets();
        
        if ( isset( $widgets[$widgetId] ) ) {
            // Get User Widgets
            $widgetConfig = $this->widgetRepository->findOneBy( ['owner' => $this->getUser()] ) ??
                            ( $this->widgetFactory->createNew() )->setOwner( $this->getUser() );
            
            // Add Config Parameters
            $widgetConfig->addWidgetConfig( $widgetId, ['status' => $status] );
            
            // Save
            $em = $this->getDoctrine()->getManager();
            $em->persist( $widgetConfig );
            $em->flush();
        }
        
        // Response
        return $this->redirect( $request->headers->get( 'referer', $this->generateUrl( $this->getParameter( 'vs_application.widgets.return_route' ) ) ) );
    }
    
    /**
     * Change Widget Configuration.
     */
    public function configs( Request $request, WidgetInterface $widget, string $widgetId ): RedirectResponse
    {
        // Build Widget
        $widgets = $widget->getWidgets();
        
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
            $em = $this->getDoctrine()->getManager();
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
    public function order( WidgetInterface $widget, string $widgetId, int $order ): JsonResponse
    {
        // Build Widget
        $widgets = $widget->getWidgets();
        
        if ( isset( $widgets[$widgetId] ) ) {
            // Get User Widgets
            $widgetConfig = $this->widgetRepository->findOneBy( ['owner' => $this->getUser()] ) ??
                            ( $this->widgetFactory->createNew() )->setOwner( $this->getUser() );
            
            // Add Config Parameters
            $widgetConfig->addWidgetConfig( $widgetId, ['order' => $order] );
            
            // Save
            $em = $this->getDoctrine()->getManager();
            $em->persist( $widgetConfig );
            $em->flush();
        }
        
        // Response
        return $this->json([
            'result' => 'success',
        ]);
    }
    
    protected function getDoctrine()
    {
        return $this->doctrine;
    }
}