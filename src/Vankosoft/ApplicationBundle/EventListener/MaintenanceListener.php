<?php namespace Vankosoft\ApplicationBundle\EventListener;

//use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\Event\RequestEvent;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\Response;
use Twig\Environment;

use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\HttpFoundation\Session\Session;

use Vankosoft\ApplicationBundle\Component\Context\ApplicationContextInterface;
use Vankosoft\ApplicationBundle\Twig\Alerts;
use Vankosoft\UsersBundle\Model\Interfaces\UserInterface;

final class MaintenanceListener
{
    /** @var ContainerInterface */
    private $container;
    
    /** @var Environment $twig */
    private $twig;
    
    /** @var RequestStack */
    private $requestStack;
    
    /** @var UserInterface */
    private $user;
    
    /** @var ApplicationContextInterface */
    private $applicationContext;
    
    public function __construct(
        ContainerInterface $container,
        Environment $twig,
        RequestStack $requestStack,
        TokenStorageInterface $tokenStorage,
        ApplicationContextInterface $applicationContext
    ) {
        $this->container            = $container;
        $this->twig                 = $twig;
        $this->requestStack         = $requestStack;
        $this->applicationContext   = $applicationContext;
        
        if ( $this->requestStack->getMainRequest() ) {
            $token  = $tokenStorage->getToken();
            if ( $token ) {
                $this->user = $token->getUser();
            }
        }
    }
    
    //public function onKernelRequest( GetResponseEvent $event )
    public function onKernelRequest( RequestEvent $event ): void
    {
        $request    = $event->getRequest();
        
//         $env        = $this->container->get( 'kernel' )->getEnvironment();
//         $debug      = \in_array( $env, ['dev'] );
        $debug      = $this->container->get( 'kernel' )->isDebug();
        
        $application    = $this->applicationContext->getApplication();
        $appSettings    = $application->getSettings();
        if ( $appSettings->isEmpty() ) {
            return;
        }
        $appSettings    = $appSettings[0];
        //echo '<pre>'; var_dump( $appSettings ); die;
        
        $settings   = $this->getSettingsManager()->getSettings( $application->getId() );
        //echo '<pre>'; var_dump( $settings ); die;
        
        // If maintenance is active and in prod or test  environment and user is not admin
        if ( isset( $settings['maintenanceMode'] ) && $settings['maintenanceMode'] ) {
        //if ( $appSettings->getMaintenanceMode() ) {
            if (
                ( ! is_object( $this->user ) || ! $this->user->hasRole( 'ROLE_ADMIN' ) )
                && ! $debug
            ) {
                $maintenancePage    = $this->getPagesRepository()->find( $settings['maintenancePage'] );
                //$maintenancePage    = $appSettings->getMaintenancePage();
                
                if ( $maintenancePage ) {
                    $event->setResponse( new Response( $this->renderMaintenancePage( $maintenancePage ), 503 ) );
                } else {
                    $event->setResponse( new Response( 'The System is in Maintenance Mode !', 503 ) );
                }
                
                $event->stopPropagation();
            } else {
                $flash  = $request->getSession()->getFlashBag();
                
                // Alerts::WARNINGS[]   = 'The System is in Maintenance Mode !';
                if ( ! $flash->has( 'in-maintenance' ) ) { // Check if there is no Flash messages of type "in-maintenance"
                    $flash->add( 'in-maintenance', 'The System is in Maintenance Mode !' );
                }
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
        //return $this->twig->render( '@VSCms/Pages/Pages/show.html.twig',
        return $this->twig->render( '@VSApplication/MaintenancePages/cms_page.html.twig',
            [
                'page'              => $maintenancePage,
                'applicationLayout' => '@VSApplication/layout.html.twig',
                'siteLayout'        => '@VSApplication/layout.html.twig',
                'inMainenance'      => true,
            ]
        );
    }
}
