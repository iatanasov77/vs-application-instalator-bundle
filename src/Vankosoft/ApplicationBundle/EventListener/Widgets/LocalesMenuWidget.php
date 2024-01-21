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
    private $widgetsRepository;
    
    /** @var EntityRepository */
    private $localesRepository;
    
    public function __construct( EntityRepository $widgetsRepository, EntityRepository $localesRepository )
    {
        $this->widgetsRepository    = $widgetsRepository;
        $this->localesRepository    = $localesRepository;
    }
    
    public function builder( WidgetEvent $event )
    {
        $widget     = $this->widgetsRepository->findOneBy( ['code' => 'profile-menu-locales'] );
        
        // Get Widget Container
        $widgets    = $event->getWidgetContainer();
        
        // Create Widget Item
        $widgetItem = new Item( $widget->getCode(), 3600 );
        $widgetItem->setGroup( $widget->getGroup()->getCode() )
                    ->setName( $widget->getName() )
                    ->setDescription( $widget->getDescription() )
                    ->setActive( $widget->getActive() )
                    ->setTemplate( '@VSApplication/Widgets/locales_menu.html.twig', [
                        'locales'   => $this->localesRepository->findAll(),
                    ]);
                        
        // Add Widgets
        $widgets->addWidget( $widgetItem );
    }
}