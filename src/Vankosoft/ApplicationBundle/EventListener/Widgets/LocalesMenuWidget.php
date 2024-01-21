<?php namespace Vankosoft\ApplicationBundle\EventListener\Widgets;

use Sylius\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository;
use Vankosoft\ApplicationBundle\Component\Widget\Builder\Item;
use Vankosoft\ApplicationBundle\EventListener\Event\WidgetEvent;

/**
 * MANUAL: https://github.com/cesurapp/pd-widget
 */
class LocalesMenuWidget extends WidgetLoader
{
    /** @var EntityRepository */
    private $localesRepository;
    
    public function __construct( EntityRepository $widgetsRepository, EntityRepository $localesRepository )
    {
        parent::__construct( $widgetsRepository );
        
        $this->localesRepository    = $localesRepository;
    }
    
    public function builder( WidgetEvent $event )
    {
        /** @var Item */
        $widgetItem = $this->createWidgetItem( 'profile-menu-locales' );
        if( $widgetItem ) {
            $widgetItem->setTemplate( '@VSApplication/Widgets/locales_menu.html.twig', [
                'locales'   => $this->localesRepository->findAll(),
            ]);
            
            // Add Widgets
            $event->getWidgetContainer()->addWidget( $widgetItem );
        }
    }
}