<?php namespace Vankosoft\ApplicationBundle\EventListener\Widgets;

use Sylius\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository;
use Vankosoft\ApplicationBundle\Component\Widget\Builder\Item;
use Vankosoft\ApplicationBundle\EventListener\Event\WidgetEvent;

/**
 * MANUAL: https://github.com/cesurapp/pd-widget
 */
class ApplicationsMenuWidget extends WidgetLoader
{
    /** @var EntityRepository */
    private $applicationsRepository;
    
    public function __construct( EntityRepository $widgetsRepository, EntityRepository $applicationsRepository )
    {
        parent::__construct( $widgetsRepository );
        
        $this->applicationsRepository   = $applicationsRepository;
    }
    
    public function builder( WidgetEvent $event )
    {
        /** @var Item */
        $widgetItem = $this->createWidgetItem( 'main-menu-applications' );
        if( $widgetItem ) {
            $widgetItem->setTemplate( '@VSApplication/Widgets/applications_menu.html.twig', [
                'applications'  => $this->applicationsRepository->findAll(),
            ]);
            
            // Add Widgets
            $event->getWidgetContainer()->addWidget( $widgetItem );
        }
    }
}