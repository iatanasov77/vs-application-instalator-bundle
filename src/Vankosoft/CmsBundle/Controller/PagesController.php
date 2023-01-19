<?php namespace Vankosoft\CmsBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Collections\ArrayCollection;
use Vankosoft\ApplicationBundle\Controller\AbstractCrudController;

use Vankosoft\CmsBundle\Form\ClonePageForm;
use Vankosoft\CmsBundle\Form\PreviewPageForm;

class PagesController extends AbstractCrudController
{
    protected function customData( Request $request, $entity = null ): array
    {
        $translations   = $this->classInfo['action'] == 'indexAction' ? $this->getTranslations() : [];
        $versions       = $this->classInfo['action'] == 'indexAction' ? $this->getVersions( $translations ) : [];
        
        $taxonomy   = $this->get( 'vs_application.repository.taxonomy' )->findByCode(
                                    $this->getParameter( 'vs_application.page_categories.taxonomy_code' )
                                );
        
        return [
            'categories'    => $this->get( 'vs_cms.repository.page_categories' )->findAll(),
            'taxonomyId'    => $taxonomy ? $taxonomy->getId() : 0,
            'translations'  => $translations,
            'versions'      => $versions,
            
            'formClone'     => $this->createForm( ClonePageForm::class )->createView(),
            'formPreview'   => $this->createForm( PreviewPageForm::class )->createView(),
        ];
    }
    
    protected function prepareEntity( &$entity, &$form, Request $request )
    {
        $categories = new ArrayCollection();
        $pcr        = $this->get( 'vs_cms.repository.page_categories' );
        
        $formLocale = $request->request->get( 'locale' );
        $formTaxon  = $request->request->get( 'category_taxon' );
        
        if ( $formLocale ) {
            $entity->setTranslatableLocale( $formLocale );
        }
        
        if ( $formTaxon ) {
            foreach ( $formTaxon as $taxonId ) {
                $category       = $pcr->findOneBy( ['taxon' => $taxonId] );
                if ( $category ) {
                    $categories[]   = $category;
                    $entity->addCategory( $category );
                }
            }
            
            foreach ( $entity->getCategories() as $cat ) {
                if ( ! $categories->contains( $cat ) ) {
                    $entity->removeCategory( $cat );
                }
            }
        }
    }
    
    private function getTranslations()
    {
        $translations   = [];
        $transRepo      = $this->get( 'vs_application.repository.translation' );
        
        foreach ( $this->getRepository()->findAll() as $page ) {
            $translations[$page->getId()] = array_keys( $transRepo->findTranslations( $page ) );
        }
        
        return $translations;
    }
    
    private function getVersions( $translations )
    {
        $versions   = [];
        $logRepo    = $this->get( 'vs_application.repository.logentry' );
        
        foreach ( $translations as $pageId => $locales ) {
            foreach ( $locales as $locale ) {
                $currentVersion = $this->getRepository()->getCurrentVersion( $pageId, $locale, $logRepo );
                if ( $currentVersion ) {
                    $versions[$pageId][$locale] = $currentVersion;
                }
            }
        }
        
        return $versions;
    }
}
