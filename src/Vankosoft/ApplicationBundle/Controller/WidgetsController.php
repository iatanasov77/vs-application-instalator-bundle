<?php namespace Vankosoft\ApplicationBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Vankosoft\ApplicationBundle\Controller\AbstractCrudController;

class WidgetsController extends AbstractCrudController
{
    protected function customData( Request $request, $entity = null ): array
    {
        return [
            
        ];
    }
    
    protected function prepareEntity( &$entity, &$form, Request $request )
    {
        $widgetName = $form->get( 'name' )->getData();
        
        $entity->setCode( $this->get( 'vs_application.slug_generator' )->generate( $widgetName ) );
    }
}