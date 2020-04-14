<?php namespace IA\UsersBundle\Entity;

use FOS\UserBundle\Model\User as BaseUser;
//use Uecode\Bundle\ApiKeyBundle\Model\ApiKeyUser as BaseUser;
use Sylius\Component\Resource\Model\ResourceInterface;
use Doctrine\ORM\Mapping as ORM;
use IA\Component\Utils;

/**
 * @ORM\Entity
 * @ORM\Table(name="IAUM_Users")
 */
class User extends BaseUser implements ResourceInterface
{
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
     * @ORM\OneToOne(targetEntity="IA\UsersBundle\Entity\Model\SubscriptionInterface", inversedBy="user")
     * @ORM\JoinColumn(name="subscriptionId", referencedColumnName="id")
     */
    protected $subscription;
    
    /**
     * @ORM\OneToMany(targetEntity="IA\UsersBundle\Entity\UserActivity", mappedBy="user")
     */
    protected $activities;
    
    /**
     * @ORM\OneToMany(targetEntity="IA\UsersBundle\Entity\UserNotification", mappedBy="user")
     */
    protected $notifications;
    
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
    
    public function getSubscription() 
    {
        return $this->subscription;
    }

    public function setSubscription(&$subscription)
    {
        $subscription->setUser($this);
        $this->subscription = $subscription;
        
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
