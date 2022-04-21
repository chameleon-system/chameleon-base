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
use TGlobal;

class AddJqueryIncludeListener
{
    /**
     * @param HtmlIncludeEventInterface $event
     */
    public function onGlobalHtmlHeaderInclude(HtmlIncludeEventInterface $event)
    {
        if (true === TGlobal::IsCMSMode()) {
            $jqueryInclude = '<script src="'.TGlobal::GetStaticURL('/chameleon/blackbox/javascript/jquery/jquery-3.3.1.min.js').'" type="text/javascript"></script><!--#GLOBALRESOURCECOLLECTION#-->';
            $jqueryInclude .= '<script src="'.TGlobal::GetStaticURL('/chameleon/blackbox/javascript/jquery/jquery-migrate-1.4.1.js').'" type="text/javascript"></script><!--#GLOBALRESOURCECOLLECTION#-->';

            $event->addData([$jqueryInclude]);
        } else {
            if (defined('CHAMELEON_URL_GOOGLE_JQUERY') && CHAMELEON_URL_GOOGLE_JQUERY !== false) {
                $jqueryInclude = '<script src="'.CHAMELEON_URL_GOOGLE_JQUERY.'"></script>';
                $sJQueryLocal = '<script src="'.TGlobal::GetStaticURL(CHAMELEON_URL_JQUERY).'" type="text/javascript"></script>';
                $jqueryInclude .= "\n<script>window.jQuery || document.write('".str_replace('/', '\\/', $sJQueryLocal)."')</script>";

                $event->addData([$jqueryInclude]);
            } else if (CHAMELEON_URL_JQUERY !== false) {
                $jqueryInclude = '<script src="'.TGlobal::GetStaticURL(CHAMELEON_URL_JQUERY).'" type="text/javascript"></script><!--#GLOBALRESOURCECOLLECTION#-->';

                $event->addData([$jqueryInclude]);
            }
        }
    }
}
