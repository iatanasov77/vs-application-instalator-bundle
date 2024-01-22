<?php namespace Vankosoft\ApplicationBundle\EventListener\Widgets;

use Sylius\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository;
use Vankosoft\ApplicationBundle\Component\Widget\Widget;
use Vankosoft\ApplicationBundle\Component\Widget\Builder\Item;
use Vankosoft\ApplicationBundle\EventListener\Event\WidgetEvent;

/**
 * MANUAL: https://github.com/cesurapp/pd-widget
 */
class ApplicationsMenuWidget implements WidgetLoaderInterface
{
    /** @var EntityRepository */
    private $applicationsRepository;
    
    public function __construct( EntityRepository $applicationsRepository )
    {
        $this->applicationsRepository   = $applicationsRepository;
    }
    
    public function builder( WidgetEvent $event )
    {
        /** @var Widget */
        $widgetContainer    = $event->getWidgetContainer();
        
        /** @var Item */
        $widgetItem = $widgetContainer->createWidgetItem( 'main-menu-applications' );
        if( $widgetItem ) {
            $widgetItem->setTemplate( '@VSApplication/Widgets/applications_menu.html.twig', [
                'applications'  => $this->applicationsRepository->findAll(),
            ]);
            
            // Add Widgets
            $widgetContainer->addWidget( $widgetItem );
        }
    }
}