<?php namespace VS\ApplicationBundle\Model\Interfaces;

use Sylius\Component\Resource\Model\ResourceInterface;
use VS\UsersBundle\Model\UserInterface;

interface UserAwareInterface extends ResourceInterface
{
    public function getCreatedBy() : ?UserInterface;
    public function getUpdatedBy() : ?UserInterface;
    public function getDeletedBy() : ?UserInterface;
}
