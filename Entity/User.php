<?php namespace IA\UsersBundle\Entity;

use FOS\UserBundle\Model\User as BaseUser;
//use Uecode\Bundle\ApiKeyBundle\Model\ApiKeyUser as BaseUser;
use Sylius\Component\Resource\Model\ResourceInterface;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @ORM\Entity
 * @ORM\Table(name="IAUM_Users")
 */
class User extends BaseUser implements ResourceInterface
{
    /**
     * FOR USERS WITH SUBSCRIPTION DERIVE THIS ENTITY AND ADD USING
     * THE TRAIT `IA\UsersBundle\Entity\Traits\UserSubscriptionTrait` FORM THIS BUNDLE
     */
    //use Traits\UserSubscriptionTrait;
    
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;
    
    /**
     * @ORM\OneToOne(targetEntity="IA\UsersBundle\Entity\UserInfo", cascade={"persist"})
     * @ORM\JoinColumn(name="user_info_id", referencedColumnName="id")
     */
    protected $userInfo;
    
    /**
     * @ORM\ManyToMany(targetEntity="IA\UsersBundle\Entity\UserGroup")
     * @ORM\JoinTable(name="IAUM_Users_Groups",
     *      joinColumns={@ORM\JoinColumn(name="userId", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="groupId", referencedColumnName="id")}
     * )
     */
    protected $groups;
    
    /**
     * @ORM\OneToMany(targetEntity="IA\UsersBundle\Entity\UserActivity", mappedBy="user")
     */
    protected $activities;
    
    /**
     * @ORM\OneToMany(targetEntity="IA\UsersBundle\Entity\UserNotification", mappedBy="user")
     */
    protected $notifications;
    
    public function __construct()
    {
        $this->groups    = new ArrayCollection();
        parent::__construct();
    }
    
    // She ti eba i formite
    public function __get( $var )
    {
        if ( property_exists ( 'IA\UsersBundle\Entity\UserInfo' , $var ) ) {
            return $this->userInfo ? $this->userInfo->$var : null;
        }
    }
    
    public function getUserInfo()
    {
        return $this->userInfo;
    }
    
    public function setUserInfo( $userInfo )
    {
        $this->userInfo = $userInfo;
        
        return $this;
    }
    
    /**
     * @return Collection|UserActivity[]
     */
    public function getActivities()
    {
        return $this->activities;
    }
    
    /**
     * @return Collection|UserActivity[]
     */
    public function getNotifications()
    {
        return $this->notifications;
    }

    public function __toString()
    {
        return ( string ) $this->userInfo ? $this->userInfo->getFullName() : '';
    }
}
