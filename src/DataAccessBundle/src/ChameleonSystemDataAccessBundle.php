<?php

namespace ChameleonSystem\DataAccessBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

class ChameleonSystemDataAccessBundle extends Bundle
{
    public function getPath(): string
    {
        return \dirname(__DIR__);
    }
}
