<?php namespace Vankosoft\ApplicationBundle\EventListener\Widgets;

use Sylius\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository;
use Vankosoft\ApplicationBundle\Component\Widget\Builder\Item;
use Vankosoft\ApplicationBundle\EventListener\Event\WidgetEvent;

/**
 * MANUAL: https://github.com/cesurapp/pd-widget
 */
class ApplicationsMenuWidget
{
    /** @var EntityRepository */
    private $applicationsRepository;
    
    public function __construct( EntityRepository $applicationsRepository )
    {
        $this->applicationsRepository   = $applicationsRepository;
    }
    
    public function builder( WidgetEvent $event )
    {
        // Get Widget Container
        $widgets    = $event->getWidgetContainer();
        
        // Create Widget Item
        $widgetItem = new Item( 'main_menu_applications', 3600 );
        $widgetItem->setGroup( 'admin_main_menu' )
                    ->setName( 'widget_applications_menu.name' )
                    ->setDescription( 'widget_applications_menu.description' )
                    ->setActive( true )
                    ->setTemplate( '@VSApplication/Widgets/applications_menu.html.twig', [
                        'applications'  => $this->applicationsRepository->findAll(),
                    ]);
                        
        // Add Widgets
        $widgets->addWidget( $widgetItem );
    }
}