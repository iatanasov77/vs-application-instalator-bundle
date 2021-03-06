<?php namespace VS\UsersBundle\Model;

interface UserNotificationInterface
{
    /** @return integer */
    public function getId();
    
    /** @return UserInterface */
    public function getUser();
    
    /** @return \DateTimeInterface */
    public function getDate();
    
    /** @return string */
    public function getNotification();
}
