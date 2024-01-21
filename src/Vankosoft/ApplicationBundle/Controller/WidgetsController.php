<?php namespace Vankosoft\ApplicationBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Vankosoft\ApplicationBundle\Controller\AbstractCrudController;

class WidgetsController extends AbstractCrudController
{
    protected function customData( Request $request, $entity = null ): array
    {
        return [
            'translations'  => $this->classInfo['action'] == 'indexAction' ? $this->getTranslations() : [],
        ];
    }
    
    protected function prepareEntity( &$entity, &$form, Request $request )
    {
        $widgetName = $form->get( 'name' )->getData();
        
        $entity->setCode( $this->get( 'vs_application.slug_generator' )->generate( $widgetName ) );
    }
    
    private function getTranslations(): array
    {
        $translations   = [];
        $transRepo      = $this->get( 'vs_application.repository.translation' );
        
        /* THIS MAKE FATAL ERROR IN PRODUCTION ( I DONT KNOW WHY )
         * ========================================================
         */
        foreach ( $this->getRepository()->findAll() as $widget ) {
            $translations[$widget->getId()] = array_keys( $transRepo->findTranslations( $widget ) );
        }
        
        
        return $translations;
    }
}