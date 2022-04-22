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

use Twig\Compiler;
use Twig\Node\IncludeNode;
use Twig\Node\Node;
use Twig\Node\NodeOutputInterface;
use Twig\Source;
use Twig_Node_Include;
use Twig_NodeOutputInterface;

class Twig_Node_Include_Decorator extends Node implements NodeOutputInterface
{
    /**
     * @var IncludeNode
     */
    private $original;

    /**
     * @param IncludeNode $original
     */
    public function __construct(IncludeNode $original)
    {
        $this->original = $original;
    }

    /**
     * @return void
     */
    public function compile(Compiler $compiler)
    {
        $snippet = $this->original->getNode('expr')->hasAttribute('value') ? $snippet = $this->original->getNode('expr')->getAttribute('value') : null;
        if ($snippet) {
            $compiler->write('echo "\n\n<!-- START INCLUDE '.$snippet.' -->\n\n";');
        }
        $this->original->compile($compiler);
        if ($snippet) {
            $compiler->write('echo "\n\n<!-- END INCLUDE '.$snippet.' -->\n\n";');
        }
    }

    public function getIterator(): \Traversable
    {
        return $this->original->getIterator();
    }

    public function getNodeTag(): string
    {
        return (string) $this->original->getNodeTag();
    }

    public function count()
    {
        return $this->original->count();
    }

    public function getTemplateLine(): int
    {
        return $this->original->getTemplateLine();
    }

    /**
     * @psalm-suppress UndefinedMethod
     * @FIXME `setTemplateName` does not exist on the original class. This method should probably be removed.
     *
     * @param string $name
     * @return void
     */
    public function setTemplateName($name)
    {
        $this->original->setTemplateName($name);
    }

    public function getTemplateName(): string
    {
        return (string) $this->original->getTemplateName();
    }

    public function setSourceContext(Source $source): void
    {
        $this->original->setSourceContext($source);
    }

    public function getSourceContext(): ?Source
    {
        return $this->original->getSourceContext();
    }
}
