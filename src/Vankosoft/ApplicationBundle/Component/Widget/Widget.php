<?php namespace Vankosoft\ApplicationBundle\Component\Widget;

use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;
use Psr\Cache\CacheItemPoolInterface;
use Psr\Cache\InvalidArgumentException;
use Doctrine\Persistence\ManagerRegistry;
use Sylius\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository;
use Sylius\Component\Resource\Factory\Factory;

use Vankosoft\ApplicationBundle\Component\Widget\Builder\Item;
use Vankosoft\ApplicationBundle\Component\Widget\Builder\ItemInterface;
use Vankosoft\ApplicationBundle\EventListener\Event\WidgetEvent;
use Vankosoft\UsersBundle\Model\UserInterface;

class Widget implements WidgetInterface
{
    /** @var AuthorizationCheckerInterface */
    private $security;
    
    /** @var EventDispatcherInterface */
    private $eventDispatcher;
    
    /** @var CacheItemPoolInterface */
    private $cache;
    
    /** @var TokenStorageInterface */
    private $token;
    
    /** @var ManagerRegistry */
    private $doctrine;
    
    /** @var EntityRepository */
    private $widgetConfigRepository;
    
    /** @var Factory */
    private $widgetConfigFactory;
    
    /** @var EntityRepository */
    private $widgetRepository;
    
    /**
     * Widget Storage.
     *
     * @var array|ItemInterface[]
     */
    private array $widgets = [];
    
    /**
     * Widget Params
     * 
     * @var array
     */
    private array $widgetParams = [];
    
    
    private bool $checkRole;

    public function __construct(
        AuthorizationCheckerInterface $security,
        EventDispatcherInterface $eventDispatcher,
        CacheItemPoolInterface $cache,
        TokenStorageInterface $token,
        ManagerRegistry $doctrine,
        EntityRepository $widgetConfigRepository,
        Factory $widgetConfigFactory,
        EntityRepository $widgetRepository
    ) {
        $this->security                 = $security;
        $this->eventDispatcher          = $eventDispatcher;
        $this->cache                    = $cache;
        $this->token                    = $token;
        $this->doctrine                 = $doctrine;
        $this->widgetConfigRepository   = $widgetConfigRepository;
        $this->widgetConfigFactory      = $widgetConfigFactory;
        $this->widgetRepository         = $widgetRepository;
    }
    
    /**
     * 
     * @param string $widgetCode
     * @return ItemInterface|null
     */
    public function createWidgetItem( string $widgetCode, bool $checkRole = true ): ?ItemInterface
    {
        $this->checkRole    = $checkRole;
        $widget             = $this->widgetRepository->findOneBy( ['code' => $widgetCode] );
        
        if ( $widget ) {
            // Create Widget Item
            $widgetItem = new Item( $widget->getCode(), 3600 );
            $widgetItem->setGroup( $widget->getGroup()->getCode() )
                        ->setName( $widget->getName() )
                        ->setDescription( $widget->getDescription() )
                        ->setActive( $widget->getActive() );
            
            return $widgetItem;
        }
        
        return null;
    }
    
    /**
     * Used to Load Widgets into Database
     */
    public function loadWidgets( ?UserInterface $user )
    {
        // Build Widgets
        $widgets = $this->getWidgets();
        
        foreach ( $widgets as $widgetId => $widgetVal ) {
            // Get User Widgets
            $widgetConfig = $this->widgetConfigRepository->findOneBy( ['owner' => $user] ) ??
                                ( $this->widgetConfigFactory->createNew() )->setOwner( $user );
            
            // Add Config Parameters
            $widgetConfig->addWidgetConfig( $widgetId, ['status' => 1] );
            
            // Save
            $em = $this->doctrine->getManager();
            $em->persist( $widgetConfig );
            $em->flush();
            
            // Flush Widget Cache
            if ( $user ) {
                $this->cache->delete( $widgetId . $user->getId() );
            } else {
                $this->cache->delete( $widgetId );
            }
        }
    }
    
    /**
     * Get Widgets.
     *
     * @return ItemInterface[]|null
     */
    public function getWidgets( $checkRole = true ): ?array
    {
        // Check Role
        $this->checkRole = $checkRole;

        // Dispatch Event
        if ( ! $this->widgets ) {
            $this->eventDispatcher->dispatch( new WidgetEvent( $this ), WidgetEvent::WIDGET_START );
        }

        return $this->widgets;
    }

    /**
     * Add Widget
     */
    public function addWidget( ItemInterface $item ): WidgetInterface
    {
        // Check Security
        if ( $this->checkRole && $item->getRole() ) {
            $decide = true;
            foreach ( $item->getRole() as $role ) {
                if ( ! $this->security->isGranted( $role ) ) {
                    $decide = false;
                    break;
                }
            }

            if ( ! $decide ) {
                return $this;
            }
        }

        // Add
        $this->widgets[$item->getId()] = $item;

        return $this;
    }

    /**
     * Remove Widget.
     */
    public function removeWidget( string $widgetId ): WidgetInterface
    {
        if ( isset( $this->widgets[$widgetId] ) ) {
            unset( $this->widgets[$widgetId] );
        }

        return $this;
    }

    /**
     * Clear current user widget cache.
     */
    public function clearWidgetCache(): void
    {
        // Get Widgets
        $widgets    = $this->getWidgets( false );
        $userId     = $this->token->getToken()->getUser()->getId();

        // Clear Cache
        foreach ( $widgets as $widget ) {
            try {
                $this->cache->deleteItem( $widget->getId() . $userId );
            } catch ( InvalidArgumentException $e ) {
            }
        }
    }
    
    /**
     * Set Widget Params to Use in Widget Loader
     * 
     * @param array $widgetParams
     */
    public function setWidgetParams( array $widgetParams ): void
    {
        $this->widgetParams = $widgetParams;
    }
    
    /**
     * Get Widget Params
     * 
     * @return array
     */
    public function getWidgetParams(): array
    {
        return $this->widgetParams;
    }
}
