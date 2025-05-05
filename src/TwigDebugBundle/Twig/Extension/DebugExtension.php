<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ChameleonSystem\TwigDebugBundle\Twig\Extension;

use ChameleonSystem\TwigDebugBundle\Twig\Parser\IncludeNodeParser;
use Twig\Extension\AbstractExtension;

class DebugExtension extends AbstractExtension
{
    /**
     * Returns the name of the extension.
     *
     * @return string The extension name
     */
    public function getName()
    {
        return 'chameleon_twig_debug_extension';
    }

    public function getTokenParsers()
    {
        return [
            new IncludeNodeParser(),
        ];
    }
}
