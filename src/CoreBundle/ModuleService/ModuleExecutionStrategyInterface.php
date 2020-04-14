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

use ChameleonSystem\CoreBundle\Exception\ModuleException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use TModelBase;

/**
 * ModuleExecutionStrategyInterface defines a common interface for different strategies on how to load modules (i.e.
 * call TModelBase::__invoke()).
 */
interface ModuleExecutionStrategyInterface
{
    /**
     * Invoke TModelBase::__invoke() using the implemented strategy.
     *
     * @param TModelBase $module
     * @param string     $spotName
     * @param bool       $isLegacyModule
     *
     * @return Response
     *
     * @throws ModuleException
     */
    public function execute(Request $request, TModelBase $module, $spotName, $isLegacyModule);
}
