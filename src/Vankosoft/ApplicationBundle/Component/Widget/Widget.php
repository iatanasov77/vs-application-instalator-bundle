<?php namespace Vankosoft\ApplicationBundle\Component\Widget;

use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;
use Psr\Cache\CacheItemPoolInterface;
use Psr\Cache\InvalidArgumentException;
use Sylius\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository;
use Sylius\Component\Resource\Factory\Factory;

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
    
    /** @var EntityRepository */
    private $widgetRepository;
    
    /** @var Factory */
    private $widgetFactory;
    
    /**
     * Widget Storage.
     *
     * @var array|ItemInterface[]
     */
    private array $widgets = [];
    
    
    private bool $checkRole;

    public function __construct(
        AuthorizationCheckerInterface $security,
        EventDispatcherInterface $eventDispatcher,
        CacheItemPoolInterface $cache,
        TokenStorageInterface $token,
        EntityRepository $widgetRepository,
        Factory $widgetFactory
    ) {
        $this->security         = $security;
        $this->eventDispatcher  = $eventDispatcher;
        $this->cache            = $cache;
        $this->token            = $token;
        $this->widgetRepository = $widgetRepository;
        $this->widgetFactory    = $widgetFactory;
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
            $widgetConfig = $this->widgetRepository->findOneBy( ['owner' => $user] ) ??
                            ( $this->widgetFactory->createNew() )->setOwner( $user );
            
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
}
