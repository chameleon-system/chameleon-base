<?php

namespace ChameleonSystem\ViewRendererBundle\Compiler\Stylesheet\Adapter;

use ChameleonSystem\ViewRendererBundle\Compiler\Stylesheet\CompilerInterface;

interface CompilerAdapterInterface extends CompilerInterface
{
    /**
     * @return string
     */
    public function getType();
}
