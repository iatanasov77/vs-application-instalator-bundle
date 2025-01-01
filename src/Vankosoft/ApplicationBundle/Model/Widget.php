<?php namespace Vankosoft\ApplicationBundle\Model;

use Vankosoft\ApplicationBundle\Model\Interfaces\WidgetInterface;
use Vankosoft\ApplicationBundle\Model\Interfaces\WidgetGroupInterface;
use Vankosoft\UsersBundle\Model\Interfaces\UserRoleInterface;
use Sylius\Component\Resource\Model\ToggleableTrait;
use Vankosoft\ApplicationBundle\Model\Traits\TranslatableTrait;
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
    
    /** @var string | null */
    protected $description;
    
    /** @var Collection|UserRoleInterface[] */
    protected $allowedRoles;
    
    /** @var bool */
    protected $allowAnonymous = false;
    
    /** @var bool */
    protected $enabled = true;
    
    /** @var string */
    protected $locale;
    
    public function __construct()
    {
        $this->fallbackLocale   = 'en_US';
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
    
    public function getDescription(): ?string
    {
        return $this->description;
    }
    
    public function setDescription( ?string $description ) : self
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
    
    /**
     * @return array
     */
    public function getAllowedRolesFromCollection(): array
    {
        $roles  = [];
        foreach ( $this->allowedRoles as $role ) {
            $roles[]    = $role->getRole();
        }
        
        return \array_unique( $roles );
    }
    
    public function setAllowedRoles( Collection $allowedRoles ): self
    {
        $this->allowedRoles  = $allowedRoles;
        
        return $this;
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
    
    public function getAllowAnonymous(): ?bool
    {
        return $this->allowAnonymous;
    }
    
    public function setAllowAnonymous( ?bool $allowAnonymous ): self
    {
        $this->allowAnonymous = (bool) $allowAnonymous;
        
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
}