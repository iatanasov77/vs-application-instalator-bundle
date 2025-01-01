<?php namespace Vankosoft\ApplicationBundle\Component\Widget;

use Vankosoft\ApplicationBundle\Component\Widget\Builder\ItemInterface;

interface WidgetBuilderInterface
{
    /**
     * Build Widgets.
     *
     * @return ItemInterface[]|null
     */
    public function build( array $widgets, string $widgetGroup = '', array $widgetId = [],  bool $render = false ): ?array;
}
