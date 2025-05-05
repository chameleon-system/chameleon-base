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
use Twig\Node\Node;
use Twig\Token;
use Twig\TokenParser\IncludeTokenParser;

class IncludeNodeParser extends IncludeTokenParser
{
    /**
     * {@inheritdoc}
     *
     * @psalm-suppress MethodSignatureMismatch - `Twig_Token` is a subtype of `Token`
     */
    public function parse(Token $token): Node
    {
        $node = parent::parse($token);

        return new Twig_Node_Include_Decorator($node);
    }
}
