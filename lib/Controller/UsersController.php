<?php namespace VS\UsersBundle\Controller;

use VS\ApplicationBundle\Controller\AbstractCrudController;
use Symfony\Component\HttpFoundation\Request;

class UsersController extends AbstractCrudController //ResourceController
{
    protected function prepareEntity( &$entity, &$form, Request $request )
    {
        var_dump( $form->get( "roles_options" )->getData() ); die;
    }
}
