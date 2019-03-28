<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use ChameleonSystem\CoreBundle\Service\LanguageServiceInterface;
use ChameleonSystem\CoreBundle\Service\PageServiceInterface;
use ChameleonSystem\CoreBundle\Service\PortalDomainServiceInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * maps urls to pagedefs, and pagedefs to pages.
/**/
class TCMSSmartURL
{
    private static $additionalCacheKeys = null;

    /**
     * If inactive language was set via language prefix. Show page only if inactive
     * languages was activated in cms.
     *
     * You can activate inactive languages in cms -> portals/websites
     *
     * @param Request $request
     */
    protected static function HandleTemporaryActivatedLanguages(Request $request)
    {
        $oPortal = self::getPortalDomainService()->getActivePortal();
        if (!is_object($oPortal) || $oPortal->GetActivateAllPortalLanguages()) {
            return;
        }
        $oLanguage = self::getLanguageService()->getActiveLanguage();
        if (false === $oLanguage->fieldActiveForFrontEnd) {
            $oConfig = TdbCmsConfig::GetInstance();
            $oBaseLanguage = $oConfig->GetFieldTranslationBaseLanguage();

            $url = self::getPageService()->getLinkToPortalHomePageAbsolute(array(), $oPortal, $oBaseLanguage);
            self::getRedirect()->redirect($url);
        }
    }

    /**
     * @param array $aAdditionalCacheKeys
     */
    public static function setAdditionalCacheKeys($aAdditionalCacheKeys = array())
    {
        self::$additionalCacheKeys = $aAdditionalCacheKeys;
    }

    /**
     * converts the user request to the real pagedef behind that call. function
     * also protects pages from cross portal calls.
     *
     * @param Request $request
     *
     * @return string
     *
     * @throws NotFoundHttpException
     */
    public static function run(Request $request)
    {
        $oURLData = &TCMSSmartURLData::GetActive();
        self::HandleTemporaryActivatedLanguages($request);
        self::RedirectOnInvalidExternalArguments();
        $oGlobal = TGlobal::instance();

        $aNonSeoParameter = array_keys($oGlobal->GetRawUserData());
        $pagedef = false;
        // if we have a pagedef in post, then we are done right away.
        if ($oGlobal->UserDataExists('pagedef')) {
            $pagedef = $oGlobal->GetUserData('pagedef');
        } else {
            $aCustomURLParameters = array();
            // need to check for pagedef again since TCMSSmartURLData may have set it
            if ($oGlobal->UserDataExists('pagedef')) {
                $pagedef = $oGlobal->GetUserData('pagedef');
            } else {
                $pagedef = self::RunCustomHandlers($request, $aCustomURLParameters);
            }

            if (false === $pagedef) {
                $oURLData->bPagedefFound = false;

                throw new NotFoundHttpException();
            }

            $oGlobal->SetRewriteParameter($aCustomURLParameters);
            foreach ($aCustomURLParameters as $key => $value) {
                $request->query->set($key, $value);
            }

            $request->query->set('pagedef', $pagedef);
        }

        $aAllParameter = array_keys($oGlobal->GetRawUserData());
        $aSeoParameterList = array_diff($aAllParameter, $aNonSeoParameter);
        $oURLData->setSeoURLParameters($aSeoParameterList);

        $oURLData->SetObjectInitializationCompleted(true);

        return $pagedef;
    }

    /**
     * returns the root page for the portal. false if no root page was found.
     *
     * @param int $iPortalId
     *
     * @return int
     */
    public static function GetPortalRootPagedef($iPortalId)
    {
        $pagedef = false;
        $query = "SELECT *
                  FROM `cms_portal`
                 WHERE `id` = '".MySqlLegacySupport::getInstance()->real_escape_string($iPortalId)."'
               ";
        if ($trow = MySqlLegacySupport::getInstance()->fetch_assoc(MySqlLegacySupport::getInstance()->query($query))) {
            $pagedef = TCMSSmartURLHandler::GetNodePage($trow['home_node_id']);
        }

        return $pagedef;
    }

