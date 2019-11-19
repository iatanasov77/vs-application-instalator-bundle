<?php

namespace IA\UsersBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

class IAUsersBundle extends Bundle
{
    public function getParent()
    {
        return 'FOSUserBundle';
    }
}
