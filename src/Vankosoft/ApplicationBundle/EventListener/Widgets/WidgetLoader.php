<?php namespace Vankosoft\ApplicationBundle\EventListener\Widgets;

use Sylius\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository;
use Vankosoft\ApplicationBundle\EventListener\Event\WidgetEvent;
use Vankosoft\ApplicationBundle\Component\Widget\Builder\ItemInterface;
use Vankosoft\ApplicationBundle\Component\Widget\Builder\Item;

abstract class WidgetLoader
{
    /** @var EntityRepository */
    protected $widgetsRepository;
    
    public function __construct( EntityRepository $widgetsRepository )
    {
        $this->widgetsRepository    = $widgetsRepository;
    }
    
    abstract public function builder( WidgetEvent $event );
    
    protected function createWidgetItem( string $widgetCode ): ?ItemInterface
    {
        $widget     = $this->widgetsRepository->findOneBy( ['code' => $widgetCode] );
        
        if ( $widget ) {
            // Create Widget Item
            $widgetItem = new Item( $widget->getCode(), 3600 );
            $widgetItem->setGroup( $widget->getGroup()->getCode() )
                        ->setName( $widget->getName() )
                        ->setDescription( $widget->getDescription() )
                        ->setActive( $widget->getActive() );
                        
            return $widgetItem;
        }
        
        return null;
    }
}