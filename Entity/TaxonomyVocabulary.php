<?php namespace IA\CmsBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Sylius\Component\Resource\Model\ResourceInterface;
use Sylius\Component\Resource\Model\ToggleableTrait;

use Gedmo\Mapping\Annotation as Gedmo;
use Gedmo\Timestampable\Traits\TimestampableEntity;

/**
 * Vocabulary
 *
 * @ORM\Table(name="IACMS_TaxonomyVocabularies")
 * @ORM\Entity
 */
class TaxonomyVocabulary implements ResourceInterface
{
    use ToggleableTrait;
    use TimestampableEntity;
    
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="string", length=255, nullable=true)
     */
    private $description;

    /**
    * @ORM\OneToMany(targetEntity="TaxonomyTerm", mappedBy="vocabulary", cascade={"remove","persist"})
    */
    private $terms;

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
     * Set name
     *
     * @param  string     $name
     * @return TaxonomyVocabulary
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set description
     *
     * @param  string     $description
     * @return TaxonomyVocabulary
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Get slug
     *
     * @return string
     */
    public function getSlug()
    {
        return $this->slug;
    }

    /**
     * Get created
     *
     * @return string
     */
    public function getCreated()
    {
        return $this->created;
    }

    /**
     * Get updated
     *
     * @return string
     */
    public function getUpdated()
    {
        return $this->updated;
    }

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->terms = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Set slug
     *
     * @param  string     $slug
     * @return TaxonomyVocabulary
     */
    public function setSlug($slug)
    {
        $this->slug = $slug;

        return $this;
    }

    /**
     * Set created
     *
     * @param  \DateTime  $created
     * @return TaxonomyVocabulary
     */
    public function setCreated($created)
    {
        $this->created = $created;

        return $this;
    }

    /**
     * Set updated
     *
     * @param  \DateTime  $updated
     * @return TaxonomyVocabulary
     */
    public function setUpdated($updated)
    {
        $this->updated = $updated;

        return $this;
    }

    /**
     * Add terms
     *
     * @param  \IATaxonomyBundle\Entity\Term $terms
     * @return TaxonomyVocabulary
     */
    public function addTerm(Term $term)
    {
        if(!$this->terms->contains($term)) {
            $term->setVocabulary($this);
            $this->terms[] = $term;
        }

        return $this;
    }

    /**
     * Remove terms
     *
     * @param \IATaxonomyBundle\Entity\Term $terms
     */
    public function removeTerm(Term $term)
    {
        if($this->terms->contains($term)) {
            $this->terms->removeElement($terms);
        }
    }

    /**
     * Get terms
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getTerms()
    {
        return $this->terms;
    }
}

