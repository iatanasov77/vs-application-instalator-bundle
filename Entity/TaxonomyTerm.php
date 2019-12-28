<?php namespace IA\CmsBundle\Entity;

use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\ORM\Mapping as ORM;
use Sylius\Component\Resource\Model\ResourceInterface;
use Sylius\Component\Resource\Model\ToggleableTrait;

/**
 * Term
 *
 * @ORM\Table(name="IACMS_TaxonomyTerm")
 * @Gedmo\Tree(type="nested")
 * @ORM\Entity(repositoryClass="IACmsBundle\Entity\Repository\TaxonomyTermsRepository")
 */
class TaxonomyTerm implements ResourceInterface
{
    use ToggleableTrait;
    
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
     * @Gedmo\Slug(fields={"name"})
     * @ORM\Column(length=128, unique=true)
     */
    private $slug;

    /**
     * @var datetime $created
     *
     * @Gedmo\Timestampable(on="create")
     * @ORM\Column(type="datetime")
     */
    private $created;

    /**
     * @var datetime $updated
     *
     * @Gedmo\Timestampable(on="update")
     * @ORM\Column(type="datetime")
     */
    private $updated;

    /**
     * @Gedmo\TreeLeft
     * @ORM\Column(name="lft", type="integer")
     */
    private $lft = 1;

    /**
     * @Gedmo\TreeLevel
     * @ORM\Column(name="lvl", type="integer")
     */
    private $lvl = 1;

    /**
     * @Gedmo\TreeRight
     * @ORM\Column(name="rgt", type="integer")
     */
    private $rgt = 1;

    /**
     * @Gedmo\TreeRoot
     * @ORM\Column(name="root", type="integer", nullable=true)
     */
    private $root;

    /**
     * @Gedmo\TreeParent
     * @ORM\ManyToOne(targetEntity="Term", inversedBy="children")
     * @ORM\JoinColumn(name="parent_id", referencedColumnName="id", onDelete="CASCADE")
     */
    private $parent;

    /**
     * @ORM\OneToMany(targetEntity="Term", mappedBy="parent")
     * @ORM\OrderBy({"lft" = "ASC"})
     */
    private $children;

    /**
     * 
     * @ORM\ManyToOne(targetEntity="TaxonomyVocabulary", inversedBy="terms")
     * @ORM\JoinColumn(name="vocabulary_id", referencedColumnName="id")
     */
    private $vocabulary;
    
    /**
     *
     * @ORM\Column(name="vocabulary_id", type="integer", nullable=true)
     */
    private $vocabulary_id;
    
    function getVocabulary_id()
    {
        return $this->vocabulary_id;
    }

    function setVocabulary_id($vocabulary_id) 
    {
        $this->vocabulary_id = $vocabulary_id;
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
     * Set name
     *
     * @param  string $name
     * @return TaxonomyTerm
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
     * @param  string $description
     * @return TaxonomyTerm
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
     * Set lft
     *
     * @param  integer $lft
     * @return TaxonomyTerm
     */
    public function setLft($lft)
    {
        $this->lft = $lft;

        return $this;
    }

    /**
     * Get lft
     *
     * @return integer
     */
    public function getLft()
    {
        return $this->lft;
    }

    /**
     * Set lvl
     *
     * @param  integer $lvl
     * @return TaxonomyTerm
     */
    public function setLvl($lvl)
    {
        $this->lvl = $lvl;

        return $this;
    }

    /**
     * Get lvl
     *
     * @return integer
     */
    public function getLvl()
    {
        return $this->lvl;
    }

    /**
     * Set rgt
     *
     * @param integer $rgt
     *
     * @return TaxonomyTerm
     */
    public function setRgt($rgt)
    {
        $this->rgt = $rgt;

        return $this;
    }

    /**
     * Get rgt
     *
     * @return integer
     */
    public function getRgt()
    {
        return $this->rgt;
    }

    /**
     * Set root
     *
     * @param integer $root
     *
     * @return TaxonomyTerm
     */
    public function setRoot($root)
    {
        $this->root = $root;

        return $this;
    }

    /**
     * Get root
     *
     * @return integer
     */
    public function getRoot()
    {
        return $this->root;
    }

    /**
     * Set parent
     *
     * @param \IATaxonomyBundle\Entity\Term $parent
     *
     * @return TaxonomyTerm
     */
    public function setParent(\IATaxonomyBundle\Entity\Term $parent = null)
    {
        $this->parent = $parent;

        return $this;
    }

    /**
     * Get parent
     *
     * @return \IATaxonomyBundle\Entity\Term
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * Add children
     *
     * @param \IATaxonomyBundle\Entity\Term $children
     *
     * @return TaxonomyTerm
     */
    public function addChild(\IATaxonomyBundle\Entity\Term $children)
    {
        $this->children[] = $children;

        return $this;
    }

    /**
     * Remove children
     *
     * @param \IATaxonomyBundle\Entity\Term $children
     */
    public function removeChild(\IATaxonomyBundle\Entity\Term $children)
    {
        $this->children->removeElement($children);
    }

    /**
     * Get children
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getChildren()
    {
        return $this->children;
    }
    
    /**
     * Constructor
     */
    public function __construct(Vocabulary $vocabulary = null)
    {
        $this->vocabulary = $vocabulary;
        $this->children = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Set slug
     *
     * @param  string $slug
     * @return TaxonomyTerm
     */
    public function setSlug($slug)
    {
        $this->slug = $slug;

        return $this;
    }

    /**
     * Set created
     *
     * @param  \DateTime $created
     * @return TaxonomyTerm
     */
    public function setCreated($created)
    {
        $this->created = $created;

        return $this;
    }

    /**
     * Set updated
     *
     * @param  \DateTime $updated
     * @return TaxonomyTerm
     */
    public function setUpdated($updated)
    {
        $this->updated = $updated;

        return $this;
    }

    /**
     * Set vocabulary
     *
     * @param  \IATaxonomyBundle\Entity\Vocabulary $vocabulary
     * @return TaxonomyTerm
     */
    public function setVocabulary(\IATaxonomyBundle\Entity\Vocabulary $vocabulary = null)
    {
        $this->vocabulary = $vocabulary;

        return $this;
    }

    /**
     * Get vocabulary
     *
     * @return \IATaxonomyBundle\Entity\Vocabulary
     */
    public function getVocabulary()
    {
        return $this->vocabulary;
    }

   
}

