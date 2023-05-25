<?php namespace Vankosoft\ApplicationBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use FOS\RestBundle\View\View;
use Sylius\Bundle\ResourceBundle\Controller\ResourceController;
use Sylius\Component\Resource\ResourceActions;
use Pagerfanta\Pagerfanta;

use Doctrine\DBAL\DBALException;
use Doctrine\ORM\ORMException;

use Vankosoft\ApplicationBundle\Component\Status;
use Vankosoft\ApplicationBundle\EventSubscriber\ResourceActionEvent;

class AbstractCrudController extends ResourceController
{
    protected $classInfo;
    
    protected $currentRequest;
    
    /** @var Pagerfanta $resources */
    protected $resources;
    
    public function showAction( Request $request ): Response
    {
        $this->currentRequest = $request;
        $this->classInfo( $request );   // call this for every controller action
        
        $configuration  = $this->requestConfigurationFactory->create( $this->metadata, $request );
        
        $this->isGrantedOr403( $configuration, ResourceActions::SHOW );
        $resource       = $this->findOr404( $configuration );
        
        $event          = $this->eventDispatcher->dispatch( ResourceActions::SHOW, $configuration, $resource );
        $eventResponse  = $event->getResponse();
        if ( null !== $eventResponse ) {
            return $eventResponse;
        }
        
        if ( $configuration->isHtmlRequest() ) {
            return $this->render( $configuration->getTemplate( ResourceActions::SHOW . '.html' ),
                array_merge(
                    [
                        'configuration'             => $configuration,
                        'metadata'                  => $this->metadata,
                        'resource'                  => $resource,
                        $this->metadata->getName()  => $resource,
                    ],
                    $this->customData( $request )
                )
            );
        }
        
        return $this->createRestView( $configuration, $resource );
    }
    
    public function indexAction( Request $request ): Response
    {
        $this->currentRequest = $request;
        $this->classInfo( $request );   // call this for every controller action
        
        $configuration = $this->requestConfigurationFactory->create( $this->metadata, $request );
        
        $this->isGrantedOr403( $configuration, ResourceActions::INDEX );
        $this->resources    = $this->resourcesCollectionProvider->get( $configuration, $this->repository );
        if (
            $this->metadata->getParameters()['classes']['interface'] == 'Vankosoft\ApplicationBundle\Model\Interfaces\TaxonRelationInterface'
        ) {
            foreach ( $this->resources as $r ) {
                $r->setCurrentLocale( $request->getLocale() );
            }
        }
        
        $this->eventDispatcher->dispatchMultiple( ResourceActions::INDEX, $configuration, $this->resources );
        
        if ( $configuration->isHtmlRequest() ) {
            return $this->render( $configuration->getTemplate( ResourceActions::INDEX . '.html' ), 
                array_merge(
                    [
                        'configuration'                     => $configuration,
                        'metadata'                          => $this->metadata,
                        'resources'                         => $this->resources,
                        $this->metadata->getPluralName()    => $this->resources,
                        
                        /** @TODO Make Admin Panel To Use Paginated Rsources and Remove This */
                        'items'                             => $this->getRepository()->findAll(),
                    ],
                    $this->customData( $request )
                )
            );
        }
        
        return $this->createRestView( $configuration, $this->resources );
    }
    
    public function createAction( Request $request ): Response
    {
        return $this->editAction( 0, ResourceActions::CREATE, $request );
    }
    
    public function updateAction( Request $request ): Response
    {
        return $this->editAction( $request->attributes->get( 'id' ), ResourceActions::UPDATE, $request );
    }
    
