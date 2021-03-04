<?php namespace VS\CmsBundle\Model;

use Sylius\Component\Resource\Model\ResourceInterface;

class PageCategoryRelation extends PageCategoryRelationInterface
{
    protected $page;
    
    protected $category;
    
    public function getPage() : PageInterface
    {
        return $this->page;
    }
    
    public function getCategory() : PageCategoryInterface
    {
        return $this->category;
    }
}
