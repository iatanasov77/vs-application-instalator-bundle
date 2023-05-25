<?php namespace Vankosoft\ApplicationBundle\EventListener\ResourceAction;

use Sylius\Component\Resource\Model\ResourceInterface;
use Vankosoft\UsersBundle\Model\UserInterface;

/**
 * MANUAL: https://q.agency/blog/custom-events-with-symfony5/
 */
final class ResourceActionEvent
{
    public const NAME   = 'vs_application.resource_action';
    
    /** @var ResourceInterface */
    private $resource;
    
    /** @var UserInterface */
    private $user;
    
    /** @var string */
    private $action;
    
    public function __construct( ResourceInterface $resource, UserInterface $user, string $action )
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