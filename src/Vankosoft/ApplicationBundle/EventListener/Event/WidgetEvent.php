<?php namespace Vankosoft\ApplicationBundle\EventListener\Event;

use Symfony\Contracts\EventDispatcher\Event;
use Vankosoft\ApplicationBundle\Component\Widget\WidgetInterface;

class WidgetEvent extends Event
{
    public const WIDGET_START = 'widget.start';

    /** @var WidgetInterface */
    private $widget;
    
    public function __construct( WidgetInterface $widget )
    {
        $this->widget   = $widget;
    }

    public function getWidgetContainer(): WidgetInterface
    {
        return $this->widget;
    }
}
