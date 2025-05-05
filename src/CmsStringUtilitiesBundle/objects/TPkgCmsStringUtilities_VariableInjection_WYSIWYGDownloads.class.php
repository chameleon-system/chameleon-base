<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

class TPkgCmsStringUtilities_VariableInjection_WYSIWYGDownloads extends TPkgCmsStringUtilities_VariableInjection
{
    /**
     * replaces custom var or cms text blocks in the text
     * These variables in the text must have the following format: [{name:format}]
     * "format" ist either string, date, or number. It is possible to specify the number of decimals
     * used when formating a number: [{variable:number:decimalplaces}]
     * example [{costs:number:2}].
     *
     * {@inheritDoc}
     */
    public function replace($sString, $aCustomVariables, $bPassVarsThroughOutHTML = false, $iWidth = false)
    {
        if (!empty($sString)) {
            $aMatches = $this->getMatches($sString);

            foreach ($aMatches as $sMatch) {
                $sDownloadLink = $this->getDownloadLinkForVariable($sMatch);
                $sString = str_replace($sMatch, $sDownloadLink, $sString);
            }
        }

        return $sString;
    }

    /**
     * @param string $sContent
     *
     * @return string[]
     */
    protected function getMatches($sContent)
    {
        $sPattern = '#\[\{\s*?((\w|-){36}|\d+)\s*?,\s*?dl\s*?,[^,\[\{\}\]]*?\s*?(|,\s*?(ico|kb)|,\s*?ico\s*?,\s*?kb|,\s*?kb\s*?,\s*?ico)\s*?\}\]#';
        preg_match_all($sPattern, $sContent, $aMatches);

        return $aMatches[0];
    }

    /**
     * method called by the regex to replace the variables in the message string.
     *
     * @param string $sMatch
     *
     * @return string
     */
    protected function getDownloadLinkForVariable($sMatch)
    {
        preg_match('/\[\{(.*?)\}\]/si', $sMatch, $aMatches);
        $sReturn = $aMatches[0];
        $aParts = explode(',', $aMatches[1]);
        foreach ($aParts as $sKey => $sPart) {
            $aParts[$sKey] = trim($sPart);
        }
        // must contain at least 3 items
        if (count($aParts) > 2) {
            $aParsedResult = [
                'id' => $aParts[0],
                'name' => $aParts[2],
                'ico' => (false !== array_search('ico', $aParts)) ? (true) : (false),
                'kb' => (false !== array_search('kb', $aParts)) ? (true) : (false),
            ];

            $oItem = new TCMSDownloadFile();
            if ($oItem->Load($aParsedResult['id'])) {
                $sReturn = $this->getLink($oItem, false, false, !$aParsedResult['kb'], false, !$aParsedResult['ico'], $aParsedResult['name']);
            } else {
                $sReturn = '<!-- download for id ['.$aParsedResult['id'].'] not found -->';
            }
        }

        return $sReturn;
    }

    /**
     * @param TCMSDownloadFile $oItem
     * @param bool $dummyLink
     * @param bool $bHideName
     * @param bool $bHideSize
     * @param bool $bCreateTmpLink
     * @param bool $bHideIcon
     * @param string $sDownloadLinkName
     *
     * @return string
     */
    protected function getLink($oItem, $dummyLink = false, $bHideName = false, $bHideSize = false, $bCreateTmpLink = false, $bHideIcon = false, $sDownloadLinkName = '')
    {
        return $oItem->getDownloadHtmlTag($dummyLink, $bHideName, $bHideSize, $bHideIcon, ($sDownloadLinkName != $oItem->GetName()) ? $sDownloadLinkName : '');
    }
}
