<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use ChameleonSystem\CoreBundle\Service\PortalDomainServiceInterface;
use ChameleonSystem\CoreBundle\Util\UrlUtil;
use Doctrine\DBAL\Connection;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * {@inheritdoc}
 */
class TCMSSmartURLHandler_URLAlias extends TCMSSmartURLHandler
{
    public function GetPageDef()
    {
        // Redirect URL-Aliases:
        /** @var TCMSSmartURLData $oURLData */
        $oURLData = TCMSSmartURLData::GetActive();
        $aUrlList = $this->getUrlAliasListAsSortedArray();
        foreach ($aUrlList as $oURLAlias) {
            // IF our URL has parameters AND we have ignore parameters, then we need to find out if there are any left after that
            $aSourceParam = [];
            $iSourceParamPos = strpos($oURLAlias->fieldSourceUrl, '?');
            if (false !== $iSourceParamPos) {
                parse_str(substr($oURLAlias->fieldSourceUrl, $iSourceParamPos + 1), $aSourceParam);
            }
            $aMapping = $this->GetMappingParameter($oURLAlias);
            $aTmpParameter = $oURLData->aParameters;
            if (count($aTmpParameter) && (!empty($oURLAlias->fieldIgnoreParameter) || count($aMapping) > 0)) {
                $sIgnore = trim($oURLAlias->fieldIgnoreParameter);
                if ('*' === $sIgnore) {
                    // ignore all EXCEPT those that are part of the source
                    foreach (array_keys($aTmpParameter) as $sKey) {
                        if (!array_key_exists($sKey, $aSourceParam)) {
                            unset($aTmpParameter[$sKey]);
                        }
                    }
                } else {
                    $aIgnore = explode("\n", str_replace(',', "\n", $oURLAlias->fieldIgnoreParameter));
                    foreach ($aIgnore as $sKey) {
                        if (array_key_exists($sKey, $aTmpParameter)) {
                            unset($aTmpParameter[$sKey]);
                        }
                    }
                    foreach ($aMapping as $sOldParameter => $sMappingParameter) {
                        if (array_key_exists($sOldParameter, $aTmpParameter)) {
                            unset($aTmpParameter[$sOldParameter]);
                        }
                    }
                }
            }

            // check if the parameters passed match the parameters in the source URL

            // we have a match, if source and $aTmpParameter match
            $bIsSame = false;
            if (count($aSourceParam) === count($aTmpParameter)) {
                $bIsSame = true;
                reset($aSourceParam);
                foreach ($aSourceParam as $sKey => $sValue) {
                    if (!array_key_exists($sKey, $aTmpParameter) || (array_key_exists($sKey, $aTmpParameter) && $aTmpParameter[$sKey] !== $sValue)) {
                        $bIsSame = false;
                        break;
                    }
                }
            }
            if ($bIsSame) {
                // redirect...
                $oStringReplace = new TPkgCmsStringUtilities_VariableInjection();
                $sTarget = $oStringReplace->replace($oURLAlias->fieldTargetUrl, $aTmpParameter, false);
                $sTarget = $this->AddMappedParametersToURL($aMapping, $oURLData, $sTarget);
                $this->getRedirect()->redirect($sTarget, Response::HTTP_MOVED_PERMANENTLY);
            }
        }

        return false;
    }

    /**
     * Sorts an array of URL aliases.
     * The array is sorted by URLs with fewest parts count first.
     * e.g. /home/, /products/, /products/merchant/, /service/law/about/.
     *
     * @return int
     */
    private function compareUrlAlias(TdbCmsUrlAlias $a, TdbCmsUrlAlias $b)
    {
        return substr_count($a->fieldTargetUrl, '/') - substr_count($b->fieldTargetUrl, '/');
    }

