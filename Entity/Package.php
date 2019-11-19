<?php namespace IA\UsersBundle\Entity;

use Sylius\Component\Resource\Model\ResourceInterface;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Sylius\Component\Resource\Model\ToggleableTrait;

/**
 * @ORM\Entity
 * @ORM\Table(name="IAUM_Packages")
 */
class Package implements ResourceInterface
{
    use ToggleableTrait;

    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;
    
    /**
     * @var string
     *
     * @ORM\Column(name="title", type="string", length=64, nullable=false)
     */
    protected $title;
    
    /**
     * @ORM\OneToMany(targetEntity="PackagePlan", mappedBy="package", cascade={"persist"})
     */
    public $plans;
    
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->plans = new ArrayCollection();
    }

    
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
     * @return Package
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
    
    
    function getPlans() 
    {
        return $this->plans->toArray();
    }
    
    public function addPlan(PackagePlan $plan)
    {
        if(!$this->plans->contains($plan)) {
            $plan->setPackage($this);
            $this->plans->add($plan);
        }
        
        return $this;
    }
    
    public function removePlan(PackagePlan $plan)
    {
        if ($this->plans->contains($plan)) {
            $this->plans->removeElement($plan);
        }
        
        return $this;
    }
    
}
