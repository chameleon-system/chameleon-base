<?php

namespace ChameleonSystem\SecurityBundle;

use ChameleonSystem\SecurityBundle\DependencyInjection\ChamleeonSystemSecurityTwoFactorCompilerPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class ChameleonSystemSecurityBundle extends Bundle
{
    public function getPath(): string
    {
        return \dirname(__DIR__);
    }
}