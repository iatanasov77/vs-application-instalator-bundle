<?php namespace VS\CmsBundle\Model;

use Doctrine\ORM\Mapping as ORM;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

use VS\ApplicationBundle\Model\Interfaces\TaxonInterface;

/**
 * Page Category Model
 */
class PageCategory implements PageCategoryInterface
{
    /** @var mixed */
    protected $id;
    
    /** @var Collection|Page[] */
    protected $pages;
    
    /** @var TaxonInterface */
    protected $taxon;
    
    /** @var int */
    //protected $position;
    
    public function __construct()
    {
        $this->pages = new ArrayCollection();
    }
    
    /**
     * {@inheritdoc}
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
    
    public function addPage( Page $page ): self
    {
        if ( ! $this->pages->contains( $page ) ) {
            $this->pages[] = $page;
            $page->addCategory( $this );
        }
        return $this;
    }
    
    public function removePage( Page $page ): self
    {
        if ( $this->pages->contains( $page ) ) {
            $this->pages->removeElement( $page );
            $page->removeCategory( $this );
        }
        return $this;
    }
    
    /**
     * {@inheritdoc}
     */
    public function getTaxon(): ?TaxonInterface
    {
        return $this->taxon;
    }
    
    /**
     * {@inheritdoc}
     */
    public function setTaxon(?TaxonInterface $taxon): void
    {
        $this->taxon = $taxon;
    }
    
    /**
     * {@inheritdoc}
     */
//     public function getPosition(): ?int
//     {
//         return $this->position;
//     }
    
    /**
     * {@inheritdoc}
     */
//     public function setPosition(?int $position): void
//     {
//         $this->position = $position;
//     }

    public function getName()
    {
        return $this->taxon ? $this->taxon->getName() : '';
    }
    
    public function __toString()
    {
        return $this->taxon ? $this->taxon->getName() : '';
    }
}
