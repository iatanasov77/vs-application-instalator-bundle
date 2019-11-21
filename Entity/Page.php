<?php

namespace IA\CmsBundle\Entity;

use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\ORM\Mapping as ORM;
use Sylius\Component\Resource\Model\ResourceInterface;
use Sylius\Component\Resource\Model\ToggleableTrait;
use Sylius\Component\Resource\Model\SlugAwareInterface;

/**
 * Page
 *
 * @ORM\Table(name="IA_Cms_Pages")
 * @ORM\Entity(repositoryClass="IA\CmsBundle\Entity\Repository\PagesRepository")
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
    private $id;

    /**
     * @var string
     *
     * @Gedmo\Slug(fields={"title"})
     * @ORM\Column(name="slug", type="string", length=255, nullable=false, unique=true)
     */
    private $slug;

    /**
     * @var string
     *
     * @ORM\Column(name="title", type="string", length=255, precision=0, scale=0, nullable=false, unique=false)
     */
    private $title;

    /**
     * @var string
     *
     * @ORM\Column(name="text", type="text", precision=0, scale=0, nullable=false, unique=false)
     */
    private $text;

    function getId()
    {
        return $this->id;
    }

    function getSlug() : ?string
    {
        return $this->slug;
    }

    function getTitle()
    {
        return $this->title;
    }

    function getText()
    {
        return $this->text;
    }

    function setId($id)
    {
        $this->id = $id;
        return $this;
    }

    function setSlug($slug=null) : void
    {
        $this->slug = $slug;
        //return $this;
    }

    function setTitle($title)
    {
        $this->title = $title;
        return $this;
    }

    function setText($text)
    {
        $this->text = $text;
        return $this;
    }

}
