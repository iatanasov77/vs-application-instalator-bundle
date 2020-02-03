<?php namespace IA\UsersBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Sylius\Component\Resource\Model\ToggleableTrait;
use Sylius\Component\Resource\Model\ResourceInterface;

/**
 * @ORM\Entity
 * @ORM\Table(name="IAUM_Packages_Plans")
 */
class PackagePlan implements ResourceInterface //, \IA\PaymentBundle\Model\CheckoutOrderInterface
{
    use ToggleableTrait;

    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * 
     * @ORM\ManyToOne(targetEntity="Package", inversedBy="plans")
     * @ORM\JoinColumn(name="packageId", referencedColumnName="id")
     */
    private $package;
    
    /**
     * 
     * @ORM\ManyToOne(targetEntity="Plan", inversedBy="plans")
     * @ORM\JoinColumn(name="planId", referencedColumnName="id")
     */
    private $plan;

    
    /**
     * @var string
     *
     * @ORM\Column(name="price", type="decimal", precision=6, scale=2, nullable=false)
     */
    private $price;
    
    /**
     *
     * @var string
     *
     *  @ORM\Column(name="currency", type="text", length=4, nullable=false)
     */
    private $currency;
    
    /**
     *
     * @var string
     * 
     *  @ORM\Column(name="description", type="text", length=256, nullable=false)
     */
    private $description;
    
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
     * Set package
     *
     * @param Package $package
     * @return PackagePlan
     */
    public function setPackage(Package $package)
    {
        $this->package = $package;

        return $this;
    }

    /**
     * Get package
     *
     * @return Package
     */
    public function getPackage()
    {
        return $this->package;
    }
    
    /**
     * Set plan
     *
     * @param Plan $plan
     * @return PackagePlan
     */
    public function setPlan(Plan $plan)
    {
        $this->plan = $plan;

        return $this;
    }

    /**
     * Get plan
     *
     * @return PackagePlan
     */
    public function getPlan()
    {
        return $this->plan;
    }
 
    function getPrice()
    {
        return $this->price;
    }

    function setPrice($price)
    {
        $this->price = $price;
        
        return $this;
    }

    function getCurrency()
    {
        return $this->currency;
    }
    
    function setCurrency($currency)
    {
        $this->currency = $currency;
        
        return $this;
    }
    
    function getDescription()
    {
        return $this->description;
    }

    function setDescription($description)
    {
        $this->description = $description;
        
        return $this;
    }
    
    public function getBillingPeriod()
    {
        return $this->plan ? $this->plan->getSubscriptionPeriod() : null;
    }
    
    public function getBillingFrequency()
    {
        return 1;
    }

}
