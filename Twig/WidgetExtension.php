<?php namespace Vankosoft\ApplicationBundle\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

use Vankosoft\ApplicationBundle\Component\Widget\Builder\ItemInterface;
use Vankosoft\ApplicationBundle\Twig\Renderer\RendererInterface;
use Vankosoft\ApplicationBundle\Component\Widget\WidgetBuilderInterface;
use Vankosoft\ApplicationBundle\Component\Widget\WidgetInterface;

class WidgetExtension extends AbstractExtension
{
    /** @var RendererInterface */
    private $engine;
    
    /** @var WidgetBuilderInterface */
    private $widgetBuilder;
    
    /** @var WidgetInterface */
    private $widgets;
    
    public function __construct(
        RendererInterface $engine,
        WidgetBuilderInterface $widgetBuilder,
        WidgetInterface $widgets
    ) {
        $this->engine           = $engine;
        $this->widgetBuilder    = $widgetBuilder;
        $this->widgets          = $widgets;
    }

    /**
     * Twig Functions.
     */
    public function getFunctions(): array
    {
        return [
            new TwigFunction( 'vs_widget_render', [$this, 'renderWidget'], ['is_safe' => ['html']] ),
            new TwigFunction( 'vs_widget_get', [$this, 'getWidget'] ),
        ];
    }

    /**
     * Render Widget Group|Item.
     */
    public function renderWidget( string $widgetGroup = '', array $widgetId = [] ): string
    {
        return $this->engine->render( $this->widgetBuilder->build( $this->widgets->getWidgets(), $widgetGroup, $widgetId, true ) );
    }

    /**
     * Get Widgets.
     *
     * @return ItemInterface[]
     */
    public function getWidget( string $widgetGroup = '', array $widgetId = [], array $widgetParams = [] ): array
    {
        return $this->widgetBuilder->build( $this->widgets->getWidgets(), $widgetGroup, $widgetId, false );
    }
}
