<?php

namespace IA\CmsBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

class IACmsBundle extends Bundle
{
    public function getContainerExtension()
    {
        return new \IA\CmsBundle\DependencyInjection\IACmsExtension();
    }
}
