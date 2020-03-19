<?php namespace IA\CmsBundle\Entity;

use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

/**
 * @Gedmo\Tree(type="nested")
 * @ORM\Table(name="IACMS_PageCategories")
 * @ORM\Entity(repositoryClass="IA\CmsBundle\Entity\Repository\PageCategoryRepository")
 */
class PageCategory
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
     * @ORM\Column(type="string")
     */
    private $name;
    
    /**
     * @ORM\OneToMany(targetEntity="Page", mappedBy="category")
     */
    private $pages;
    
    /**
     * @Gedmo\TreeLeft()
     * @ORM\Column(type="integer")
     */
    private $lft;
    
    /**
     * @Gedmo\TreeLevel()
     * @ORM\Column(type="integer")
     */
    private $lvl;
    
    /**
     * @Gedmo\TreeRight()
     * @ORM\Column(type="integer")
     */
    private $rgt;
    
    /**
     * @Gedmo\TreeRoot()
     * @ORM\ManyToOne(targetEntity="PageCategory",cascade={"persist"})
     * @ORM\JoinColumn(referencedColumnName="id", onDelete="CASCADE")
     */
    private $root;
    
    /**
     * @Gedmo\TreeParent()
     * @ORM\ManyToOne(targetEntity="PageCategory", inversedBy="children",cascade={"persist"})
     * @ORM\JoinColumn(referencedColumnName="id", onDelete="CASCADE")
     */
    private $parent;
    
    /**
     * @ORM\OneToMany(targetEntity="PageCategory", mappedBy="parent",cascade={"persist"})
     * @ORM\OrderBy({"lft" = "ASC"})
     */
    private $children;
    
    
    public function __construct()
    {
        $this->posts = new ArrayCollection();
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
