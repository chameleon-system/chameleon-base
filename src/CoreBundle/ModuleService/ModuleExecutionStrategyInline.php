<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

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
