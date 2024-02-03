<?php namespace Vankosoft\ApplicationBundle\Model;

use Vankosoft\ApplicationBundle\Model\Interfaces\WidgetInterface;
use Vankosoft\ApplicationBundle\Model\Interfaces\WidgetGroupInterface;
use Vankosoft\UsersBundle\Model\UserRoleInterface;
use Sylius\Component\Resource\Model\ToggleableTrait;
use Sylius\Component\Resource\Model\TranslatableTrait;
use Sylius\Component\Resource\Model\TranslationInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

class Widget implements WidgetInterface
{
    use ToggleableTrait;    // About enabled field - $enabled (active)
    use TranslatableTrait;
    
    /** @var integer */
    protected $id;
    
    /** @var WidgetGroupInterface */
    protected $group;
    
    /** @var string */
    protected $code;
    
    /** @var string */
    protected $name;
    
    /** @var string */
    protected $description;
    
    /** @var Collection|UserRoleInterface[] */
    protected $allowedRoles;
    
    /** @var bool */
    protected $enabled = true;
    
    /** @var string */
    protected $locale;
    
    public function __construct()
    {
        $this->allowedRoles = new ArrayCollection();
    }
    
    public function getId()
    {
        return $this->id;
    }
    
    public function getGroup(): ?WidgetGroupInterface
    {
        return $this->group;
    }
    
    public function setGroup( WidgetGroupInterface $group )
    {
        $this->group  = $group;
        
        return $this;
    }
    
    public function getCode(): string
    {
        return $this->code;
    }
    
    public function setCode( string $code ) : self
    {
        $this->code = $code;
        
        return $this;
    }
    
    public function getName(): string
    {
        return $this->name;
    }
    
    public function setName( string $name ) : self
    {
        $this->name = $name;
        
        return $this;
    }
    
    public function getDescription(): string
    {
        return $this->description;
    }
    
    public function setDescription( string $description ) : self
    {
        $this->description = $description;
        
        return $this;
    }
    
    /**
     * @return Collection|UserRoleInterface[]
     */
    public function getAllowedRoles(): Collection
    {
        return $this->allowedRoles;
    }
    
    public function addAllowedRole( UserRoleInterface $allowedRole ): self
    {
        if ( ! $this->allowedRoles->contains( $allowedRole ) ) {
            $this->allowedRoles[] = $allowedRole;
        }
        
        return $this;
    }
    
    public function removeAllowedRole( UserRoleInterface $allowedRole ): self
    {
        if ( $this->allowedRoles->contains( $allowedRole ) ) {
            $this->allowedRoles->removeElement( $allowedRole );
        }
        
        return $this;
    }
    
    public function getActive(): ?bool
    {
        return $this->enabled;
    }
    
    public function setActive( ?bool $active ): self
    {
        $this->enabled = (bool) $active;
        
        return $this;
    }
    
    public function isActive(): bool
    {
        return $this->enabled;
    }
    
    public function getLocale()
    {
        return $this->currentLocale;
    }
    
    public function getTranslatableLocale(): ?string
    {
        return $this->locale;
    }
    
    public function setTranslatableLocale($locale): self
    {
        $this->locale = $locale;
        
        return $this;
    }
    
    protected function createTranslation(): TranslationInterface
    {
        
    }
}