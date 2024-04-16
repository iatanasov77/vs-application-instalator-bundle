<?php namespace Vankosoft\ApplicationBundle\Component\Widget;

use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Sylius\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository;
use Vankosoft\ApplicationBundle\Component\Widget\Builder\ItemInterface;

class WidgetBuilder implements WidgetBuilderInterface
{
    /** @var TokenStorageInterface */
    private $tokenStorage;
    
    /** @var EntityRepository */
    private $widgetRepository;
    
    /** @var EntityRepository */
    private $widgetConfigRepository;
    
    /**
     * User Widget Configuration.
     * 
     * @var array
     */
    private $widgetConfig = [];

    public function __construct(
        TokenStorageInterface $tokenStorage,
        EntityRepository $widgetRepository,
        EntityRepository $widgetConfigRepository
    ) {
        $this->tokenStorage             = $tokenStorage;
        $this->widgetRepository         = $widgetRepository;
        $this->widgetConfigRepository   = $widgetConfigRepository;
    }

    public function buildUserConfig( ?array $users = null ): ?array
    {
        $widgets    = $this->widgetRepository->findAll();
        foreach ( $widgets as $widget ) {
            
        }
    }
    
    /**
     * Build Widgets.
     *
     * @param $widgets ItemInterface[]
     * @param string $widgetGroup
     * @param array $widgetId
     * @param bool $render
     *
     * @return ItemInterface[]
     */
    public function build( array $widgets, string $widgetGroup = '', array $widgetId = [], bool $render = false ): ?array
    {
        // Without Widgets
        if ( ! $widgets) {
            return $widgets;
        }

        // Load User Widget Configuration
        $this->loadUserConfig();

        // Output Widgets
        $outputWidget = [];

        // getAllowAnonymous()
        
        // Custom Items
        if ( $widgetId ) {
            foreach ( $widgetId as $id ) {
                if ( isset( $widgets[$id] ) ) {
                    // Activate
                    $widgets[$id]->setActive( $this->widgetConfig[$id]['status'] ?? $widgets[$id]->getAllowAnonymous() );

                    // Set Widget Config
                    $widgets[$id]->setConfig( $this->widgetConfig[$id] ?? [] );

                    $outputWidget[] = $widgets[$id];
                }
            }

            return $outputWidget;
        }

        foreach ( $widgets as $widget ) {
            // Activate
            $widget->setActive( $this->widgetConfig[$widget->getId()]['status'] ?? $widget->getAllowAnonymous() );

            // Set Widget Config
            $widget->setConfig( $this->widgetConfig[$widget->getId()] ?? [] );

            // Enable
            if ( ( '' !== $widgetGroup && $widget->getGroup() !== $widgetGroup ) || ( $render && ! $widget->isActive() ) ) {
                continue;
            }

            // Set Custom Order
            if ( isset( $this->widgetConfig[$widget->getId()]['order'] ) ) {
                $widget->setOrder( $this->widgetConfig[$widget->getId()]['order'] );
            }

            // Order
            if ( null !== $widget->getOrder() ) {
                while ( isset( $outputWidget[$widget->getOrder()] ) ) {
                    $widget->setOrder( $widget->getOrder() + 1 );
                }

                $outputWidget[$widget->getOrder()] = $widget;
            } else {
                $outputWidget[] = $widget;
            }
        }

        // Sort
        ksort( $outputWidget );

        return $outputWidget;
    }

    /**
     * Load User Widget Configuration.
     */
    private function loadUserConfig(): void
    {
        if ( ! $this->widgetConfig ) {
            $currentUser    = $this->tokenStorage->getToken() ? $this->tokenStorage->getToken()->getUser() : null;
            
            $config         = $this->widgetConfigRepository->findOneBy([
                'owner' => $currentUser,
            ]);

            if ( null !== $config ) {
                $this->widgetConfig = $config->getConfig();
            }
        }
    }
}
