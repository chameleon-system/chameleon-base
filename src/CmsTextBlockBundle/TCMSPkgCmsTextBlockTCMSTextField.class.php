<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

class TCMSPkgCmsTextBlockTCMSTextField extends TCMSPkgCmsTextBlockTCMSTextFieldAutoParent
{
    private $aInternalTextBlockReplaceCache = array();

    /**
     * Replace CmsTextBlocks in String.
     *
     * @param string $sString
     * @param int    $iWidth  - max image width
     *
     * @return string
     */
    protected function _ReplaceCmsTextBlockInString($sString, $iWidth = 600)
    {
        if (false === stripos($sString, '[{')) {
            return $sString;
        }
        static $bReplacingTextblockInString = false;

        // prevent recursion. since the text blocks are also wysiwyg fields, we need to prevent them from trying to inject textblocks in textblocks - otherwise an endless loop occurs.
        if ($bReplacingTextblockInString) {
            return $sString;
        }

        $bReplacingTextblockInString = true;
        $aCustomVariables = $this->AddCmsTextBlockVariables($iWidth);
        if (is_array($aCustomVariables) && count($aCustomVariables) > 0) {
            $oStringReplace = new TPkgCmsStringUtilities_VariableInjection();
            $sString = $oStringReplace->replace($sString, $aCustomVariables, false, $iWidth);
        }
        $bReplacingTextblockInString = false;

        return $sString;
    }

    /**
     * Get list of available cms text block names for replacing cms text block tags.
     *
     * @param int $iWidth
     *
     * @return array
     */
    protected function AddCmsTextBlockVariables($iWidth)
    {
        $aCmsTextBlockPortalArray = array();
        $oPortal = TTools::GetActivePortal();
        if (!is_null($oPortal)) {
            $aCmsTextBlockPortalArray = $oPortal->GetPortalCmsTextBlockArray($iWidth);
        }

        return $aCmsTextBlockPortalArray;
    }
}
