<?php namespace Vankosoft\ApplicationBundle\Component\Widget;

use Vankosoft\ApplicationBundle\Component\Widget\Builder\ItemInterface;
use Vankosoft\UsersBundle\Model\Interfaces\UserInterface;

interface WidgetInterface
{
    public function createWidgetItem( string $widgetCode, bool $checkRole = true ): ?ItemInterface;
    
    public function loadWidgets( ?UserInterface $user, bool $checkRole = true, bool $all = false );
    
    /**
     * Get Items to Widget Storage.
     *
     * @return ItemInterface[]|null
     */
    public function getWidgets( bool $checkRole = true ): ?array;
    
    /**
     * Get Items to Widget Storage.
     *
     * @return ItemInterface[]|null
     */
    public function getAllWidgets( bool $checkRole = true ): ?array;

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
