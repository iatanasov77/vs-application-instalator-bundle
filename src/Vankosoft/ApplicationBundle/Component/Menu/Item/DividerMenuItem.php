<?php namespace Vankosoft\ApplicationBundle\Component\Menu\Item;

use Knp\Menu\FactoryInterface;
use Knp\Menu\MenuItem;

final class DividerMenuItem extends MenuItem
{
    /**
     * @param FactoryInterface $factory
     */
    public function __construct( string $id, FactoryInterface $factory )
    {
        parent::__construct( $id, $factory );
        
        $this
            ->setExtra( 'divider', true )
            ->setAttribute( 'class', 'separator' )
        ;
    }
}