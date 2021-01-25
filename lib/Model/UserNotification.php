<?php namespace VS\UsersBundle\Model;

class UserNotification
{

    /**
     * @var mixed
     */
    protected $id;

    /**
     * Relation to the User entity
     *
     * @var mixed
     */
    protected $user;

    /**
     * @var string
     */
    protected $notification;

    /**
     * @var \DateTime|null
     */
    protected $date;

    function getId()
    {
        return $this->id;
    }

    function getUser()
    {
        return $this->user;
    }

    function getDate()
    {
        return $this->date;
    }

    function setId($id)
    {
        $this->id = $id;
        
        return $this;
    }

    function setUser($user)
    {
        $this->user = $user;
        
        return $this;
    }

    function setDate($date)
    {
        $this->date = $date;
        
        return $this;
    }
    
    function setNotification($notification)
    {
        $this->notification = $notification;
        
        return $this;
    }

    function getNotification()
    {
        return $this->notification;
    }
}
