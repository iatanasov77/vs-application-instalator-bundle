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
    
    /** @var EntityRepository */
    private $installationInfoRepository;
    
    public function __construct( EntityRepository $applicationsRepository, EntityRepository $installationInfoRepository )
    {
        $this->applicationsRepository       = $applicationsRepository;
        $this->installationInfoRepository   = $installationInfoRepository;
    }
    
    public function builder( WidgetEvent $event )
    {
        /** @var Widget */
        $widgetContainer    = $event->getWidgetContainer();
        
        /** @var Item */
        $widgetItem = $widgetContainer->createWidgetItem( 'main-menu-applications' );
        if( $widgetItem ) {
            $installationInfo   = $this->installationInfoRepository->findOneBy( [], ['id' => 'DESC'] );
            
            $widgetItem->setTemplate( '@VSApplication/Widgets/applications_menu.html.twig', [
                'applications'      => $this->applicationsRepository->findAll(),
                'installationInfo'  => $installationInfo,
            ]);
            
            // Add Widgets
            $widgetContainer->addWidget( $widgetItem );
        }
    }
}