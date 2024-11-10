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
use Vankosoft\ApplicationBundle\Model\Interfaces\WidgetInterface as WidgetModelInterface;
use Vankosoft\UsersBundle\Model\Interfaces\UserInterface;
use Vankosoft\UsersBundle\Model\UserRole;

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
        $allowed            = true;
        
        if ( $widget ) {
            if ( $checkRole ) {
                $allowed    = $this->enableWidgetForUser( $widget );
            }
            
            if ( $allowed ) {
                // Create Widget Item
                $widgetItem = new Item( $widget->getCode(), 3600 );
                $widgetItem->setGroup( $widget->getGroup()->getCode() )
                            ->setName( $widget->getName() )
                            ->setDescription( $widget->getDescription() )
                            ->setActive( $widget->getActive() )
                            ->setRole( $widget->getAllowedRolesFromCollection() )
                            ->setAllowAnonymous( $widget->getAllowAnonymous() )
                ;
                
                return $widgetItem;
            }
        }
        
        return null;
    }
    
    /**
     * Used to Load Widgets into Database
     */
    public function loadWidgets( ?UserInterface $user, bool $checkRole = true, bool $all = false )
    {
        // Build Widgets
        $widgets = $all ? $this->getAllWidgets( $checkRole ) : $this->getWidgets( $checkRole );
        
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
    public function getWidgets( bool $checkRole = true ): ?array
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
     * Get All Widgets Including New Widgets.
     *
     * @return ItemInterface[]|null
     */
    public function getAllWidgets( bool $checkRole = true ): ?array
    {
        $allWidgets = $this->widgetRepository->findAll();
        
        $widgets    = [];
        foreach ( $allWidgets as $w ) {
            $widgetItem = $this->createWidgetItem( $w->getCode(), $checkRole );
            if ( $widgetItem ) {
                $widgets[$w->getCode()] = $widgetItem;
            }
        }
            
        return $widgets;
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
        $userId     = $this->token->getToken() ? $this->token->getToken()->getUser()->getId() : '';

        // Clear Cache
        foreach ( $widgets as $widget ) {
            try {
                $this->cache->deleteItem( $widget->getId() . $userId );
            } catch ( InvalidArgumentException $e ) {
            }
        }
    }
    
    private function enableWidgetForUser( WidgetModelInterface $widget ): bool
    {
        $user           = $this->token->getToken() ? $this->token->getToken()->getUser() : null;
        $userRoles      = $user ? $user->getRoles() : [UserRole::ANONYMOUS];
        
        $allowedRoles   = \array_intersect( $userRoles, $widget->getAllowedRolesFromCollection() );
        
        return ! empty( $allowedRoles );
    }
}
