<?php namespace IA\UsersBundle\Entity\Trait;

trait UserSubscriptionTrait
{
    /**
     * @ORM\OneToOne(targetEntity="IA\UsersBundle\Entity\UserSubscription", inversedBy="user")
     * @ORM\JoinColumn(name="subscriptionId", referencedColumnName="id")
     */
    protected $subscription;
    
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
}
