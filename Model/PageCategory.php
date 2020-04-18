<?php namespace IA\CmsBundle\Model;

use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Sylius\Component\Resource\Model\ResourceInterface;

class PageCategory implements ResourceInterface
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    protected $id;
    
    /**
     * @ORM\Column(type="string")
     * @Gedmo\Translatable
     */
    protected $name;
    
    /**
     * @ORM\OneToMany(targetEntity="Page", mappedBy="category")
     */
    protected $pages;
    
    /**
     * @Gedmo\TreeLeft()
     * @ORM\Column(type="integer")
     */
    protected $lft;
    
    /**
     * @Gedmo\TreeLevel()
     * @ORM\Column(type="integer")
     */
    protected $lvl;
    
    /**
     * @Gedmo\TreeRight()
     * @ORM\Column(type="integer")
     */
    protected $rgt;
    
    /**
     * @Gedmo\TreeRoot()
     * @ORM\ManyToOne(targetEntity="PageCategory",cascade={"persist"})
     * @ORM\JoinColumn(referencedColumnName="id", onDelete="CASCADE")
     */
    protected $root;
    
    /**
     * @Gedmo\TreeParent()
     * @ORM\ManyToOne(targetEntity="PageCategory", inversedBy="children",cascade={"persist"})
     * @ORM\JoinColumn(referencedColumnName="id", onDelete="CASCADE")
     */
    protected $parent;
    
    /**
     * @ORM\OneToMany(targetEntity="PageCategory", mappedBy="parent",cascade={"persist"})
     * @ORM\OrderBy({"lft" = "ASC"})
     */
    protected $children;
    
    /**
     * @Gedmo\Locale
     * Used locale to override Translation listener`s locale
     * this is not a mapped field of entity metadata, just a simple property
     * and it is not necessary because globally locale can be set in listener
     */
    protected $locale;
    
    public function __construct()
    {
        $this->pages        = new ArrayCollection();
    }
    
    public function setTranslatableLocale($locale)
    {
        $this->locale = $locale;
        
        return $this;
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
     * @return Collection|Page[]
     */
    public function getPages(): Collection
    {
        return $this->pages;
    }
    
    public function getName()
    {
        return $this->name;
    }
    
    public function setName($name)
    {
        $this->name = $name;
        
        return $this;
    }
    
    public function getRoot()
    {
        return $this->root;
    }
    
    public function setParent(PageCategory $parent = null)
    {
        $this->parent = $parent;
    }
    
    public function getParent()
    {
        return $this->parent;
    }
}
