<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use ChameleonSystem\CoreBundle\Service\SystemPageServiceInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Exception\RouteNotFoundException;

abstract class TCMSSmartURLHandler
{
    /**
     * any parameters dumped into this array will be added to oGlobal->userData
     * the caching system will make sure that they are recoved when fetching a page from
     * cache. so make sure you never write into oGlobal->userData from within this
     * class, or any of its children.
     *
     * @var array
     */
    public $aCustomURLParameters = [];

    /**
     * Set custom cache triggers for URL handler like  array('table'=>'tablename','id'=>'record_id').
     * TCMSSmartURL will add these cache triggers to his own cache triggers if the URL handler had found a valid page.
     *
     * @var array
     */
    public $aCacheChangeTriggers = [];

    /**
     * this method should parse the url and check which page matches
     * it should convert url parts to GET parameters by using aCustomURLParameters.
     *
     * @return string|bool
     */
    abstract public function GetPageDef();

    /**
     * @var Request
     */
    private $request;

    /**
     * @return Request
     */
    protected function getRequest()
    {
        return $this->request;
    }

    public function setRequest(Request $request)
    {
        $this->request = $request;
    }

    /**
     * removes empty elements from the path, and any .html endings.
     *
     * @param array $aPath
     *
     * @return array
     */
    protected function CleanPath($aPath)
    {
        $cleanedPathElements = [];
        foreach ($aPath as $pathElement) {
            $pathElement = trim($pathElement);
            if ('' === $pathElement) {
                continue;
            }

            if ('.html' === substr($pathElement, -5)) {
                $pathElement = substr($pathElement, 0, -5);
            }

            if (!empty($pathElement) || '0' == $pathElement) {
                $cleanedPathElements[] = $pathElement;
            }
        }

        return $cleanedPathElements;
    }

    /**
     * @param string $iNodeId
     *
     * @return bool|string
     *
     * @throws ErrorException
     * @throws TPkgCmsException_Log
     */
    public static function GetNodePage($iNodeId)
    {
        $iPageId = false;
        $sCurrentDateTime = date('Y-m-d H:i:s');
        $query = "SELECT *
                  FROM cms_tree_node
                 WHERE `cms_tree_id` = '".MySqlLegacySupport::getInstance()->real_escape_string($iNodeId)."'
                   AND `active` = '1'
                   AND `tbl` = 'cms_tpl_page'
                   AND `contid` != ''
                   AND `start_date` <= '".MySqlLegacySupport::getInstance()->real_escape_string($sCurrentDateTime)."'
                   AND (`end_date` >= '".MySqlLegacySupport::getInstance()->real_escape_string($sCurrentDateTime)."' OR `end_date` = '0000-00-00 00:00:00')
                   ORDER BY `start_date` DESC, `cmsident` DESC
                   LIMIT 1
               ";

        $oCmsTreeNodeList = TdbCmsTreeNodeList::GetList($query);
        /** @var $oCmsTreeNodeList TdbCmsTreeNodeList */
        if ($oCmsTreeNodeList->Length() > 0) {
            $oCmsTreeNode = $oCmsTreeNodeList->Current();
            /** @var $oCmsTreeNode TdbCmsTreeNode */
            $iPageId = $oCmsTreeNode->sqlData['contid'];
        }

        return $iPageId;
    }

    /**
     * checks if the current page url matches the systempage with name $sSystemPageName
     * if url match was found it returns the id of the page else false.
     *
     * @param string $sSystemPageName
     * @param TCMSPortal $oCmsPortal
     * @param TCMSSmartURLData $oURLData
     *
     * @return string|false - returns false if systempage does not match the url
     */
    protected function GetPortalSystemPageID($sSystemPageName, $oCmsPortal, $oURLData)
    {
        $sSystemPageID = false;
        try {
            $sSystemPagePath = $this->getSystemPageService()->getLinkToSystemPageRelative($sSystemPageName, [], $oCmsPortal);
        } catch (RouteNotFoundException $e) {
            return false;
        }
        if (!stristr($sSystemPagePath, 'javascript:')) {
            if ('.html' == substr($sSystemPagePath, -5)) {
                $sSystemPagePath = substr($sSystemPagePath, 0, -5);
            }
            if ('http://' == substr($sSystemPagePath, 0, 7) || 'https://' == substr($sSystemPagePath, 0, 8)) {
                $sSystemPagePath = substr($sSystemPagePath, strpos($sSystemPagePath, '/', 8));
            }
            if ('/' != substr($sSystemPagePath, -1)) {
                $sSystemPagePath .= '/';
            }

            if (strlen($sSystemPagePath) < strlen($oURLData->sRelativeFullURL) && substr($oURLData->sRelativeFullURL, 0, strlen($sSystemPagePath)) == $sSystemPagePath) {
                $sSystemPageID = $oCmsPortal->GetSystemPageId($sSystemPageName);
            }
        }

        return $sSystemPageID;
    }

    /**
     * add parameters to the url
     * by default all of the existing parameters in TCMSSmartURLData
     * this is maybe useful if you don't want to loose any tracking parameters from any tracking / analytics service.
     *
     * @param string $sUrl
     * @param array $aFilterParams blacklist of url parameters that will be ignored
     *
     * @return string
     */
    protected function addParametersToUrl($sUrl, ?TCMSSmartURLData $oUrlData = null, $aFilterParams = [])
    {
        $iCount = 0;
        if (null === $oUrlData) {
            $oUrlData = TCMSSmartURLData::GetActive();
        }
        foreach ($oUrlData->aParameters as $sName => $sValue) {
            if (!in_array($sName, $aFilterParams)) {
                ++$iCount;
                if (1 === $iCount && false === strpos($sUrl, '?')) {
                    $sUrl .= '?';
                } else {
                    $sUrl .= '&';
                }

                if (is_array($sValue)) {
                    foreach ($sValue as $key => $val) {
                        $sUrl .= $sName.'['.$key.']='.$val.'&';
                    }

                    $sUrl = substr($sUrl, 0, -1);
                } else {
                    $sUrl .= $sName.'='.$sValue;
                }
            }
        }

        return $sUrl;
    }

    public function __construct()
    {
    }

    /**
     * @return SystemPageServiceInterface
     */
    private function getSystemPageService()
    {
        return ChameleonSystem\CoreBundle\ServiceLocator::get('chameleon_system_core.system_page_service');
    }

    /**
     * @return ICmsCoreRedirect
     */
    protected function getRedirect()
    {
        return ChameleonSystem\CoreBundle\ServiceLocator::get('chameleon_system_core.redirect');
    }
}
