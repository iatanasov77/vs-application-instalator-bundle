<?php namespace Vankosoft\ApplicationBundle\EventListener\Widgets;

use Vankosoft\ApplicationBundle\Repository\Interfaces\ApplicationRepositoryInterface;
use Vankosoft\ApplicationInstalatorBundle\Repository\InstalationInfoRepositoryInterface;
use Vankosoft\ApplicationBundle\Component\Widget\Widget;
use Vankosoft\ApplicationBundle\Component\Widget\Builder\Item;
use Vankosoft\ApplicationBundle\EventListener\Event\WidgetEvent;

/**
 * MANUAL: https://github.com/cesurapp/pd-widget
 */
class ApplicationsMenuWidget implements WidgetLoaderInterface
{
    /** @var ApplicationRepositoryInterface */
    private $applicationsRepository;
    
    /** @var InstalationInfoRepositoryInterface */
    private $installationInfoRepository;
    
    public function __construct(
        ApplicationRepositoryInterface $applicationsRepository,
        InstalationInfoRepositoryInterface $installationInfoRepository
    ) {
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
            $installationInfo   = $this->installationInfoRepository->getLatestInstallation();
            
            $widgetItem->setTemplate( '@VSApplication/Widgets/applications_menu.html.twig', [
                'applications'      => $this->applicationsRepository->findAll(),
                'installationInfo'  => $installationInfo,
            ]);
            
            // Add Widgets
            $widgetContainer->addWidget( $widgetItem );
        }
    }
}