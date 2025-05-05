<?php

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
     * @param string $spotName
     * @param bool $isLegacyModule
     *
     * @return Response
     *
     * @throws ModuleException
     */
    public function execute(Request $request, \TModelBase $module, $spotName, $isLegacyModule);
}
