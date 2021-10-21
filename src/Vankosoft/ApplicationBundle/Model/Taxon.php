<?php namespace VS\ApplicationBundle\Model;

use Doctrine\Common\Collections\ArrayCollection;
use Sylius\Component\Taxonomy\Model\Taxon as BaseTaxon;
use Sylius\Component\Taxonomy\Model\TaxonInterface;

use VS\ApplicationBundle\Model\Interfaces\TaxonInterface as VsTaxonInterface;

class Taxon extends BaseTaxon implements VsTaxonInterface
{
    protected $taxonomy;
    
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
}
