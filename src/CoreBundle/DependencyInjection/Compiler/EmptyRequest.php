<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ChameleonSystem\CoreBundle\DependencyInjection\Compiler;

use Symfony\Component\HttpFoundation\Request;

class EmptyRequest extends Request
{
    public function getLanguage()
    {
        return null;
    }

    public function getActivePage()
    {
        return null;
    }
}
