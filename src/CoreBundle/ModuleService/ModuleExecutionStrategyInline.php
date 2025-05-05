<?php

namespace ChameleonSystem\CoreBundle\ModuleService;

use Symfony\Component\HttpFoundation\Request;

class ModuleExecutionStrategyInline implements ModuleExecutionStrategyInterface
{
    /**
     * {@inheritdoc}
     */
    public function execute(Request $request, \TModelBase $module, $spotName, $isLegacyModule)
    {
        return $module->__invoke($request, $isLegacyModule);
    }
}
