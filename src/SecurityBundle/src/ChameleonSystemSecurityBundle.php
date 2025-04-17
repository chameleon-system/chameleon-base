<?php

namespace ChameleonSystem\SecurityBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

class ChameleonSystemSecurityBundle extends Bundle
{
    public function getPath(): string
    {
        return \dirname(__DIR__);
    }
}
