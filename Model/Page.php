<?php namespace IA\CmsBundle\Model;

use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\ORM\Mapping as ORM;
use Sylius\Component\Resource\Model\ResourceInterface;
use Sylius\Component\Resource\Model\ToggleableTrait;
use Sylius\Component\Resource\Model\SlugAwareInterface;

/**
 * Page Model
 */
class Page implements ResourceInterface, SlugAwareInterface
{
    use ToggleableTrait;
    
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer", precision=0, scale=0, nullable=false, unique=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    protected $id;

    /**
     * @var string
     *
     * @Gedmo\Slug(fields={"title"})
     * @ORM\Column(name="slug", type="string", length=255, nullable=false, unique=true)
     */
    protected $slug;

    /**
     * @var string
     *
     * @Gedmo\Translatable
     * @ORM\Column(name="title", type="string", length=255, precision=0, scale=0, nullable=false, unique=false)
     */
    protected $title;

    /**
     * @var string
     *
     * @Gedmo\Translatable
     * @ORM\Column(name="text", type="text", precision=0, scale=0, nullable=false, unique=false)
     */
    protected $text;
    
    /**
     * @ORM\ManyToOne(targetEntity="PageCategory", inversedBy="pages")
     */
    protected $category;
    
    /**
     * @Gedmo\Locale
     * Used locale to override Translation listener`s locale
     * this is not a mapped field of entity metadata, just a simple property
     * and it is not necessary because globally locale can be set in listener
     */
    protected $locale;
    
    public function setTranslatableLocale($locale)
    {
        $this->locale = $locale;
        
        return $this;
    }
    
    public function getId()
    {
        return $this->id;
    }

    public function getSlug() : ?string
    {
        return $this->slug;
    }

    public function getTitle()
    {
        return $this->title;
    }

    public function getText()
    {
        return $this->text;
    }

    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }

    public function setSlug($slug=null) : void
    {
        $this->slug = $slug;
        //return $this;
    }

    public function setTitle($title)
    {
        $this->title = $title;
        return $this;
    }

    public function setText($text)
    {
        $this->text = $text;
        return $this;
    }

    public function getCategory(): ?PageCategory
    {
        return $this->category;
    }
    
    public function setCategory(?PageCategory $category): self
    {
        $this->category = $category;
        
        return $this;
    }
}
