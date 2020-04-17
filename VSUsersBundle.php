<?php namespace VS\UsersBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

class VSUsersBundle extends Bundle
{
    public function getParent()
    {
        return 'FOSUserBundle';
    }
}
