<?php namespace Vankosoft\ApplicationBundle\Model\Traits;

use Vankosoft\UsersBundle\Model\Interfaces\UserInterface;

/**
 * Created From \Pd\WidgetBundle\Entity\WidgetUser
 */
trait WidgetUserTrait
{
    /** @var array */
    protected $config;
    
    /** @var UserInterface | null */
    protected $owner;
    
    public function setConfig(array $config): self
    {
        $this->config = $config;
        
        return $this;
    }
    
    public function getConfig(): array
    {
        return $this->config;
    }
    
    public function addWidgetConfig(string $widgetId, array $config = []): self
    {
        $this->config[$widgetId] = array_merge($this->config[$widgetId] ?? [], $config);
        
        return $this;
    }
    
    public function removeWidgetConfig(string $widgetId, array $config = []): self
    {
        foreach ($config as $id => $content) {
            if (isset($this->config[$widgetId][$id])) {
                unset($this->config[$widgetId][$id]);
            }
        }
        
        return $this;
    }
    
    public function getOwner(): ?UserInterface
    {
        return $this->owner;
    }
    
    public function setOwner($owner = null): self
    {
        $this->owner = $owner;
        
        return $this;
    }
}
