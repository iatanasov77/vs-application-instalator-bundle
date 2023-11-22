<?php namespace Vankosoft\ApplicationBundle\Component\Widget;

use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;
use Psr\Cache\CacheItemPoolInterface;
use Psr\Cache\InvalidArgumentException;

use Vankosoft\ApplicationBundle\Component\Widget\Builder\ItemInterface;
use Vankosoft\ApplicationBundle\EventListener\Event\WidgetEvent;

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
        TokenStorageInterface $token
    ) {
        $this->security         = $security;
        $this->eventDispatcher  = $eventDispatcher;
        $this->cache            = $cache;
        $this->token            = $token;
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
