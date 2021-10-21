<?php namespace VS\ApplicationBundle\Model;

use VS\ApplicationBundle\Model\Interfaces\SettingsInterface;
use VS\ApplicationBundle\Model\Interfaces\ApplicationInterface;
use VS\CmsBundle\Model\PageInterface;

class Settings implements SettingsInterface
{   
    /** @var integer */
    protected $id;

    /** @var boolean */
    protected $maintenanceMode;
    
    /** @var VS\CmsBundle\Model\PageInterface */
    protected $maintenancePage;
    
    /** @var string */
    protected $theme;
    
    /** @var ApplicationInterface */
    protected $application;
    
    public function getId()
    {
        return $this->id;
    }
  
    public function setMaintenanceMode( $maintenanceMode )
    {
        $this->maintenanceMode  = $maintenanceMode;

        return $this;
    }

    public function getMaintenanceMode()
    {
        return $this->maintenanceMode;
    }
    
    public function getMaintenancePage() : ?PageInterface
    {
        return $this->maintenancePage;
    }
    
    public function setMaintenancePage( ?PageInterface $maintenancePage ) : self
    {
        $this->maintenancePage  = $maintenancePage;
        
        return $this;
    }
    
    public function setTheme( $theme )
    {
        $this->theme    = $theme;
        
        return $this;
    }
    
    public function getTheme()
    {
        return $this->theme;
    }
    
    public function getApplication()
    {
        return $this->application;
    }
    
    public function setApplication( $application ) : self
    {
        $this->application  = $application;
        
        return $this;
    }
}
