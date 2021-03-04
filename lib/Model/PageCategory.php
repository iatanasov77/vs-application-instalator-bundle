<?php namespace VS\CmsBundle\Model;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

use VS\ApplicationBundle\Model\Interfaces\TaxonInterface;
use VS\ApplicationBundle\Model\Taxon;

/**
 * Page Category Model
 */
class PageCategory implements PageCategoryInterface
{
    /** @var mixed */
    protected $id;
    
    /** @var Collection|PageCategoryRelation[] */
    protected $relations;
    
    /** @var TaxonInterface */
    protected $taxon;
    
    /**
     * @var string
     *          
     * Non-mapped field used for creation of new taxon
     */
    protected $currentLocale;
    
    public function __construct()
    {
        $this->relations    = new ArrayCollection();
    }
    
    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return $this->id;
    }
    
    /**
     * @return Collection|PageCategoryRelation[]
     */
    public function getRelations(): Collection
    {
        return $this->relations;
    }
    
    /**
     * @return Collection|PageCategory[]
     */
    public function getPages()
    {
        $pages = [];
        foreach( $this->getRelations() as $relation ){
            if( ! isset( $pages[$relation->getPage()->getId()] ) ) {
                $pages[$relation->getPage()->getId()]    = $relation->getPage(); //Ensure uniqueness
            }
        }
        
        return $pages;
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

    public function getName()
    {
        return $this->taxon ? $this->taxon->getName() : '';
    }
    
    public function setName( string $name ) : self
    {
        if ( ! $this->taxon ) {
            $this->taxon    = new Taxon();
            $this->taxon->setCurrentLocale( $this->currentLocale );
        }
        $this->taxon->setName( $name );
        
        return $this;
    }
    
    public function setCurrentLocale( string $currentLocale ) : void
    {
        $this->currentLocale = $currentLocale;
    }
    
    public function __toString()
    {
        return $this->taxon ? $this->taxon->getName() : '';
    }
}