    /**
     * Returns the list of URL aliases for the request URL.
     *
     * @return TdbCmsUrlAliasList
     */
    protected function getUrlAliasList()
    {
        $dbConnection = $this->getDatabaseConnection();
        $request = $this->getCurrentRequest();

        $conditions = [];
        $relativeSourceUrl = $request->getRequestUri();
        $paramStartPos = strpos($relativeSourceUrl, '?');
        $hasParameters = false !== $paramStartPos;

        if (true === $hasParameters) {
            $relativeSourceUrl = substr($relativeSourceUrl, 0, $paramStartPos);
            $relativeSourceUrl = trim($relativeSourceUrl, '/');
            $absoluteSourceUrl = $request->getSchemeAndHttpHost().'/'.$relativeSourceUrl;

            $conditions[] = '`source_url` LIKE '.$dbConnection->quote($relativeSourceUrl.'?%');
            $conditions[] = '`source_url` LIKE '.$dbConnection->quote($relativeSourceUrl.'/?%');
            $conditions[] = '`source_url` LIKE '.$dbConnection->quote('/'.$relativeSourceUrl.'/?%');
            $conditions[] = '`source_url` LIKE '.$dbConnection->quote('/'.$relativeSourceUrl.'?%');

            $conditions[] = '`source_url` LIKE '.$dbConnection->quote($absoluteSourceUrl.'?%');
            $conditions[] = '`source_url` LIKE '.$dbConnection->quote($absoluteSourceUrl.'/?%');
        } else {
            $relativeSourceUrl = trim($relativeSourceUrl, '/');
            $absoluteSourceUrl = $request->getSchemeAndHttpHost().'/'.$relativeSourceUrl;

            $conditions[] = '`source_url` = '.$dbConnection->quote($relativeSourceUrl);
            $conditions[] = '`source_url` = '.$dbConnection->quote('/'.$relativeSourceUrl.'/');
            $conditions[] = '`source_url` = '.$dbConnection->quote($relativeSourceUrl.'/');
            $conditions[] = '`source_url` = '.$dbConnection->quote('/'.$relativeSourceUrl);

            $conditions[] = '`source_url` = '.$dbConnection->quote($absoluteSourceUrl);
            $conditions[] = '`source_url` = '.$dbConnection->quote($absoluteSourceUrl.'/');
        }

        // handle non exact match records
        $conditions[] = sprintf("(`exact_match` = '0' AND `source_url` LIKE %s)", $dbConnection->quote('/'.$relativeSourceUrl.'%'));
        $conditions[] = sprintf("(`exact_match` = '0' AND `source_url` LIKE %s)", $dbConnection->quote($absoluteSourceUrl.'%'));

        /** @var TdbCmsPortal $oPortal */
        $oPortal = $this->getPortalDomainService()->getActivePortal();
        $conditionString = implode(' OR ', $conditions);
        $sQuery = "SELECT *
                     FROM `cms_url_alias`
                    WHERE `active` = '1'
                      AND (`cms_portal_id` = ".$dbConnection->quote($oPortal->id)." OR `cms_portal_id` = '')
                      AND (`source_url` != `target_url` OR (`source_url` = `target_url` AND `parameter_mapping` != ''))
                      AND ($conditionString)";

        return TdbCmsUrlAliasList::GetList($sQuery);
    }

    /**
     * loop through record list and save every object in array that will be sorted later on.
     *
     * @return array
     */
    protected function getUrlAliasListAsSortedArray()
    {
        $aUrlList = [];
        $oCmsUrlAliasList = $this->getUrlAliasList();
        while ($oURLAlias = $oCmsUrlAliasList->Next()) {
            $aUrlList[] = $oURLAlias;
        }
        usort($aUrlList, [$this, 'compareUrlAlias']);

        return $aUrlList;
    }

    /**
     * Get array with mapping parameter from given url alias
     * key = old parameter name
     * value = new parameter name.
     *
     * @param TdbCmsUrlAlias $oURLAlias
     *
     * @return array
     */
    protected function GetMappingParameter($oURLAlias)
    {
        $aParameterList = [];
        if (!empty($oURLAlias->fieldParameterMapping)) {
            $sParameterString = trim($oURLAlias->fieldParameterMapping);
            if (!empty($sParameterString)) {
                $aParameterRows = explode("\n", $sParameterString);
                foreach ($aParameterRows as $row) {
                    $sepPos = strpos($row, '=');
                    if (false !== $sepPos) {
                        $tmpKey = substr($row, 0, $sepPos);
                        $tmpVal = substr($row, $sepPos + 1);
                        $aParameterList[trim($tmpKey)] = trim($tmpVal);
                    }
                }
            }
        }

        return $aParameterList;
    }

    /**
     * Add mapped parameter to target url.
     *
     * @param array $aMapping
     * @param TCMSSmartURLData $oURLData
     * @param string $sTargetURL
     *
     * @return string
     */
    protected function AddMappedParametersToURL($aMapping, $oURLData, $sTargetURL)
    {
        if (count($aMapping) > 0) {
            if (false !== strpos($sTargetURL, '?')) {
                $sTargetURL .= '&';
            } else {
                $sTargetURL .= '?';
            }
            $aMappedURLParameter = [];
            reset($aMapping);
            foreach ($aMapping as $sOldParameter => $sMappedToParameter) {
                if (array_key_exists($sOldParameter, $oURLData->aParameters)) {
                    $aMappedURLParameter[$sMappedToParameter] = $oURLData->aParameters[$sOldParameter];
                }
            }

            $urlUtil = $this->getUrlUtilService();
            $sTargetURL .= $urlUtil->getArrayAsUrl($aMappedURLParameter, '', '&');
        }

        return $sTargetURL;
    }

    /**
     * @return Connection
     */
    private function getDatabaseConnection()
    {
        return ChameleonSystem\CoreBundle\ServiceLocator::get('database_connection');
    }

    /**
     * @return Request
     */
    private function getCurrentRequest()
    {
        return ChameleonSystem\CoreBundle\ServiceLocator::get('request_stack')->getCurrentRequest();
    }

    /**
     * @return PortalDomainServiceInterface
     */
    private function getPortalDomainService()
    {
        return ChameleonSystem\CoreBundle\ServiceLocator::get('chameleon_system_core.portal_domain_service');
    }

    /**
     * @return UrlUtil
     */
    private function getUrlUtilService()
    {
        return ChameleonSystem\CoreBundle\ServiceLocator::get('chameleon_system_core.util.url');
    }
}
