<?php namespace Vankosoft\ApplicationBundle\EventListener\Widgets;

use Vankosoft\ApplicationBundle\EventListener\Event\WidgetEvent;

interface WidgetLoaderInterface
{
    public function builder( WidgetEvent $event );
}