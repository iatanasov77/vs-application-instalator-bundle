<?php namespace Vankosoft\ApplicationBundle\EventListener\Widgets;

use Sylius\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository;
use Vankosoft\ApplicationBundle\Component\Widget\Widget;
use Vankosoft\ApplicationBundle\Component\Widget\Builder\Item;
use Vankosoft\ApplicationBundle\EventListener\Event\WidgetEvent;

/**
 * MANUAL: https://github.com/cesurapp/pd-widget
 */
class LocalesMenuWidget implements WidgetLoaderInterface
{
    /** @var EntityRepository */
    private $localesRepository;
    
    public function __construct( EntityRepository $localesRepository )
    {
        $this->localesRepository    = $localesRepository;
    }
    
    public function builder( WidgetEvent $event )
    {
        /** @var Widget */
        $widgetContainer    = $event->getWidgetContainer();
        
        /** @var Item */
        $widgetItem = $widgetContainer->createWidgetItem( 'profile-menu-locales' );
        if( $widgetItem ) {
            $widgetItem->setTemplate( '@VSApplication/Widgets/locales_menu.html.twig', [
                'locales'   => $this->localesRepository->findAll(),
            ]);
            
            // Add Widgets
            $widgetContainer->addWidget( $widgetItem );
        }
    }
}