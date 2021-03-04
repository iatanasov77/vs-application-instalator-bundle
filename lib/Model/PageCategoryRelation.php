<?php namespace VS\CmsBundle\Model;

class PageCategoryRelation implements PageCategoryRelationInterface
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
