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
use Symfony\Component\Routing\Exception\RouteNotFoundException;

class TPkgCommentModuleConfig extends TPkgCommentModuleConfigAutoParent
{
    public const VIEW_PATH = 'pkgComment/views/db/TPkgCommentModuleConfig';

    /**
     * @var int|string
     *
     * @psalm-var positive-int|0
     */
    protected $iPageSize = 0;

    /**
     * @var int
     *
     * @psalm-var positive-int
     */
    protected $iPage = 1;

    /**
     * @var float|int
     *
     * @psalm-var positive-int|0
     */
    protected $iMaxPage = 0;

    /**
     * @var int
     *
     * @psalm-var positive-int|0
     */
    protected $iCommentNr = 0;

    /**
     * @var mixed|null
     */
    protected $oActiveItem;

    /**
     * allow users that are not signed in to comment.
     *
     * @return bool
     */
    public function AllowGuestComments()
    {
        return $this->fieldGuestCommentAllowed;
    }

    /**
     * @return void
     */
    public function SetActiveItem($oActiveItem)
    {
        $this->oActiveItem = $oActiveItem;
    }

    /**
     * Returns the comment type configured in the module config.
     *
     * @return TdbPkgCommentType|null
     */
    public function GetFieldPkgCommentType()
    {
        /** @var TdbPkgCommentType|null $oItem */
        $oItem = $this->GetFromInternalCache('oLookuppkg_comment_type_id');

        if (is_null($oItem)) {
            $oItem = TdbPkgCommentType::GetNewInstance();
            $oItem->SetLanguage($this->iLanguageId);

            /** @var TdbPkgCommentType $oItem */
            $oItem = $oItem->GetInstance($this->fieldPkgCommentTypeId);

            if (!array_key_exists('id', $oItem->sqlData) && !empty($oItem->sqlData['id'])) {
                $oItem = null;
            }
            $this->SetInternalCache('oLookuppkg_comment_type_id', $oItem);
        }

        return $oItem;
    }

    /**
     * @param object $oActiveCommentType
     * @param TdbPkgCommentType $oPkgCommentType
     *
     * @return string
     */
    private function getCommentInternalCacheKey($oActiveCommentType, $oPkgCommentType)
    {
        $sActiveTypeId = null === $oActiveCommentType ? 'null' : $oActiveCommentType->id;
        $sTypeId = null === $oPkgCommentType ? 'null' : $oPkgCommentType->id;

        return 'oModuleCommentList_'.$sActiveTypeId.'_'.$sTypeId;
    }

    /**
     * return comment list from comment type id and active item id.
     *
     * @return TdbPkgCommentList
     */
    public function GetComments()
    {
        $oPkgCommentType = $this->GetFieldPkgCommentType();
        $oActiveCommentType = $this->GetActiveItem();
        $sKey = $this->getCommentInternalCacheKey($oActiveCommentType, $oPkgCommentType);
        $oCommentList = $this->GetFromInternalCache($sKey);
        if (is_null($oCommentList)) {
            if (!is_null($oActiveCommentType) && $this->AllowShowComments()) {
                $oCommentList = $this->GetListForItemTable($oActiveCommentType->id, $oPkgCommentType->id);
                $this->iCommentNr = $oCommentList->GetNrOfComments();
                if ($this->iPageSize > 0) {
                    /* @psalm-suppress InvalidPropertyAssignmentValue */
                    $this->iMaxPage = ceil($oCommentList->Length() / $this->iPageSize);
                } else {
                    $this->iMaxPage = 1;
                }
                $oCommentList = $this->SetPageInfos($oCommentList);
            } else {
                $oCommentList = TdbPkgCommentList::GetList();
            }
            $this->SetInternalCache('oModuleCommentList', $oCommentList);
        }

        return $oCommentList;
    }

    /**
     * set page infos to comment list.
     *
     * @param TdbPkgCommentList $oCommentList
     *
     * @return TdbPkgCommentList
     */
    protected function SetPageInfos($oCommentList)
    {
        $iStartRecord = ($this->iPage - 1) * $this->iPageSize;
        if ($this->iPageSize > 0) {
            $oCommentList->SetPagingInfo($iStartRecord, $this->iPageSize);
        } else {
            $oCommentList->SetPagingInfo($iStartRecord, -1);
        }

        return $oCommentList;
    }

    /**
     * set page infos to view.
     *
     * @param TViewParser $oView
     *
     * @return TViewParser
     */
    protected function GetPageInfosToview($oView)
    {
        $oView->AddVar('iAktPage', $this->iPage);
        $oView->AddVar('iMaxPage', $this->iMaxPage);
        $oView->AddVar('iPageSizege', $this->iPageSize);

        return $oView;
    }

    /**
     * set page size from db.
     *
     * @return void
     *
     * @psalm-suppress InvalidPropertyAssignmentValue
     */
    protected function SetPageInfo()
    {
        $this->iPageSize = $this->fieldNumberOfCommentsPerPage;
    }

    /**
     * set page from userdata.
     *
     * @return void
     */
    protected function SetPage()
    {
        $oGlobal = TGlobal::instance();
        $iPage = $oGlobal->GetUserData(TdbPkgComment::URL_NAME_ID_PAGE);
        if (!empty($iPage)) {
            $this->iPage = $iPage;
        }
    }

