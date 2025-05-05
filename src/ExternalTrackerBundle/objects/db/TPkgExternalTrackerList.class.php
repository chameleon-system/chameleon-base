<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use ChameleonSystem\CoreBundle\Service\ActivePageServiceInterface;
use Symfony\Component\HttpFoundation\Request;

class TPkgExternalTrackerList extends TPkgExternalTrackerListAutoParent implements IPkgCmsEventObserver
{
    public const SESSION_KEY_NAME = 'esono/pkgExternalTracker/trackerData';

    /**
     * @param string|null $sQuery
     * @param string|null $sLanguageId
     */
    public function __construct($sQuery = null, $sLanguageId = null)
    {
        parent::__construct($sQuery, $sLanguageId);
        $this->bAllowItemCache = true;
    }

    /**
     * @static
     *
     * @return TdbPkgExternalTrackerList
     */
    public static function GetActiveInstance()
    {
        static $oInstance = null;
        static $bTrackerListWasLoaded = false;
        if (is_null($oInstance)) {
            $oInstance = new TdbPkgExternalTrackerList();
        }
        if (false === $bTrackerListWasLoaded) {
            // we must load the items in the list only once the active page has come available. Otherwise, portal restrictions cannot apply
            $activePage = self::getMyActivePageService()->getActivePage();
            if (null !== $activePage) {
                $iLanguageId = self::getMyLanguageService()->getActiveLanguageId();
                $oInstance->Load(TdbPkgExternalTrackerList::GetDefaultQuery($iLanguageId));
                $bTrackerListWasLoaded = true;
            }
        }

        return $oInstance;
    }

    /**
     * this should be called just after the opening body tag. It outputs / injects the tracking code.
     *
     * @return void
     */
    public function TrackPage(TdbCmsTplPage $oPage)
    {
        $this->SetPage($oPage);
        $oEventManager = TPkgCmsEventManager::GetInstance();
        $oEventManager->RegisterObserver(IPkgCmsEvent::CONTEXT_CORE, IPkgCmsEvent::NAME_GET_CUSTOM_HEADER_DATA, $this);
        $oEventManager->RegisterObserver(IPkgCmsEvent::CONTEXT_CORE, IPkgCmsEvent::NAME_GET_CUSTOM_FOOTER_DATA, $this);
        $aClosingLines = $this->GetPostBodyOpeningCode();
        $sClosingLines = implode("\n", $aClosingLines);
        echo $sClosingLines;
    }

    /**
     * @param string $sEventName
     *
     * @return void
     */
    public function AddEvent($sEventName, $aParameter = [])
    {
        $state = $this->GetStateData();
        if (null === $state) {
            return;
        }
        $state->AddEventData($sEventName, $aParameter);
    }

    /**
     * add an object to the state data.
     *
     * @param string $sStateDataKey
     *
     * @return void
     */
    public function AddStateData($sStateDataKey, $oObject)
    {
        $state = $this->GetStateData();
        if (null === $state) {
            return;
        }
        $state->AddStateData($sStateDataKey, $oObject);
    }

    /**
     * @return void
     */
    private function SetPage(TdbCmsTplPage $oPage)
    {
        $state = $this->GetStateData();
        if (null === $state) {
            return;
        }
        $state->SetActivePage($oPage);
    }

    /**
     * inject head includes into controller.
     *
     * @return void
     */
    private function AddHTMLHeadIncludesToController()
    {
        $aHTMLHeadIncludes = $this->GetHTMLHeadIncludes();
        foreach ($aHTMLHeadIncludes as $sLine) {
            $oController = ChameleonSystem\CoreBundle\ServiceLocator::get('chameleon_system_core.chameleon_controller');
            $oController->AddHTMLHeaderLine($sLine);
        }
    }

    /**
     * @return void
     */
    private function AddHtmlFooterIncludesToController()
    {
        $aIncludes = $this->GetPreBodyClosingCode();
        foreach ($aIncludes as $sLine) {
            $oController = ChameleonSystem\CoreBundle\ServiceLocator::get('chameleon_system_core.chameleon_controller');
            $oController->AddHTMLFooterLine($sLine);
        }
    }

    /**
     * return the head includes.
     *
     * @return array
     */
    protected function GetHTMLHeadIncludes()
    {
        $stateData = $this->GetStateData();
        if (null === $stateData) {
            return [];
        }

        $aHTMLHeadIncludes = [];
        $this->GoToStart();
        while ($oItem = $this->Next()) {
            $aIncFromItem = $oItem->GetHTMLHeadIncludes($stateData);
            foreach ($aIncFromItem as $sLine) {
                if (!in_array($sLine, $aHTMLHeadIncludes)) {
                    $aHTMLHeadIncludes[] = $sLine;
                }
            }
        }

        return $aHTMLHeadIncludes;
    }

    /**
     * @return array
     */
    protected function GetPostBodyOpeningCode()
    {
        $stateData = $this->GetStateData();
        if (null === $stateData) {
            return [];
        }
        $aLines = [];
        $this->GoToStart();
        while ($oItem = $this->Next()) {
            $aIncFromItem = $oItem->GetPostBodyOpeningCode($stateData);
            foreach ($aIncFromItem as $sLine) {
                if (!in_array($sLine, $aLines)) {
                    $aLines[] = $sLine;
                }
            }
        }

        return $aLines;
    }

