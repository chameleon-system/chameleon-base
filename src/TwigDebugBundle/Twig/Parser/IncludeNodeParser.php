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

class IncludeNodeParser extends \Twig_TokenParser_Include
{
    /**
     * {@inheritdoc}
     * @psalm-suppress MethodSignatureMismatch - `Twig_Token` is a subtype of `Token`
     */
    public function parse(\Twig_Token $token)
    {
        $node = parent::parse($token);

        return new Twig_Node_Include_Decorator($node);
    }
}
