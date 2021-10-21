<?php namespace VS\ApplicationBundle\EventListener;

//use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\Response;

use VS\ApplicationBundle\Twig\Alerts;

class MaintenanceListener
{
    protected $container;
    protected $user;
    protected $applicationId;
    protected $applicationLayout;
    
    public function __construct(
        ContainerInterface $container,
        int $applicationId = null,
        ?string $applicationLayout
    ) {
        $this->container            = $container;
        $this->applicationId        = $applicationId;
        $this->applicationLayout    = $applicationLayout;
        
        $token              = $this->container->get( 'security.token_storage' )->getToken();
        if ( $token ) {
            $this->user         = $token->getUser();
        }
    }
    
    //public function onKernelRequest( GetResponseEvent $event )
    public function onKernelRequest( RequestEvent $event )
    {
        $debug              = in_array( $this->container->get('kernel')->getEnvironment(), ['dev'] );
        $settings           = $this->getSettingsManager()->getSettings( $this->applicationId );
        
        // If maintenance is active and in prod or test  environment and user is not admin
        if ( $settings['maintenanceMode'] ) {
            if (
                ( ! is_object( $this->user ) || ! $this->user->hasRole( 'ROLE_ADMIN' ) )
                && ! $debug
                ) {
                    $maintenancePage    = $settings['maintenancePage'] ?
                                            $this->getPagesRepository()->find( $settings['maintenancePage'] ) :
                                            null;
                    if ( $maintenancePage ) {
                        $event->setResponse( new Response( $this->renderMaintenancePage( $maintenancePage ), 503 ) );
                    } else {
                        $event->setResponse( new Response( 'The System is in Maintenance Mode !', 503 ) );
                    }
                    
                    $event->stopPropagation();
                } else {
                    Alerts::$WARNINGS[]   = 'The System is in Maintenance Mode !';
                }
        }
    }
    
    protected function getSettingsManager()
    {
        return $this->container->get( 'vs_app.settings_manager' );
    }
    
    protected function getPagesRepository()
    {
        return $this->container->get( 'vs_cms.repository.pages' );
    }
    
    private function renderMaintenancePage( $maintenancePage ): string
    {
        return $this->container->get( 'templating' )->render( '@VSCms/Pages/show.html.twig',
            [
                'page'              => $maintenancePage,
                'applicationLayout' => $this->applicationLayout ?: '@VSApplication/layout.html.twig',
                'inMainenance'      => true,
            ]
        );
    }
}
