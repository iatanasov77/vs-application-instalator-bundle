<?php namespace Vankosoft\ApplicationBundle\EventSubscriber;

use Sylius\Component\Resource\Model\ResourceInterface;
use Vankosoft\UsersBundle\Model\Interfaces\UserInterface;

/**
 * MANUAL: https://q.agency/blog/custom-events-with-symfony5/
 */
final class ResourceActionEvent
{
    public const NAME   = 'vs_application.resource_action';
    
    /** @var string */
    private $resource;
    
    /** @var UserInterface|null */
    private $user;
    
    /** @var string */
    private $action;
    
    public function __construct( $resource, $user, $action )
    {
        $this->resource = $resource;
        $this->user     = $user;
        $this->action   = $action;
    }
    
    public function getResource()
    {
        return $this->resource;
    }
    
    public function getUser()
    {
        return $this->user;
    }
    
    public function getAction()
    {
        return $this->action;
    }
}