    /**
     * execute all custom handlers untill a pagedef is found. if none is found, return false
     * the custom handler which found a pagedef can return custom cache triggers.
     *
     * @param Symfony\Component\HttpFoundation\Request $request
     * @param array                                    $aCustomURLParameters
     *
     * @return int
     */
    protected static function RunCustomHandlers(Request $request, &$aCustomURLParameters)
    {
        $pagedef = false;
        $oPortal = self::getPortalDomainService()->getActivePortal();
        if (is_null($oPortal)) {
            $query = 'SELECT * FROM `cms_smart_url_handler` ORDER BY `position`';
        } else {
            $query = "SELECT `cms_smart_url_handler`.* FROM `cms_smart_url_handler`
                         LEFT JOIN `cms_smart_url_handler_cms_portal_mlt` ON `cms_smart_url_handler_cms_portal_mlt`.`source_id` = `cms_smart_url_handler`.`id`
                         LEFT JOIN `cms_portal` ON `cms_portal`.`id` = `cms_smart_url_handler_cms_portal_mlt`.`target_id`
                              WHERE (`cms_portal`.`id` = '".$oPortal->id."' OR `cms_portal`.`id` IS NULL)
                              AND `cms_smart_url_handler`.`active` = '1'
                           ORDER BY `cms_smart_url_handler`.`position`";
        }
        $thandler = MySqlLegacySupport::getInstance()->query($query);
        while (false === $pagedef && ($aHandler = MySqlLegacySupport::getInstance()->fetch_assoc($thandler))) {
            $sClassName = $aHandler['name'];

            /** @var TCMSSmartURLHandler $oNewHandler */
            $oNewHandler = new $sClassName();
            $oNewHandler->setRequest($request);
            $pagedef = $oNewHandler->GetPageDef();
            if (false !== $pagedef) {
                foreach ($oNewHandler->aCustomURLParameters as $key => $value) {
                    $aCustomURLParameters[$key] = $value;
                }
            }
        }

        return $pagedef;
    }

    /**
     * returns the SEO url as names with " - " as spacer (for <title> tag.
     *
     * @param int $iTreeNode
     * @param int $iPortalID
     *
     * @return string
     */
    public static function GetURLName($iTreeNode, $iPortalID = null)
    {
        $stopNodes = TCMSPortal::GetStopNodes($iPortalID);
        $oRootNode = new TCMSTreeNode();
        $oRootNode->Load($iTreeNode);
        $aPath = $oRootNode->GetPath($stopNodes);
        $name = '';
        for ($i = 2; $i < count($aPath); ++$i) {
            if ($i > 2) {
                $name .= ' - ';
            }
            $name .= $aPath[$i]->sqlData['name'];
        }

        return $name;
    }

    /**
     * checks for invalid arguments and redirects to the current page with all parameters except
     * the invalid argument list - this is used to filter external requests for search engines (prevent dublicate content).
     */
    protected static function RedirectOnInvalidExternalArguments()
    {
        if (defined('INVALID_GET_PARAMS') && INVALID_GET_PARAMS != '') {
            $bParamFound = false;
            $oURLData = &TCMSSmartURLData::GetActive();
            $aAllParamNames = array_keys($oURLData->aParameters);
            $aInvalidGetParams = explode('|', INVALID_GET_PARAMS);
            $aFinalParameters = array();
            foreach ($aAllParamNames as $key => $value) {
                if (in_array(strtolower($value), $aInvalidGetParams)) {
                    $bParamFound = true;
                } else {
                    $aFinalParameters[$value] = $oURLData->aParameters[$value];
                }
            }

            if ($bParamFound) {
                //$oActivePage = TCMSActivePage::GetInstance();
                //$iPageId = $oActivePage->id;
                $sUrlWithoutParams = substr($oURLData->sOriginalURL, 0, strpos($oURLData->sOriginalURL, '?'));

                if (count($aFinalParameters) > 0) {
                    $sUrlWithoutParams .= '?'.str_replace('&amp;', '&', TTools::GetArrayAsURL($aFinalParameters));
                }

                $newURL = REQUEST_PROTOCOL.'://'.$oURLData->sOriginalDomainName.$sUrlWithoutParams;
                self::getRedirect()->redirect($newURL, Response::HTTP_MOVED_PERMANENTLY);
            }
        }
    }

    /**
     * @return PortalDomainServiceInterface
     */
    private static function getPortalDomainService()
    {
        return \ChameleonSystem\CoreBundle\ServiceLocator::get('chameleon_system_core.portal_domain_service');
    }

    /**
     * @return LanguageServiceInterface
     */
    private static function getLanguageService()
    {
        return \ChameleonSystem\CoreBundle\ServiceLocator::get('chameleon_system_core.language_service');
    }

    /**
     * @return PageServiceInterface
     */
    private static function getPageService()
    {
        return \ChameleonSystem\CoreBundle\ServiceLocator::get('chameleon_system_core.page_service');
    }

    /**
     * @return ICmsCoreRedirect
     */
    private static function getRedirect()
    {
        return \ChameleonSystem\CoreBundle\ServiceLocator::get('chameleon_system_core.redirect');
    }
}
