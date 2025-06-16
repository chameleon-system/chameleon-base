<?php

namespace ChameleonSystem\CmsDashboardBundle\Bridge\Chameleon\Enum;

enum ServerStatusDashboardWidgetCacheTypeEnum: string
{
    case CACHE = 'cache';
    case SESSION = 'session';
}
