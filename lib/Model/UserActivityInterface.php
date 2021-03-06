<?php namespace VS\UsersBundle\Model;

interface UserActivityInterface
{
    /** @return integer */
    public function getId();
    
    /** @return string */
    public function getUser();
    
    /** @return \DateTimeInterface */
    public function getDate();
    
    /** @return string */
    public function getActivity();
}
