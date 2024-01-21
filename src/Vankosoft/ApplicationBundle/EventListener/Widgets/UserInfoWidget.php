<?php namespace Vankosoft\ApplicationBundle\EventListener\Widgets;

use Vankosoft\ApplicationBundle\Component\Widget\Builder\Item;
use Vankosoft\ApplicationBundle\EventListener\Event\WidgetEvent;

/**
 * MANUAL: https://github.com/cesurapp/pd-widget
 */
class UserInfoWidget extends WidgetLoader
{
    public function builder( WidgetEvent $event )
    {
        /** @var Item */
        $widgetItem = $this->createWidgetItem( 'user-info' );
        if( $widgetItem ) {
            $widgetItem->setTemplate( '@VSApplication/Widgets/user_info.html.twig' )
            //->setContent( 'pdWidget Text Content' )
            //->setRole( ['USER_INFO_WIDGET'] )
            
            ->setData( function () {
                return ['userCount' => 5];
            })
            ->setOrder( 5 );
            
            // Add Widgets
            $event->getWidgetContainer()->addWidget( $widgetItem );
        }
    }
}