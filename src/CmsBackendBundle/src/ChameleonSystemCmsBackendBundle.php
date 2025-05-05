<?php

namespace ChameleonSystem\CmsBackendBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

class ChameleonSystemCmsBackendBundle extends Bundle
{
    public function getPath(): string
    {
        return \dirname(__DIR__);
    }
}
