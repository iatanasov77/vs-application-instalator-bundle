<?php namespace IA\UsersBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Sylius\Component\Resource\Model\ResourceInterface;
use Sylius\Component\Resource\Model\ToggleableTrait;

/**
 * Plan
 *
 * @ORM\Table(name="IAUM_Plans")
 * @ORM\Entity
 */
class Plan implements ResourceInterface
{
    use ToggleableTrait;
    
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="title", type="string", length=64, nullable=true)
     */
    private $title;
    
    /** 
     * 
     * @ORM\Column(type="string") 
     */
    private $subscriptionPeriod;
    
    /**
     * @ORM\OneToMany(targetEntity="PackagePlan", mappedBy="plan")
     */
    public $plans;
    

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set title
     *
     * @param string $title
     * @return Plan
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get title
     *
     * @return string 
     */
    public function getTitle()
    {
        return $this->title;
    }
    
    function getSubscriptionPeriod()
    {
        return $this->subscriptionPeriod;
    }

    function setSubscriptionPeriod($subscriptionPeriod)
    {
        $this->subscriptionPeriod = $subscriptionPeriod;
        
        return $this;
    }

        
    function getFields() 
    {
        return $this->packagePlans->toArray();
    }
    
    public function addField(PackagePlan $packagePlan)
    {
        if($packagePlan->getTitle() 
                && !$this->packagePlans->contains($packagePlan)) {
            $packagePlan->setPlan($this);
            $this->packagePlans->add($packagePlan);
        }
        
        return $this;
    }
    
    public function removeField(PackagePlan $packagePlan)
    {
        if ($this->packagePlans->contains($packagePlan)) {
            $this->packagePlans->removeElement($packagePlan);
        }
        
        return $this;
    }
    
    function getPlans() 
    {
        return $this->plans->toArray();
    }
    
    public function __toString()
    {
        return $this->title;
    }
    
}
