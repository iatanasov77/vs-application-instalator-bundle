<?php namespace Vankosoft\ApplicationBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Vankosoft\ApplicationBundle\Controller\AbstractCrudController;

class LocalesController extends AbstractCrudController
{
    protected function customData( Request $request, $entity = null ): array
    {
        return [
            'translations'  => $this->classInfo['action'] == 'indexAction' ? $this->getTranslations() : [],
            'defaultLocale' => $this->getParameter( 'locale' ),
        ];
    }
    
    protected function prepareEntity( &$entity, &$form, Request $request )
    {
        $formPost   = $request->request->get( 'locale_form' );
        
        if ( isset( $formPost['translatableLocale'] ) ) {
            $entity->setTranslatableLocale( $formPost['translatableLocale'] );
        }
    }
    
    private function getTranslations(): array
    {
        $translations   = [];
        $transRepo      = $this->get( 'vs_application.repository.translation' );
        /* THIS MAKE FATAL ERROR IN PRODUCTION ( I DONT KNOW WHY )
         * ========================================================
        foreach ( $this->getRepository()->findAll() as $locale ) {
            $translations[$locale->getId()] = array_keys( $transRepo->findTranslations( $locale ) );
        }
        */
        
        return $translations;
    }
}
