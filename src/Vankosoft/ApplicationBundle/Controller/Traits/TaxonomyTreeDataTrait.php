<?php namespace Vankosoft\ApplicationBundle\Controller\Traits;

use Vankosoft\ApplicationBundle\Repository\TaxonomyRepository;
use Vankosoft\ApplicationBundle\Repository\TaxonRepository;

trait TaxonomyTreeDataTrait
{
    /** @var TaxonomyRepository */
    protected $taxonomyRepository;
    
    /** @var TaxonRepository */
    protected $taxonRepository;
    
    protected function gtreeTableData( $taxonomyId, $parentId, $displayRootTaxon = false ): array
    {
        $ertt       = $this->getTaxonRepository();
        $ert        = $this->getTaxonomyRepository();
        $rootTaxon  = $ert->find( $taxonomyId )->getRootTaxon();
        
        if ( ! $parentId ) {
            $parentId   = $rootTaxon->getId();
        }
        $taxons         = $ertt->getTaxonsAsArray( $rootTaxon->getId(), $parentId );
        
        $gtreeTableData = $this->buildGtreeTableData( $taxons );
        
        if ( $displayRootTaxon && $parentId == $rootTaxon->getId() ) {
            array_unshift( $gtreeTableData, [
                'id'        => $rootTaxon->getId(),
                'name'      => $rootTaxon->getName(),
                'level'     => 0,
                'type'      => "RootTaxon"
            ]);
        }
        
        return ['nodes' => $gtreeTableData];
    }
    
    protected function easyuiComboTreeData( $taxonomyId, array $selectedValues = [], array $leafs = [], $displayRootTaxon = false ): array
    {
        $rootTaxon      = $this->getTaxonomyRepository()->find( $taxonomyId )->getRootTaxon();
        $data           = [];
        
        if ( $displayRootTaxon ) {
            $data[0]        = [
                'id'        => $rootTaxon->getId(),
                'text'      => $rootTaxon->getName(),
                'children'  => []
            ];
            
            $this->buildEasyuiCombotreeData( $rootTaxon->getChildren(), $data[0]['children'], $selectedValues, $leafs, empty( $leafs ) );
        } else {
            $this->buildEasyuiCombotreeData( $rootTaxon->getChildren(), $data, $selectedValues, $leafs, empty( $leafs) );
        }
        
        return $data;
    }
    
    protected function easyuiComboTreeDataProvideTaxons( array $taxons, array $selectedValues = [], array $leafs = [] ): array
    {
        $data           = [];
        $this->buildEasyuiCombotreeData( $taxons, $data, $selectedValues, $leafs, empty( $leafs) );
        
        return $data;
    }
    
    protected function buildGtreeTableData( $taxons ): array
    {
        $data   = [];
        foreach ( $taxons as $t ) {
            $data[] = [
                'id'        => (int)$t['id'],
                'name'      => $t['name'],
                'level'     => (int)$t['tree_level'],
                'type'      => "default"
            ];
        }
        
        return $data;
    }
    
    protected function buildEasyuiCombotreeData( $tree, &$data, array $selectedValues, array $leafs, $notLeafs ): void
    {
        $key    = 0;
        foreach( $tree as $node ) {
            $data[$key]   = [
                'id'        => $node->getId(),
                'text'      => $node->getName(),
                'children'  => [],
                'disabled'  => ! $notLeafs
            ];
            if ( in_array( $node->getId(), $selectedValues ) ) {
                $data[$key]['checked'] = true;
            }
            
            if ( array_key_exists( $node->getId(), $leafs ) ) {
                $this->buildEasyuiCombotreeData( $leafs[$node->getId()], $data[$key]['children'], $selectedValues, $leafs, false );
            }
            
            // Buld Child Categories After Leafs because Leafs override children keys
            if ( $node->getChildren()->count() ) {
                $this->buildEasyuiCombotreeData( $node->getChildren(), $data[$key]['children'], $selectedValues, $leafs, $notLeafs );
            }
            
            $key++;
        }
    }
    
    protected function bootstrapTreeviewData( $tree, &$data, $useTarget = true, $taxonId = null, array $leafs = [] ): bool
    {
        foreach( $tree as $k => $node ) {
            $node->setCurrentLocale(  $node->getParent()->getCurrentLocale() );
            
            $data[$k]   = [
                'text'  => $node->getTranslation()->getName(),
                'tags'  => ['0']
            ];
            
            if ( $node->getChildren()->count() ) {
                $data[$k]['nodes']  = [];
                $expandParent   = $this->bootstrapTreeviewData( $node->getChildren(), $data[$k]['nodes'], $useTarget, $taxonId, $leafs );
            } else {
                $expandParent   = false;
                foreach ( $leafs as $l => $leaf ) {
                    if ( $leaf->getOwner()->getTaxon()->getId() == $node->getId() ) {
                        if ( ! isset( $data[$k]['nodes'] ) ) {
                            $data[$k]['nodes']  = [];
                        }
                        $data[$k]['nodes'][$l]    = [
                            'text'  => $leaf->getTreeTitle(),
                            'icon'  => 'treeLeaf',
                            'tags'  => [$leaf->getTreeTag()],
                            'href'  => $this->targetUrlLeaf( $leaf->getId() ),
                        ];
                    }
                }
            }
            
            $data[$k]['state']   = [
                'checked'   => false,
                'disabled'  => false,
                'expanded'  => $expandParent,
                'selected'  => $taxonId == $node->getId()
            ];
            
            if ( $useTarget && $this->targetCount( $node->getId() ) ) {
                $data[$k]['href']   = $this->targetUrl( $node->getId() );
            }
        }
        
        $expandParent   = $node ? $taxonId == $node->getId() : false;
        return $expandParent;
    }
    
    protected function targetCount( $taxonId )
    {
        return 0;
    }
    
    protected function targetUrl( $taxonId ): string
    {
        return '';
    }
    
    protected function targetUrlLeaf( $leafId ): string
    {
        return '';
    }
    
    protected function getTaxonomyRepository(): TaxonomyRepository
    {
        return $this->taxonomyRepository;
        //return $this->get( 'vs_application.repository.taxonomy' );
    }
    
    protected function getTaxonRepository(): TaxonRepository
    {
        return $this->taxonRepository;
        //return $this->get( 'vs_application.repository.taxon' );
    }
}
