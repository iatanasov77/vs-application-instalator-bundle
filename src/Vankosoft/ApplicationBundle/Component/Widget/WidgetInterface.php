<?php namespace Vankosoft\ApplicationBundle\Component\Widget;

use Vankosoft\ApplicationBundle\Component\Widget\Builder\ItemInterface;

interface WidgetInterface
{
    /**
     * Get Items to Widget Storage.
     *
     * @return ItemInterface[]|null
     */
    public function getWidgets( $checkRole = true ): ?array;

    /**
     * Add Item to Widget Storage.
     */
    public function addWidget( ItemInterface $item ): self;

    /**
     * Remove Item to Widget Storage.
     */
    public function removeWidget( string $widgetId ): self;

    /**
     * Clear Current User Widget Cache.
     */
    public function clearWidgetCache(): void;
}
