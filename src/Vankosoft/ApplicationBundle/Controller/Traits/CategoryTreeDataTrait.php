<?php namespace Vankosoft\ApplicationBundle\Controller\Traits;

use Doctrine\Common\Collections\Collection;

trait CategoryTreeDataTrait
{
    /**
     * Used in Categories to get tree for EasyUiComboTree for Parent Form Field
     * 
     * @param array $tree
     * @param array $data
     * @param array $selectedValues
     */
    protected function buildEasyuiCombotreeDataFromCollection( array $tree, array &$data, array $selectedValues ): void
    {
        $key    = 0;
        
        if ( is_array( $tree ) ) {
            foreach( $tree as $nodeKey => $node ) {
                $data[$key]   = [
                    'id'        => $node['id'],
                    'text'      => $node['name'],
                    'children'  => []
                ];
                if ( in_array( $nodeKey, $selectedValues ) ) {
                    $data[$key]['checked'] = true;
                }
                
                if ( ! empty( $node['children'] ) ) {
                    $this->buildEasyuiCombotreeDataFromCollection( $node['children'], $data[$key]['children'], $selectedValues );
                }
                
                $key++;
            }
        }
    }
    
    /**
     * Used in Categories to get tree for EasyUiComboTree for Parent Form Field
     * 
     * @param Collection $items
     * @param array $itemsTree
     */
    protected function getItemsTree( Collection $items, array &$itemsTree ): void
    {
        foreach ( $items as $item ) {
            $itemsTree[$item->getName()] = [
                'id'        => $item->getId(),
                'name'      => $item->getName(),
                'children'  => [],
            ];
            
            if ( ! $item->getChildren()->isEmpty() ) {
                $this->getItemsTree( $item->getChildren(), $itemsTree[$item->getName()]['children'] );
            }
        }
    }
}