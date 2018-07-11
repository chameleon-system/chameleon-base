<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

interface IPkgCmsStringUtilities_VariableInjection
{
    /**
     * @abstract
     *
     * @param string $sText
     * @param array  $aVariables
     *
     * @return string
     */
    public function replace($sString, $aCustomVariables, $bPassVarsThroughOutHTML = false, $iWidth = false);
}
