<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ChameleonSystem\CoreBundle\RequestType;

abstract class AbstractRequestType implements RequestTypeInterface
{
    /**
     * @return void
     */
    protected function sendDefaultHeaders()
    {
        if (true === headers_sent()) {
            return;
        }

        header('X-Frame-Options: SAMEORIGIN');
        if (CMS_AUTO_SEND_UTF8_HEADER) {
            header('Content-type: text/html; charset=UTF-8');
        }
    }
}
