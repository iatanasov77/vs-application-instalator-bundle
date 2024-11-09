<?php namespace Vankosoft\ApplicationBundle\Model;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Comparable;
use Sylius\Component\Taxonomy\Model\Taxon as BaseTaxon;

/** Use Sylius Interfaces for Parameter and Return Types For Parent Models Compatibility */
use Sylius\Component\Taxonomy\Model\TaxonInterface;
use Sylius\Component\Taxonomy\Model\TaxonTranslationInterface;
// use Vankosoft\ApplicationBundle\Model\Interfaces\TaxonInterface;
// use Vankosoft\ApplicationBundle\Model\Interfaces\TaxonTranslationInterface;

use Vankosoft\ApplicationBundle\Model\Interfaces\TaxonInterface as VsTaxonInterface;
use Vankosoft\ApplicationBundle\Model\Interfaces\TaxonomyInterface;
use Vankosoft\CmsBundle\Model\Interfaces\FileInterface;

class Taxon extends BaseTaxon implements VsTaxonInterface, Comparable
{
    /** @var TaxonomyInterface */
    protected $taxonomy;
    
    /**
     * @var Collection|FileInterface[]
     *
     * @psalm-var Collection<array-key, ImageInterface>
     */
    protected $images;
    
    public function __construct()
    {
        parent::__construct();
        
        /** @var ArrayCollection<array-key, ImageInterface> $this->images */
        $this->images   = new ArrayCollection();
    }
    
    /**
     * Overide Base Taxon Method
     * 
     * @param string $code
     */
    public function setCode( ?string $code ): void
    {
        $this->code = $code;
        $this->setSlug( $code );
    }
    
    public function hasChild( TaxonInterface $taxon ): bool
    {
        if ( ! $this->children ) {
            $this->children = new ArrayCollection();
        }
        return $this->children->contains( $taxon );
    }
    
    public function getCurrentLocale()
    {
        return $this->currentLocale;
    }
    
    public function getTaxonomy()
    {
        return $this->taxonomy;
    }
    
    public function getImages(): Collection
    {
        return $this->images;
    }
    
    public function getImagesByType(string $type): Collection
    {
        return $this->images->filter(function (FileInterface $image) use ($type): bool {
            return $type === $image->getType();
        });
    }
    
    public function hasImages(): bool
    {
        return !$this->images->isEmpty();
    }
    
    public function hasImage(FileInterface $image): bool
    {
        return $this->images->contains($image);
    }
    
    public function addImage(FileInterface $image): void
    {
        $image->setOwner($this);
        $this->images->add($image);
    }
    
    public function removeImage(FileInterface $image): void
    {
        if ($this->hasImage($image)) {
            $image->setOwner(null);
            $this->images->removeElement($image);
        }
    }
    
    /**
     * {@inheritDoc}
     * @see \Doctrine\Common\Comparable::compareTo($other)
     */
    public function compareTo($other): int
    {
        if ( $this->code === $other->getCode() ) {
            return 0;
        } elseif ( $this->getParent() && $this->getParent()->getCode() === $other->getCode() ) {
            return -1;
        } else {
            return 1;
        }
    }
    
    public function getExistingTranslations()
    {
        return array_keys( $this->translationsCache );
    }
    
    public function createNewTranslation(): TaxonTranslationInterface
    {
        return $this->createTranslation();
    }
}
