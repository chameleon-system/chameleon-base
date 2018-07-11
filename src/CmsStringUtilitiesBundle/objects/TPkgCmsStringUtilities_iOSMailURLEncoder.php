<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

class TPkgCmsStringUtilities_iOSMailURLEncoder
{
    /**
     * @param string $source
     *
     * @return string
     */
    public function encode($source)
    {
        return preg_replace_callback(
            '/(<img [^>]*?src=["\'])(.*?)(["\'])/',
            function ($matches) {
                return $matches[1].str_replace('=', '&#61;', $matches[2]).$matches[3];
            },
            $source);
    }
}
