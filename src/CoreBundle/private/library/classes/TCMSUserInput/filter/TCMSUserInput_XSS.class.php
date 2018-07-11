<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * @deprecated since 6.2.0 - user input needs to be escaped depending on the target system/language. Use Twig
 * escaping when rendering to HTML.
 */
class TCMSUserInput_XSS extends TCMSUserInput_BaseText
{
    /**
     * filter a single item
     * performs an XSS filter
     * input data filter that removes XSS attacks on html strings.
     *
     * @param string $sValue
     *
     * @return string
     */
    protected function FilterItem($sValue)
    {
        $sValue = parent::FilterItem($sValue);
        // performance tweak, filter is only necessary if value is not empty or not numeric
        if (!empty($val) || !is_numeric($sValue)) {
            // we need the filter only on strings with html parts
            if (false !== strpos($sValue, '<') || false !== strpos($sValue, '>')) {
                $oGlobal = TGlobal::instance();
                $oGlobal->SetPurifierConfig();
                $oPurifier = new HTMLPurifier($oGlobal->oHTMLPurifyConfig);
                $sValue = $oPurifier->purify($sValue);
                // HTMLPurifier converts & to &amp; so we need to reverse this
                if (false === strpos($sValue, '<')) {
                    $sValue = str_replace('&amp;', '&', $sValue);
                }
            }
        }

        return $sValue;
    }
}
