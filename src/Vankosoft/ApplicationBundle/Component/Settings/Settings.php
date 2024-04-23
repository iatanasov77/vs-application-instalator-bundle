<?php namespace Vankosoft\ApplicationBundle\Component\Settings;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\PropertyAccess\PropertyAccessor;

use Psr\Cache\CacheItemPoolInterface;
use Psr\Cache\InvalidArgumentException;

use Vankosoft\ApplicationBundle\Component\Exception\SettingsException;

final class Settings
{
    /** @var ContainerInterface $container */
    private $container;
    
    /** @var CacheItemPoolInterface */
    private $cache;
    
    /** @var PropertyAccessor $propertyAccessor */
    private $propertyAccessor;
    
    /** @var array $settingsKeys */
    private $settingsKeys;
    
    public function __construct( ContainerInterface $container, CacheItemPoolInterface $cache )
    {
        $this->container        = $container;
        
        $this->propertyAccessor = PropertyAccess::createPropertyAccessor();
        $this->settingsKeys     = ['maintenanceMode', 'maintenancePage', 'theme'];
        
        $this->cache            = $cache;
    }
    
    public function getSettings( $applicationId )
    {
        $cacheId    = $applicationId ? "settings_application_{$applicationId}" : 'settings_general';
        
        $settingsCache  = $this->cache->getItem( $cacheId );
        if ( ! $settingsCache->isHit() ) {
            $settings   = $applicationId ? $this->generalizeSettings( $applicationId ) : $this->generalSettings();
            
            $settingsCache->set( \json_encode( $settings ) );
            $this->cache->save( $settingsCache );
        } else {
            $settings   = json_decode( $settingsCache->get(), true );
        }
        
        return $settings;
    }
    
    public function saveSettings( $applicationId )
    {
        $cacheId    = $applicationId ? "settings_application_{$applicationId}" : 'settings_general';
        
        $settingsCache  = $this->cache->getItem( $cacheId );
        $allSettings    = [];
        
        // Applications Settings
        $applications  = $this->getApplicationRepository()->findAll();
        foreach ( $applications as $app ) {
            $settings   = $applicationId == $app->getId() || $applicationId == 0 ?
                            $this->generalizeSettings( $app->getId() ) : 
                            $this->getSettings( $app->getId() );
            
            $appCacheId = "settings_application_{$app->getId()}";
            $allSettings[$appCacheId]  = json_encode( $settings );
            
            $appSettings    = $this->cache->getItem( $appCacheId );
            $appSettings->set( $allSettings[$appCacheId] );
            $this->cache->save( $appSettings );
        }
        
        // General Settings
        $settings   = ( $applicationId == null ) ? $this->generalSettings() : $this->getSettings( null );
        $allSettings['settings_general']    = json_encode( $settings );
        
        $settingsCache->set( \json_encode( $allSettings ) );
        $this->cache->save( $settingsCache );
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
        $settingsCache  = $this->cache->getItem( 'settings_general' );
        $allSettings    = [];
        
        // Applications Settings
        $applications  = $this->getApplicationRepository()->findAll();
        foreach ( $applications as $app ) {
            $settings                                       = $this->getSettings( $app->getId() );
            $settings['maintenanceMode']                    = $maintenanceMode;
            
            $appCacheId = "settings_application_{$app->getId()}";
            $allSettings[$appCacheId]  = json_encode( $settings );
            
            $appSettings    = $this->cache->getItem( $appCacheId );
            $appSettings->set( $allSettings[$appCacheId] );
            $this->cache->save( $appSettings );
        }
        
        // General Settings
        $settings                           = $this->getSettings( null );
        $settings['maintenanceMode']        = $maintenanceMode;
        $allSettings['settings_general']    = json_encode( $settings );
        
        $settingsCache->set( \json_encode( $allSettings ) );
        $this->cache->save( $settingsCache );
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
            if ( $value === null || ( $key == 'maintenanceMode' && $value == false ) ) {
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
