<?php namespace IA\UsersBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Entity\User;
use IA\PaymentBundle\Entity\Payment;

/**
 * Plan
 *
 * @ORM\Table(name="IAUM_UsersSubscriptions")
 * @ORM\Entity(repositoryClass="IA\UsersBundle\Entity\Repository\UserSubscriptionRepository")
 */
class UserSubscription
{

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @ORM\OneToOne(targetEntity="IA\UsersBundle\Entity\User", mappedBy="subscription")
     * @ORM\JoinColumn(name="userId", referencedColumnName="id")
     */
    private $user;

    /**
     * @ORM\OneToOne(targetEntity="PackagePlan")
     * @ORM\JoinColumn(name="planId", referencedColumnName="id")
     */
    private $plan;

    /**
     *
     * @var type 
     * 
     * @ORM\Column(name="date", type="datetime")
     */
    private $date;


    /**
     * @ORM\OneToOne(targetEntity="IA\PaymentBundle\Entity\Payment", cascade={"persist", "remove"})
     * @ORM\JoinColumn(name="paymentDetailsId", referencedColumnName="id", nullable=true)
     */
    private $paymentDetails;

    function getId()
    {
        return $this->id;
    }

    function getUser()
    {
        return $this->user;
    }

    function getPlan()
    {
        return $this->plan;
    }

    function getDate()
    {
        return $this->date;
    }

    function setId($id)
    {
        $this->id = $id;
        
        return $this;
    }

    function setUser($user)
    {
        $this->user = $user;
        
        return $this;
    }

    function setPlan($plan)
    {
        $this->plan = $plan;
        
        return $this;
    }

    function setDate($date)
    {
        $this->date = $date;
        
        return $this;
    }

    function getPaymentMethod()
    {
        return $this->paymentDetails->getPaymentMethod();
    }

    function getPaymentDetails()
    {
        return $this->paymentDetails;
    }

    function setPaymentDetails(Payment $paymentDetails)
    {
        $this->paymentDetails = $paymentDetails;

        return $this;
    }

}
