<?php namespace Vankosoft\ApplicationBundle\EventListener;

use Symfony\Component\HttpKernel\Event\RequestEvent;
use Sylius\Bundle\ThemeBundle\Context\ThemeContextInterface;
use Sylius\Bundle\ThemeBundle\Repository\ThemeRepositoryInterface;

use Vankosoft\ApplicationBundle\Component\Context\ApplicationContextInterface;
use Vankosoft\ApplicationBundle\Repository\Interfaces\SettingsRepositoryInterface;

class ThemeChangeListener
{
    protected $themeContext;
    protected $themeRepository;
    protected $settingsRepository;
    protected $applicationId;
    
    public function __construct(
        ThemeContextInterface $themeContext,
        ThemeRepositoryInterface $themeRepository,
        SettingsRepositoryInterface $settingsRepository,
        ApplicationContextInterface $applicationContext,
        int $applicationId = null
    ) {
        $this->themeContext         = $themeContext;
        $this->themeRepository      = $themeRepository;
        $this->settingsRepository   = $settingsRepository;
        
       
        $this->applicationId        = $applicationContext->getApplication()->getId();
//         if ( $applicationId ) {
//             $this->applicationId    = $applicationId;
//         }
    }
    
    public function onKernelRequest( RequestEvent $event )
    {
        $settings   = $this->settingsRepository->getSettings( $this->applicationId );

        if( $settings && $settings->getTheme() ) {
            $theme      = $this->themeRepository->findOneByName( $settings->getTheme() );
            //$theme      = $this->themeRepository->findOneByName( 'vankosoft/test-theme' );
            if ( $theme ) {
                $this->themeContext->setTheme( $theme );
            }
        }
    }
}