    public function editAction( $id, $resourceAction , Request $request ): Response
    {
        $this->classInfo( $request );   // call this for every controller action
        
        $configuration  = $this->requestConfigurationFactory->create( $this->metadata, $request );
        
        $er             = $this->getRepository();
        $entity         = $id ? $er->findOneBy( ['id' => $id] ) : $this->getFactory()->createNew();
        $form           = $this->resourceFormFactory->create( $configuration, $entity );
        
        if ( in_array( $request->getMethod(), ['POST', 'PUT', 'PATCH'], true ) && $form->handleRequest( $request) ) { // ->isValid()
            $em     = $this->getDoctrine()->getManager();
            $entity = $form->getData();
            
            // middleware method
            $this->prepareEntity( $entity, $form, $request );
            
            $em->persist( $entity );
            $em->flush();
            
            $currentUser    = $this->get( 'vs_users.security_bridge' )->getUser();
            // Using Symfony Event Dispatcher ( NOT \Sylius\Bundle\ResourceBundle\Controller\EventDispatcher )
            $this->get( 'event_dispatcher' )->dispatch(
                new ResourceActionEvent( $entity, $currentUser, $resourceAction ),
                ResourceActionEvent::NAME
            );
            
            if( $request->isXmlHttpRequest() ) {
                return new JsonResponse([
                    'status'   => Status::STATUS_OK
                ]);
            } else {
                $routesPrefix   = $this->classInfo['bundle'] . '_' . $this->classInfo['controller'];
                if ( $form->getClickedButton() && 'btnApply' === $form->getClickedButton()->getName() ) {
                    return $this->redirect( $this->generateUrl( $routesPrefix . '_update', ['id' => $entity->getId()] ) );
                } else {
                    return $this->redirect( $this->generateUrl( $routesPrefix . '_index' ) );
                }
            }
        }
        
        if ($configuration->isHtmlRequest()) {
            return $this->render( $configuration->getTemplate( $resourceAction . '.html' ), array_merge( [
                'item' => $entity,
                'form' => $form->createView(),
            ], $this->customData( $request, $entity ) ) );
        }
        
        return $this->createRestView( $configuration, $entity );
    }
    
    public function deleteAction( Request $request ): Response
    {
        try {
            $response = parent::deleteAction( $request );
            
            $redirectUrl    = $request->request->get( 'redirectUrl' );
            if ( $redirectUrl ) {
                return $this->redirect( $redirectUrl );
            }
        } catch ( DBALException $e ) {
            if ( ! $this->getParameter( 'vs_application.supress_pdo_exception' ) ) {
                throw new \Vankosoft\ApplicationBundle\Component\Exception\PDOException( 'VS Application DBAL Exception. You can supress it by setting the parameter: vs_application.supress_pdo_exception', 500, $e );
            }
        } catch ( \PDOException $e ) {
            if ( ! $this->getParameter( 'vs_application.supress_pdo_exception' ) ) {
                throw new \Vankosoft\ApplicationBundle\Component\Exception\PDOException( 'VS Application PDO Exception. You can supress it by setting the parameter: vs_application.supress_pdo_exception', 500, $e );
            }
        } catch ( ORMException $e ) {
            if ( ! $this->getParameter( 'vs_application.supress_pdo_exception' ) ) {
                throw new \Vankosoft\ApplicationBundle\Component\Exception\PDOException( 'VS Application ORM Exception. You can supress it by setting the parameter: vs_application.supress_pdo_exception', 500, $e );
            }
        } catch ( \Exception $e ) {
            if ( ! $this->getParameter( 'vs_application.supress_pdo_exception' ) ) {
                throw new \Vankosoft\ApplicationBundle\Component\Exception\PDOException( 'VS Application PHP Exception. You can supress it by setting the parameter: vs_application.supress_pdo_exception', 500, $e );
            }
        }
        
        return $response;
    }
        
    protected function classInfo( Request $request )
    {
        if ( ! $this->classInfo ) {
            // when write this code request return: vs_users.controller.users:indexAction
            $info           = explode( '.', $request->attributes->get( '_controller' ) );
            $controllerInfo = explode( '::', $info[2] );
            
            $this->classInfo    = [
                'bundle'        => $info[0],
                'controller'    => $controllerInfo[0],
                'action'        => $controllerInfo[1],
            ];
        }
    }
    
    protected function prepareEntity( &$entity, &$form, Request $request )
    {
        
    }
    
    protected function customData( Request $request, $entity = null ): array
    {
        return [];
    }
    
    protected function getRepository()
    {
        return $this->get( $this->classInfo['bundle'] . '.repository.' . $this->classInfo['controller'] );
    }
    
    protected function getFactory()
    {
        return $this->get( $this->classInfo['bundle'] . '.factory.' . $this->classInfo['controller'] );
    }
}
