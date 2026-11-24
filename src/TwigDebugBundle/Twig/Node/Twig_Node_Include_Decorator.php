<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ChameleonSystem\TwigDebugBundle\Twig\Node;

use Twig\Attribute\YieldReady;
use Twig\Compiler;
use Twig\Node\IncludeNode;
use Twig\Node\Node;
use Twig\Node\NodeOutputInterface;

#[YieldReady]
class Twig_Node_Include_Decorator extends Node implements NodeOutputInterface
{
    public function __construct(IncludeNode $original)
    {
        parent::__construct(['include' => $original], [], $original->getTemplateLine());
    }

    public function compile(Compiler $compiler): void
    {
        $include = $this->getNode('include');
        $snippet = null;
        if ($include instanceof IncludeNode) {
            $expression = $include->getNode('expr');
            if ($expression->hasAttribute('value')) {
                $snippet = $expression->getAttribute('value');
            }
        }

        if (\is_string($snippet) && $snippet) {
            $compiler->write('yield ')->string("\n\n<!-- START INCLUDE ".$snippet." -->\n\n")->raw(";\n");
        }

        $compiler->subcompile($include);

        if (\is_string($snippet) && $snippet) {
            $compiler->write('yield ')->string("\n\n<!-- END INCLUDE ".$snippet." -->\n\n")->raw(";\n");
        }
    }
}
