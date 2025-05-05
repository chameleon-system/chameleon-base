<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

class TPkgCmsStringUtilities_HTML
{
    /**
     * this is a simple implementation of htmlentities, but with one additional option: you can provide a blacklist of characters, that won't be converted.
     * This is sometimes useful, for example when you want to exclude umlauts from the convertion.
     *
     * @param string $string
     * @param array $blacklist
     *
     * @return string
     */
    public function convertEntitiesWithBlacklist($string, $blacklist = [])
    {
        /*
         * The number of arguments for get_html_translation_table is correct, PHPStorm stub is not (third argument was
         * added in PHP 5.3.4).
         * If you read this comment and PHPStorm does not complain about the method call, remove the comment.
         */
        $list = get_html_translation_table(HTML_ENTITIES, ENT_COMPAT, 'UTF-8');
        foreach ($blacklist as $item) {
            unset($list[$item]);
        }
        $search = array_keys($list);
        $values = array_values($list);

        return str_replace($search, $values, $string);
    }
}
