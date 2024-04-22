<?php namespace Vankosoft\ApplicationBundle\Component\Settings;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\PropertyAccess\PropertyAccessor;

use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Symfony\Component\Cache\Adapter\PhpArrayAdapter;

use Vankosoft\ApplicationBundle\Component\Exception\SettingsException;

class Settings
{
    /** @var ContainerInterface $container */
    private $container;
    
    /** @var PhpArrayAdapter $cache */
    private $cache;
    
    /** @var PropertyAccessor $propertyAccessor */
    private $propertyAccessor;
    
    /** @var array $settingsKeys */
    private $settingsKeys;
    
    public function __construct( ContainerInterface $container )
    {
        $this->container        = $container;
        
        $this->propertyAccessor = PropertyAccess::createPropertyAccessor();
        $this->settingsKeys     = ['maintenanceMode', 'maintenancePage', 'theme'];
        
        // https://symfony.com/doc/current/components/cache/adapters/php_array_cache_adapter.html
        //==========================================================================================
        // This adapter requires turning on the opcache.enable php.ini setting.
        /////////////////////////////////////////////////////////////////////////////////////////////
        $cacheDir              = isset( $_ENV['DIR_VAR'] ) ? 
                                    $_ENV['DIR_VAR'] . '/cache' : 
                                    $this->container->getParameter( 'kernel.cache_dir' );
        $this->cache            = new PhpArrayAdapter(
            // single file where values are cached
            $cacheDir . '/vankosoft_settings.cache',
            // a backup adapter, if you set values after warmup
            new FilesystemAdapter()
        );
    }
    
    public function getSettings( $applicationId )
    {
        $cacheId    = $applicationId ? "settings_application_{$applicationId}" : 'settings_general';
        
        $settingsCache  = $this->cache->getItem( $cacheId );
        if ( ! $settingsCache->isHit() ) {
            $settings   = $applicationId ? $this->generalizeSettings( $applicationId ) : $this->generalSettings();
            
            $this->cache->warmUp( [$cacheId => json_encode( $settings )] );
        } else {
            $settings   = json_decode( $settingsCache->get(), true );
        }
        
        return $settings;
    }
    
    public function saveSettings( $applicationId )
    {
        $allSettings    = [];
        
        // Applications Settings
        $applications  = $this->getApplicationRepository()->findAll();
        foreach ( $applications as $app ) {
            $settings   = ( $applicationId == $app->getId() ) ? $this->generalizeSettings( $applicationId ) : $this->getSettings( $app->getId() );
            $allSettings["settings_application_{$app->getId()}"]  = json_encode( $settings );
        }
        
        // General Settings
        $settings   = ( $applicationId == null ) ? $this->generalSettings() : $this->getSettings( null );
        $allSettings['settings_general']    = json_encode( $settings );
        
        $this->cache->warmUp( $allSettings );
    }
    
    public function clearCache( $applicationId, $all = false )
    {
        if ( $all ) {
            $applications  = $this->getApplicationRepository()->findAll();
            foreach ( $applications as $app ) {
                $this->cache->deleteItem( "settings_application_{$app->getId()}" );
            }
            
            $this->cache->deleteItem( 'settings_general' );
        } else {
            $cacheId    = $applicationId ? "settings_application_{$applicationId}" : 'settings_general';
            
            $this->cache->deleteItem( $cacheId );
        }
    }
    
    public function forceMaintenanceMode( bool $maintenanceMode )
    {
        $allSettings    = [];
        
        // Applications Settings
        $applications  = $this->getApplicationRepository()->findAll();
        foreach ( $applications as $app ) {
            $settings                                       = $this->getSettings( $app->getId() );
            $settings['maintenanceMode']                    = $maintenanceMode;
            
            $allSettings["settings_application_{$app->getId()}"]  = json_encode( $settings );
        }
        
        // General Settings
        $settings                           = $this->getSettings( null );
        $settings['maintenanceMode']        = $maintenanceMode;
        $allSettings['settings_general']    = json_encode( $settings );
        
        $this->cache->warmUp( $allSettings );
    }
    
    // Used For Dump/Debug
    public function getAllSettings()
    {
        $applications      = $this->getApplicationRepository()->findAll();
        $settings   = [];
        foreach ( $applications as $app ) {
            $settings["settings_application_{$app->getId()}"]   = $this->getSettings( $app->getId() );
        }
        $settings['settings_general']   = $this->getSettings( null );
        
        return $settings;
    }
    
    private function generalizeSettings( $applicationId ) : array
    {
        $application   = $this->getApplicationRepository()->find( $applicationId );
        if ( ! $application ) {
            throw new SettingsException( "Application With ID:{$applicationId} Not Exists!" );
        }
        
        $generalSettings        = $this->getSettingsRepository()->getSettings();
        $applicationSettings    = $this->getSettingsRepository()->getSettings( $application );
        //var_dump( $generalSettings ); die;
        
        $generalizedSettings    = [];
        foreach( $this->settingsKeys as $key ) {
            $value  = $applicationSettings ? $this->propertyAccessor->getValue( $applicationSettings, $key ) : null;
            if ( $value === null ) {
                $value  = $this->propertyAccessor->getValue( $generalSettings, $key );
            }
            
            $generalizedSettings[$key]  = is_object( $value ) ? $value->getId() : $value;
        }
  
        return $generalizedSettings;
    }
    
    private function generalSettings() : array
    {
        $generalSettings    = $this->getSettingsRepository()->getSettings();
        //var_dump( $generalSettings ); die;
        
        $settings    = [];
        foreach( $this->settingsKeys as $key ) {
            $value          = $this->propertyAccessor->getValue( $generalSettings, $key );
            $settings[$key] = is_object( $value ) ? $value->getId() : $value;
        }
        
        return $settings;
    }
    
    private function getApplicationRepository()
    {
        return $this->container->get( 'vs_application.repository.application' );
    }
    
    private function getSettingsRepository()
    {
        return $this->container->get( 'vs_application.repository.settings' );
    }
}
