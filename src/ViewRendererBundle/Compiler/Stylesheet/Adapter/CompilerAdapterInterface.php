<?php

namespace ChameleonSystem\ViewRendererBundle\Compiler\Stylesheet\Adapter;

use ChameleonSystem\ViewRendererBundle\Compiler\Stylesheet\CompilerInterface;

interface CompilerAdapterInterface extends CompilerInterface
{
    public function getType(): string;
}
