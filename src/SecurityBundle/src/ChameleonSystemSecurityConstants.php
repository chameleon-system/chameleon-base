<?php

namespace ChameleonSystem\SecurityBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

class ChameleonSystemSecurityConstants extends Bundle
{
    public const FIREWALL_BACKEND_NAME = 'backend';
    public const GOOGLE_RETURN_ROUTE_NAME = 'connect_google_check';
}
