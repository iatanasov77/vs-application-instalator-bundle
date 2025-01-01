<?php namespace Vankosoft\ApplicationBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Form\FormInterface;
use FOS\RestBundle\View\View;
use Sylius\Bundle\ResourceBundle\Controller\ResourceController;
use Sylius\Component\Resource\ResourceActions;
use Pagerfanta\Pagerfanta;

use Doctrine\DBAL\Driver\PDO\PDOException;
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
                    $this->customData( $request, $resource )
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
        
        $metadataParameters = $this->metadata->getParameters();
        if (
            isset( $metadataParameters['classes']['interface'] ) &&
            $metadataParameters['classes']['interface'] == 'Vankosoft\ApplicationBundle\Model\Interfaces\TaxonRelationInterface'
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
                        
                        /**
                         * @TODO Make Admin Panel To Use Paginated Rsources and Remove This
                         *
                         * In Category Resources that Create Simpla Tree Table in Index Pages 
                         * Add This Parameter in customData Method
                         */
                        //'items'                             => $this->getRepository()->findAll(),
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
        
        if ( in_array( $request->getMethod(), ['POST', 'PUT', 'PATCH'], true ) && $form->handleRequest( $request )->isValid() ) { // ->isValid()
            $em     = $this->getDoctrine()->getManager();
            $entity = $form->getData();
            
            $preEvent = $this->eventDispatcher->dispatchPreEvent( $resourceAction, $configuration, $entity );
            
            // middleware method
            $this->prepareEntity( $entity, $form, $request );
            
            $em->persist( $entity );
            $em->flush();
            
            // Dispach a Sylius Resource Post Event
            $postEvent = $this->eventDispatcher->dispatchPostEvent( $resourceAction, $configuration, $entity );
            
            /**
             * Using Symfony Event Dispatcher ( NOT \Sylius\Bundle\ResourceBundle\Controller\EventDispatcher )
             * Used for 'addUserActivity' Event
             */
            $currentUser    = $this->get( 'vs_users.security_bridge' )->getUser();
            $this->get( 'event_dispatcher' )->dispatch(
                new ResourceActionEvent( $this->metadata->getAlias(), $currentUser, $resourceAction ),
                ResourceActionEvent::NAME
            );
            
            // middleware method to add Custom Events After Save ETC.
            $this->afterSaveEntity( $entity, $request );
            
            if( $request->isXmlHttpRequest() ) {
                return new JsonResponse([
                    'status'   => Status::STATUS_OK
                ]);
            } else {
                $routesPrefix   = $this->classInfo['bundle'] . '_' . $this->classInfo['controller'];
                $routeParams    = $this->getRouteParams( $request );
                
                if ( $form->getClickedButton() && 'btnApply' === $form->getClickedButton()->getName() ) {
                    $routeParams    = \array_merge( ['id' => $entity->getId()], $routeParams );
                    return $this->redirect( $this->generateUrl( $routesPrefix . '_update', $routeParams ) );
                } else {
                    return $this->redirect( $this->generateUrl( $routesPrefix . '_index', $routeParams ) );
                }
            }
        }
        
        if ( $configuration->isHtmlRequest() ) {
            $formErrors = $this->getErrorsFromForm( $form );
            if ( ! empty( $formErrors ) ) {
                //echo '<pre>'; var_dump( $formErrors ); die;
            }
            
            return $this->render( $configuration->getTemplate( $resourceAction . '.html' ), array_merge( [
                'metadata'      => $this->metadata,
                'item'          => $entity,
                'form'          => $form->createView(),
                'formErrors'    => $formErrors,
            ], $this->customData( $request, $entity ) ) );
        }
        
        return $this->createRestView( $configuration, $entity );
    }
    
    public function deleteAction( Request $request ): Response
    {
        try {
            $response = parent::deleteAction( $request );
            
            $currentUser    = $this->get( 'vs_users.security_bridge' )->getUser();
            // Using Symfony Event Dispatcher ( NOT \Sylius\Bundle\ResourceBundle\Controller\EventDispatcher )
            $this->get( 'event_dispatcher' )->dispatch(
                new ResourceActionEvent( $this->metadata->getAlias(), $currentUser, ResourceActions::DELETE ),
                ResourceActionEvent::NAME
            );
            
            $redirectUrl    = $request->request->get( 'redirectUrl' );
            if ( $redirectUrl ) {
                return $this->redirect( $redirectUrl );
            }
        } catch ( DBALException $e ) {
            if ( ! $this->getParameter( 'vs_application.supress_pdo_exception' ) ) {
                throw new \Vankosoft\ApplicationBundle\Component\Exception\PDOException( 'VS Application DBAL Exception. You can supress it by setting the parameter: vs_application.supress_pdo_exception', 500, $e );
            }
        } catch ( PDOException $e ) { // Doctrine DBAL PDOException
            if ( ! $this->getParameter( 'vs_application.supress_pdo_exception' ) ) {
                throw new \Vankosoft\ApplicationBundle\Component\Exception\PDOException( 'VS Application DBAL PDO Exception. You can supress it by setting the parameter: vs_application.supress_pdo_exception', 500, $e );
            }
        } catch ( ORMException $e ) {
            if ( ! $this->getParameter( 'vs_application.supress_pdo_exception' ) ) {
                throw new \Vankosoft\ApplicationBundle\Component\Exception\PDOException( 'VS Application ORM Exception. You can supress it by setting the parameter: vs_application.supress_pdo_exception', 500, $e );
            }
        } catch ( \PDOException $e ) {
            if ( ! $this->getParameter( 'vs_application.supress_pdo_exception' ) ) {
                throw new \Vankosoft\ApplicationBundle\Component\Exception\PDOException( 'VS Application PDO Exception. You can supress it by setting the parameter: vs_application.supress_pdo_exception', 500, $e );
            }
        } catch ( \Exception $e ) {
            if ( ! $this->getParameter( 'vs_application.supress_pdo_exception' ) ) {
                throw new \Vankosoft\ApplicationBundle\Component\Exception\PDOException( 'VS Application PHP Exception. You can supress it by setting the parameter: vs_application.supress_pdo_exception', 500, $e );
            }
        }
        
        //var_dump( $this->getParameter( 'vs_application.supress_pdo_exception' ) ); die;
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
    
    protected function afterSaveEntity( $entity, Request $request )
    {
        
    }
    
    protected function getRepository()
    {
        return $this->get( $this->classInfo['bundle'] . '.repository.' . $this->classInfo['controller'] );
    }
    
    protected function getFactory()
    {
        return $this->get( $this->classInfo['bundle'] . '.factory.' . $this->classInfo['controller'] );
    }
    
    protected function getErrorsFromForm( FormInterface $form, bool $child = false ): array
    {
        $errors = [];
        
        foreach ( $form->getErrors() as $error ) {
            if ( $child ) {
                $errors[] = $error->getMessage();
            } else {
                $errors[$error->getOrigin()->getName()][] = $error->getMessage();
            }
        }
        
        foreach ( $form->all() as $childForm ) {
            if ( $childForm instanceof FormInterface ) {
                if ( $childErrors = $this->getErrorsFromForm( $childForm, true ) ) {
                    $errors[$childForm->getName()] = $childErrors;
                }
            }
        }
        
        return $errors;
    }
    
    protected function getRouteParams( Request $request ): array
    {
        $routeParams = $request->attributes->get('_route_params');
        if ( isset( $routeParams['_sylius'] ) ) {
            unset( $routeParams['_sylius'] );
        }
        //var_dump($routeParams);die;
        
        return $routeParams;
    }
}
