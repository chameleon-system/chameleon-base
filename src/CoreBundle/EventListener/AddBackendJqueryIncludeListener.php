<?php
/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ChameleonSystem\CoreBundle\EventListener;

use ChameleonSystem\CoreBundle\Event\HtmlIncludeEventInterface;

class AddBackendJqueryIncludeListener
{
    public function onGlobalHtmlHeaderInclude(HtmlIncludeEventInterface $event): void
    {
        if (true === \TGlobal::IsCMSMode()) {
            $jqueryInclude = '<script src="'.\TGlobal::GetStaticURL('/chameleon/blackbox/javascript/jquery/jquery-3.7.1.min.js').'" type="text/javascript"></script>';
            $event->addData([$jqueryInclude]);
        }
    }
}
