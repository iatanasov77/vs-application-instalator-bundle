<?php namespace Vankosoft\UsersBundle\Controller;

use Vankosoft\ApplicationBundle\Controller\AbstractCrudController;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Collections\ArrayCollection;

class UsersController extends AbstractCrudController //ResourceController
{
    protected function prepareEntity( &$entity, &$form, Request $request )
    {
        $userManager    = $this->container->get( 'vs_users.manager.user' );
        
        // Set User Info
        $this->setUserInfo( $entity, $form );
        
        $plainPassword  = $form->get( "plain_password" )->getData();
        if ( $plainPassword ) {
            $userManager->encodePassword( $entity, $plainPassword );
        }
        
        $selectedRoles  = \json_decode( $request->request->get( 'selectedRoles' ), true );
        $this->buildRoles( $entity, $selectedRoles );
        /*
        $roles  = $form->get( "roles_options" )->getData();
        var_dump( $roles ); die;
        $entity->setRoles( $roles );
        */
        
        /*
        $entity->setVerified( true );

        // I dont know yet if these fields should be in the form
        $entity->setPreferedLocale( $request->getLocale() );
        $entity->setEnabled( true );
        */
        
        $allowedApplications    = $form->get( "applications" )->getData();
        $this->clearApplications( $entity );
        $entity->setApplications( $allowedApplications );
    }
    
    private function buildRoles( &$entity, array $roles )
    {
        //var_dump( $roles ); die;
        $repo   = $this->get( 'vs_users.repository.user_roles' );
        
        $entity->setRolesCollection( new ArrayCollection() );
        foreach ( $roles as $r ) {
            $entity->addRole( $repo->find( $r['id'] ) );
        }
    }
    
    /**
     * Used before setApplications method to fix when removing an application
     * MANUAL: https://stackoverflow.com/questions/38955114/symfony-doctrine-remove-manytomany-association/38955917
     */
    private function clearApplications( &$entity )
    {
        $userApps   = $entity->getApplications();
        $appRepo    = $this->get( 'vs_application.repository.application' );
        
        foreach ( $appRepo->findAll() as $app ) {
            if ( ! $userApps->contains( $app ) ) {
                $app->removeUser( $entity );
            }
        }
        
        return $this;
    }
    
    private function setUserInfo( &$entity, $form )
    {
        $userManager    = $this->container->get( 'vs_users.manager.user' );
        $userInfo       = $entity->getInfo();
        if ( ! $userInfo ) {
            $userInfo   = $this->container->get( 'vs_users.factory.user_info' )->createNew();
            $entity->setInfo( $userInfo );
        }
        
        /*
        $profilePictureFile = $form->get( 'profilePicture' )->getData();
        if ( $profilePictureFile ) {
            $userManager->createAvatar( $userInfo, $profilePictureFile );
        }
        */
    }
}