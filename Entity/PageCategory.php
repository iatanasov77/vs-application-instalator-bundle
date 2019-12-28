<?php namespace IA\CmsBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

/**
 * @ORM\Table(name="IACMS_PageCategories")
 * @ORM\Entity(repositoryClass="IA\CmsBundle\Entity\Repository\PagesRepository")
 */
class PageCategory
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer", precision=0, scale=0, nullable=false, unique=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;
    
    /**
     * @var string
     *
     * @ORM\Column(name="title", type="string", length=64, precision=0, scale=0, nullable=false, unique=false)
     */
    private $title;
    
    /**
     * @ORM\OneToMany(targetEntity="Page", mappedBy="category")
     */
    private $pages;
    
    public function __construct()
    {
        $this->pages = new ArrayCollection();
    }
    
    public function getId()
    {
        return $this->id;
    }
    
    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }
    
    public function getTitle()
    {
        return $this->title;
    }
    
    public function setTitle($title)
    {
        $this->title = $title;
        return $this;
    }
    
    /**
     * @return Collection|Page[]
     */
    public function getPages(): Collection
    {
        return $this->pages;
    }
}
