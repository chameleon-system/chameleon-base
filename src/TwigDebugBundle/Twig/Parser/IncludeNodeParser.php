<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ChameleonSystem\TwigDebugBundle\Twig\Parser;

use ChameleonSystem\TwigDebugBundle\Twig\Node\Twig_Node_Include_Decorator;
use Twig_Error_Syntax;
use Twig_NodeInterface;
use Twig_Token;

class IncludeNodeParser extends \Twig_TokenParser_Include
{
    /**
     * Parses a token and returns a node.
     *
     * @param Twig_Token $token A Twig_Token instance
     *
     * @return Twig_NodeInterface A Twig_NodeInterface instance
     *
     * @throws Twig_Error_Syntax
     */
    public function parse(Twig_Token $token)
    {
        $node = parent::parse($token);

        return new Twig_Node_Include_Decorator($node);
    }
}
