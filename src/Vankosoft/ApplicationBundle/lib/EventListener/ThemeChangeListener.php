<?php namespace VS\ApplicationBundle\EventListener;

use Symfony\Component\HttpKernel\Event\RequestEvent;
use Sylius\Bundle\ThemeBundle\Context\ThemeContextInterface;
use Sylius\Bundle\ThemeBundle\Repository\ThemeRepositoryInterface;

use VS\ApplicationBundle\Repository\SettingsRepositoryInterface;

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
        int $applicationId = null
    ) {
        $this->themeContext         = $themeContext;
        $this->themeRepository      = $themeRepository;
        $this->settingsRepository   = $settingsRepository;
        $this->applicationId        = $applicationId;
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
