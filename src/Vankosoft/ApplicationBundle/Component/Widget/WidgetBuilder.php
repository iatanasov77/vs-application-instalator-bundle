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
    
    /**
     * User Widget Configuration.
     * 
     * @var array
     */
    private $widgetConfig = [];

    public function __construct(
        TokenStorageInterface $tokenStorage,
        EntityRepository $widgetRepository
    ) {
        $this->tokenStorage     = $tokenStorage;
        $this->widgetRepository = $widgetRepository;
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

        // Custom Items
        if ( $widgetId ) {
            foreach ( $widgetId as $id ) {
                if ( isset( $widgets[$id] ) ) {
                    // Activate
                    $widgets[$id]->setActive( $this->widgetConfig[$id]['status'] ?? false );

                    // Set Widget Config
                    $widgets[$id]->setConfig( $this->widgetConfig[$id] ?? [] );

                    $outputWidget[] = $widgets[$id];
                }
            }

            return $outputWidget;
        }

        foreach ( $widgets as $widget ) {
            // Activate
            $widget->setActive( $this->widgetConfig[$widget->getId()]['status'] ?? false );

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
            $config = $this->widgetRepository->findOneBy([
                'owner' => $this->tokenStorage->getToken()->getUser(),
            ]);

            if ( null !== $config ) {
                $this->widgetConfig = $config->getConfig();
            }
        }
    }
}