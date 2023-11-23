<?php namespace Vankosoft\ApplicationBundle\EventListener\Widgets;

use Sylius\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository;
use Vankosoft\ApplicationBundle\Component\Widget\Builder\Item;
use Vankosoft\ApplicationBundle\EventListener\Event\WidgetEvent;

/**
 * MANUAL: https://github.com/cesurapp/pd-widget
 */
class LocalesMenuWidget
{
    /** @var EntityRepository */
    private $localesRepository;
    
    public function __construct( EntityRepository $localesRepository )
    {
        $this->localesRepository    = $localesRepository;
    }
    
    public function builder( WidgetEvent $event )
    {
        // Get Widget Container
        $widgets    = $event->getWidgetContainer();
        
        // Create Widget Item
        $widgetItem = new Item( 'profile_menu_locales', 3600 );
        $widgetItem->setGroup( 'admin_profile_menu' )
                    ->setName( 'widget_locales_menu.name' )
                    ->setDescription( 'widget_locales_menu.description' )
                    ->setActive( true )
                    ->setTemplate( '@VSApplication/Widgets/locales_menu.html.twig', [
                        'locales'   => $this->localesRepository->findAll(),
                    ]);
                        
        // Add Widgets
        $widgets->addWidget( $widgetItem );
    }
}