    /**
     * return comment list for item and type.
     *
     * @param string $sItemId
     * @param string $sTypeId
     * @param string|null $iLanguageId
     *
     * @return TdbPkgCommentList
     */
    public function GetListForItemTable($sItemId, $sTypeId, $iLanguageId = null)
    {
        $sQuery = "SELECT * FROM `pkg_comment`
                WHERE `pkg_comment`.`item_id` = '".MySqlLegacySupport::getInstance()->real_escape_string($sItemId)."'
                  AND `pkg_comment`.`pkg_comment_type_id` = '".MySqlLegacySupport::getInstance()->real_escape_string($sTypeId)."'
                  AND  `pkg_comment_id`='' ";
        if (!$this->fieldShowReportedComments) {
            $sQuery .= "AND `pkg_comment`.`mark_as_reported` = '0'";
        }
        $sOrderBy = 'ORDER BY `pkg_comment`.`created_timestamp` DESC';
        if (array_key_exists('newest_on_top', $this->sqlData) && '0' === $this->sqlData['newest_on_top']) {
            $sOrderBy = 'ORDER BY `pkg_comment`.`created_timestamp` ASC';
        }
        $sQuery .= ' '.$sOrderBy;
        if (null === $iLanguageId) {
            $iLanguageId = self::getLanguageService()->getActiveLanguageId();
        }
        $oList = TdbPkgCommentList::GetList();
        $oList->SetLanguage($iLanguageId);
        $oList->Load($sQuery);

        return $oList;
    }

    /**
     * Check if comments can be shown.
     *
     * @return bool
     */
    public function AllowShowComments()
    {
        $bAllowShowComments = false;
        $oUser = TdbDataExtranetUser::GetInstance();
        if ($this->fieldGuestCanSeeComments) {
            $bAllowShowComments = true;
        } elseif ($oUser->IsLoggedIn()) {
            $bAllowShowComments = true;
        }

        return $bAllowShowComments;
    }

    /**
     * return active item.
     *
     * @return object|null
     */
    public function GetActiveItem()
    {
        if (is_null($this->oActiveItem)) {
            $oPkgCommentType = $this->GetFieldPkgCommentType();
            $this->oActiveItem = $oPkgCommentType->GetActiveItem();
        }

        return $this->oActiveItem;
    }

    /**
     * render the list using a given view.
     *
     * @param string $sViewName - view name
     * @param array $aCallTimeVars - optional parameters passed from the calling method to the view
     *
     * @return string
     */
    public function Render($sViewName, $aCallTimeVars = [])
    {
        $oView = new TViewParser();
        $this->SetPage();
        if (array_key_exists('oActiveCommentItem', $aCallTimeVars)) {
            $this->oActiveItem = $aCallTimeVars['oActiveCommentItem'];
        }
        $this->SetPageInfo();
        $oView = $this->GetAdditionalViewVariables($oView);
        $oView->AddVar('aCallTimeVars', $aCallTimeVars);

        return $oView->RenderObjectPackageView($sViewName, self::VIEW_PATH, 'Customer');
    }

    /**
     * @return string[][]
     */
    public function GetCacheTrigger()
    {
        $aClearCacheParameter = [];
        $oCommentList = $this->GetComments();
        while ($oComment = $oCommentList->Next()) {
            $aClearCacheParameter[] = ['table' => 'pkg_comment', 'id' => $oComment->id];
            $oChildCommentList = $oComment->GetChildComments();
            while ($oChildComment = $oChildCommentList->Next()) {
                $aClearCacheParameter[] = ['table' => 'pkg_comment', 'id' => $oChildComment->id];
            }
            $oChildCommentList->GoToStart();
        }
        $oCommentList->GoToStart();
        if (!is_null($this->id)) {
            $aClearCacheParameter[] = ['table' => 'pkg_comment_module_config', 'id' => $this->id];
        }
        $aClearCacheParameter[] = ['table' => 'pkg_comment_type', 'id' => $this->fieldPkgCommentTypeId];

        return $aClearCacheParameter;
    }

    /**
     * Get additional data to show in view.
     *
     * @param TViewParser $oView
     *
     * @return TViewParser
     */
    protected function GetAdditionalViewVariables($oView)
    {
        $oUser = TdbDataExtranetUser::GetInstance();
        try {
            $sAnnounceCommentLink = $this->getSystemPageService()->getLinkToSystemPageRelative('announcecomment');
            if (strstr($sAnnounceCommentLink, 'javascript:alert')) {
                $sAnnounceCommentLink = '';
            }
        } catch (RouteNotFoundException $e) {
            $sAnnounceCommentLink = '';
        }
        $oCommentList = $this->GetComments();
        $oActiveItem = $this->GetActiveItem();
        $oView->AddVar('sAnnounceCommentLink', $sAnnounceCommentLink);
        $oView->AddVar('oModconf', $this);
        $oView->AddVar('iCommentNr', $this->iCommentNr);
        $oView->AddVar('oActiveItem', $oActiveItem);
        $oView->AddVar('oCommentList', $oCommentList);
        $oView->AddVar('bShowComments', $this->AllowShowComments());
        $oView->addVar('bAllowGuestComments', $this->AllowGuestComments());
        $bShowCommentForm = false;
        if ($this->AllowGuestComments() || $oUser->IsLoggedIn()) {
            $bShowCommentForm = true;
        }
        $oView->addVar('bShowCommentForm', $bShowCommentForm);
        $bShowComments = false;
        if ($this->fieldGuestCanSeeComments || $oUser->IsLoggedIn()) {
            $bShowComments = true;
        }
        $oView->addVar('bShowComments', $bShowComments);
        $oView = $this->GetPageInfosToview($oView);

        return $oView;
    }

    /**
     * @return SystemPageServiceInterface
     */
    private function getSystemPageService()
    {
        return ChameleonSystem\CoreBundle\ServiceLocator::get('chameleon_system_core.system_page_service');
    }
}
