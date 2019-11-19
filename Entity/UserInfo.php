<?php namespace IA\UsersBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="IAUM_UsersInfo")
 */
class UserInfo
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;
    
    /**
     * @ORM\Column(name="apiToken", type="string", unique=true, nullable=true)
     */
    protected $apiToken;
    
    /**
     * @var string
     *
     * @ORM\Column(name="firstName", type="string", length=128, nullable=false)
     */
    protected $firstName;
    
    /**
     * @var string
     *
     * @ORM\Column(name="lastName", type="string", length=128, nullable=false)
     */
    protected $lastName;
    
    /**
     * @var string
     *
     * @ORM\Column(name="country", type="string", length=3, nullable=false)
     */
    protected $country;
    
    /**
     * @var \DateTime
     *
     * @ORM\Column(name="birthday", type="datetime", nullable=true)
     */
    protected $birthday;
    
    /**
     * @var string
     *
     * @ORM\Column(name="mobile", type="integer", length=16, nullable=true)
     */
    protected $mobile;
    
    /**
     * @var string
     *
     * @ORM\Column(name="website", type="string", length=64, nullable=true)
     */
    protected $website;
    
    /**
     * @var string
     *
     * @ORM\Column(name="occupation", type="string", length=64, nullable=true)
     */
    protected $occupation;
    
    public function getApiToken()
    {
        return $this->apiToken;
    }
    
    public function setApiToken($apiToken)
    {
        $this->apiToken = $apiToken;
        
        return $this;
    }
    
    public function getFirstName()
    {
        return $this->firstName;
    }
    
    public function getLastName()
    {
        return $this->lastName;
    }
    
    public function setFirstName($firstName)
    {
        $this->firstName = $firstName;
        
        return $this;
    }
    
    public function setLastName($lastName)
    {
        $this->lastName = $lastName;
        
        return $this;
    }
    
    public function getCountry()
    {
        return $this->country;
    }
    
    public function getBirthday()
    {
        return $this->birthday;
    }
    
    public function getMobile()
    {
        return $this->mobile;
    }
    
    public function getWebsite()
    {
        return $this->website;
    }
    
    public function setCountry($country)
    {
        $this->country = $country;
        
        return $this;
    }
    
    public function setBirthday(\DateTime $birthday)
    {
        $this->birthday = $birthday;
        
        return $this;
    }
    
    public function setMobile($mobile)
    {
        $this->mobile = $mobile;
        
        return $this;
    }
    
    public function setWebsite($website)
    {
        $this->website = $website;
        
        return $this;
    }
    
    public function getOccupation() {
        return $this->occupation;
    }
    
    public function setOccupation($occupation) {
        $this->occupation = $occupation;
        
        return $this;
    }
    
    public function getFullName()
    {
        return $this->firstName . ' ' . $this->lastName;
    }
}
