<?php namespace Vankosoft\ApplicationBundle\Model\Interfaces;

use Sylius\Component\Resource\Model\ResourceInterface;
use Vankosoft\UsersBundle\Model\Interfaces\UserInterface;

interface UserAwareInterface extends ResourceInterface
{
    public function getCreatedBy() : ?UserInterface;
    public function getUpdatedBy() : ?UserInterface;
    public function getDeletedBy() : ?UserInterface;
}
