<?php

namespace ChameleonSystem\CoreBundle\ModuleService;

use Symfony\Component\HttpFoundation\Request;
use TModelBase;

class ModuleExecutionStrategyInline implements ModuleExecutionStrategyInterface
{
    /**
     * {@inheritdoc}
     */
    public function execute(Request $request, TModelBase $module, $spotName, $isLegacyModule)
    {
        return $module->__invoke($request, $isLegacyModule);
    }
}
