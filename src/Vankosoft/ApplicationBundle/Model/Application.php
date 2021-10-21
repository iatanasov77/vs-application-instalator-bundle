<?php namespace VS\ApplicationBundle\Model;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Sylius\Component\Resource\Model\TimestampableTrait;
use Sylius\Component\Resource\Model\ToggleableTrait;
use VS\ApplicationBundle\Model\Interfaces\ApplicationInterface;

class Application implements ApplicationInterface
{
    use TimestampableTrait, ToggleableTrait;
    
    /** @var integer */
    protected $id;
    
    /** @var Collection|Settings[] */
    protected $settings;
    
    /** @var string */
    protected $title;
    
    /** @var string */
    protected $hostname;
    
    /** @var string */
    protected $code;
    
    public function __construct()
    {
        $this->settings = new ArrayCollection();
    }
    
    public function getId()
    {
        return $this->id;
    }
    
    public function getSettings() : Collection
    {
        return $this->settings;
    }
    
    public function getTitle()
    {
        return $this->title;
    }
    
    public function setTitle( $title ) : self
    {
        $this->title = $title;
        
        return $this;
    }
    
    public function getHostname()
    {
        return $this->hostname;
    }
    
    public function setHostname( $hostname ) : self
    {
        $this->hostname = $hostname;
        
        return $this;
    }
    
    
    public function getCode()
    {
        return $this->code;
    }
    
    public function setCode( $code ) : self
    {
        $this->code = $code;
        
        return $this;
    }
}
