<?php namespace Vankosoft\ApplicationBundle\Model;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Comparable;
use Sylius\Component\Taxonomy\Model\Taxon as BaseTaxon;
use Sylius\Component\Taxonomy\Model\TaxonInterface;

use Vankosoft\ApplicationBundle\Model\Interfaces\TaxonInterface as VsTaxonInterface;
use Vankosoft\CmsBundle\Model\FileInterface;

class Taxon extends BaseTaxon implements VsTaxonInterface, Comparable
{
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
    
    public function compareTo($other): int
    {
        return $this->code === $other->getCode() ? 0 : 1;
    }
}