    /**
     * @return array
     */
    protected function GetPreBodyClosingCode()
    {
        $stateData = $this->GetStateData();
        if (null === $stateData) {
            return [];
        }
        $aLines = [];
        $this->GoToStart();
        while ($oItem = $this->Next()) {
            $aIncFromItem = $oItem->GetPreBodyClosingCode($stateData);
            foreach ($aIncFromItem as $sLine) {
                if (!in_array($sLine, $aLines)) {
                    $aLines[] = $sLine;
                }
            }
        }

        return $aLines;
    }

    /**
     * @return TPkgExternalTrackerState|null
     */
    protected function GetStateData()
    {
        /** @var Request $request */
        $request = ChameleonSystem\CoreBundle\ServiceLocator::get('request_stack')->getCurrentRequest();
        if (null === $request || false === $request->hasSession() || false === $request->getSession()->isStarted()) {
            return null;
        }

        if (false === $request->getSession()->has(self::SESSION_KEY_NAME)) {
            $request->getSession()->set(self::SESSION_KEY_NAME, new TPkgExternalTrackerState());
        }

        return $request->getSession()->get(self::SESSION_KEY_NAME);
    }

    /**
     * return an instance for the query passed.
     *
     * @param string $sQuery - custom query instead of default query
     * @param string $iLanguageId - the language id for record overloading
     * @param bool $bAllowCaching - set this to true if you want to cache the recordlist object
     * @param bool $bForceWorkflow - set this to true to force adding the workflow query part even in cms backend mode
     * @param bool $bUseGlobalFilterInsteadOfPreviewFilter - set this to true if you want to overload all workflow data instead of only the records that are marked for preview
     */
    public static function GetList($sQuery = null, $iLanguageId = null, $bAllowCaching = true, $bForceWorkflow = false, $bUseGlobalFilterInsteadOfPreviewFilter = false): TdbPkgExternalTrackerList
    {
        $oList = parent::GetList($sQuery, $iLanguageId, $bAllowCaching, $bForceWorkflow, $bUseGlobalFilterInsteadOfPreviewFilter);
        if (!is_null($sQuery) && $oList) {
            $sActiveRestriction = TdbPkgExternalTrackerList::GetActiveItemSQLSnippet();
            if (!empty($sActiveRestriction)) {
                $oList->AddFilterString($sActiveRestriction);
            }
        }

        return $oList;
    }

    /**
     * return the sql added to any list to restrict to only active items.
     *
     * @return string
     */
    public static function GetActiveItemSQLSnippet()
    {
        if (true === TGlobal::IsCMSMode()) {
            return ' 1 = 1';
        }

        $sFieldActive = 'active';
        if (TdbPkgExternalTracker::CMSFieldIsTranslated($sFieldActive)) {
            $sFieldActive = self::getFieldTranslationUtil()->getTranslatedFieldName('pkg_external_tracker', 'active');
        }

        return " (`pkg_external_tracker`.`{$sFieldActive}` = '1')".TdbPkgExternalTrackerList::GetActiveForPortalItemSQLSnippet();
    }

    /**
     * @return string
     */
    public static function GetActiveForPortalItemSQLSnippet()
    {
        $sPortalActive = '';
        $sPortalRestriction = '';
        $oActivePage = self::getMyActivePageService()->getActivePage();
        if ($oActivePage) {
            $activePortal = $oActivePage->GetPortal();
            if ($activePortal) {
                $sPortalRestriction = $activePortal->id;
            }
        }
        if ('' === $sPortalRestriction) {
            $sPortalActive .= ' AND `cms_portal`.`id` IS NULL';
        } else {
            $sPortalActive .= " AND (`cms_portal`.`id` = '".MySqlLegacySupport::getInstance()->real_escape_string($sPortalRestriction)."' OR `cms_portal`.`id` IS NULL)";
        }

        return $sPortalActive;
    }

    /**
     * return default query for the table.
     *
     * @param int $iLanguageId - language used for query
     * @param string $sFilterString - any filter conditions to add to the query
     */
    public static function GetDefaultQuery($iLanguageId, $sFilterString = '1=1'): string
    {
        $sActiveSnippet = TdbPkgExternalTrackerList::GetActiveItemSQLSnippet();
        if (!empty($sActiveSnippet)) {
            $sFilterString .= ' AND '.$sActiveSnippet;
        }
        $sQuery = "SELECT `pkg_external_tracker`.*
                          FROM `pkg_external_tracker`
                          LEFT JOIN `pkg_external_tracker_cms_portal_mlt` ON `pkg_external_tracker_cms_portal_mlt`.`source_id` = `pkg_external_tracker`.`id`
                          LEFT JOIN `cms_portal` ON `cms_portal`.`id` = `pkg_external_tracker_cms_portal_mlt`.`target_id`
                         WHERE {$sFilterString}";

        return $sQuery;
    }

    /**
     * @return IPkgCmsEvent
     *                      the method is called when an event is triggered
     */
    public function PkgCmsEventNotify(IPkgCmsEvent $oEvent)
    {
        $state = $this->GetStateData();
        if (null === $state) {
            return $oEvent;
        }
        switch ($oEvent->GetName()) {
            case IPkgCmsEvent::NAME_GET_CUSTOM_HEADER_DATA:
                $state->setDataProcessed(true);
                $this->AddHTMLHeadIncludesToController();
                break;
            case IPkgCmsEvent::NAME_GET_CUSTOM_FOOTER_DATA:
                $state->setDataProcessed(true);
                $this->AddHtmlFooterIncludesToController();
                break;
        }

        return $oEvent;
    }

    /**
     * @return ActivePageServiceInterface
     */
    private static function getMyActivePageService()
    {
        return ChameleonSystem\CoreBundle\ServiceLocator::get('chameleon_system_core.active_page_service');
    }
}
