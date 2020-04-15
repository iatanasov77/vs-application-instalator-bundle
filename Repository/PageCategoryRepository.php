<?php namespace IA\CmsBundle\Repository;

use Gedmo\Tree\Entity\Repository\NestedTreeRepository;

class PageCategoryRepository extends NestedTreeRepository
{
    public function countPages( $categoryId )
    {
        $countPages = $this->find( $categoryId )->getPages()->count();
        
        return $countPages;
    }
}
