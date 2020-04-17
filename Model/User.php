<?php namespace VS\UsersBundle\Model;

use FOS\UserBundle\Model\User as BaseUser;
use Doctrine\ORM\Mapping as ORM;

class User extends BaseUser implements UserInterface
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;
    
    /**
     * @ORM\Column(name="preferedLocale", type="string", length=6, nullable=false)
     */
    protected $preferedLocale;
    
    public function __construct()
    {
        parent::__construct();
    }
    
    public function getId()
    {
        return $this->id;
    }
    
    public function getPreferedLocale()
    {
        return $this->preferedLocale;
    }
    
    public function setPreferedLocale( $preferedLocale ) : self
    {
        $this->preferedLocale   = $preferedLocale;
        
        return $this;
    }
}
