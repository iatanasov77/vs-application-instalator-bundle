<?php namespace Vankosoft\ApplicationBundle\EventListener\Widgets;

use Vankosoft\ApplicationBundle\Component\Widget\Widget;
use Vankosoft\ApplicationBundle\Component\Widget\Builder\Item;
use Vankosoft\ApplicationBundle\EventListener\Event\WidgetEvent;

/**
 * MANUAL: https://github.com/cesurapp/pd-widget
 */
class UserInfoWidget implements WidgetLoaderInterface
{
    public function builder( WidgetEvent $event )
    {
        /** @var Widget */
        $widgetContainer    = $event->getWidgetContainer();
        
        /** @var Item */
        $widgetItem = $widgetContainer->createWidgetItem( 'user-info' );
        if( $widgetItem ) {
            $widgetItem->setTemplate( '@VSApplication/Widgets/user_info.html.twig' )
                //->setContent( 'pdWidget Text Content' )
                ->setData( function () {
                    return ['userCount' => 5];
                })
                ->setOrder( 5 )
            ;
            
            // Add Widgets
            $widgetContainer->addWidget( $widgetItem );
        }
    }
}