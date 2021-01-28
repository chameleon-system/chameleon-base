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
use ChameleonSystem\CoreBundle\Util\UrlNormalization\UrlNormalizationUtil;

class TPkgComment extends TPkgCommentAutoParent
{
    const URL_NAME_ID_PAGE = 'chameleon_system_comment.url.id_page';

    const URL_NAME_ID = 'sCommentId';

    const URL_NAME = 'chameleon_system_comment.url.name';

    const URL_NAME_JUMPER = 'chameleon_system_comment.url.jumper';

    const MESSAGE_CONSUMER_NAME = 'comments';

    const URL_ACTION_PARAMETER = 'action';

    const VIEW_PATH = 'pkgComment/views/db/TPkgComment';

    protected $aActionModes = array('edit', 'recomment');

    /**
     * Returns the active comment.
     *
     * @static TdbPkgComment
     *
     * @return TdbPkgComment
     */
    public static function &GetInstance()
    {
        static $oComment = null;
        if (is_null($oComment)) {
            $oGlobal = TGlobal::instance();
            $iActiveCommentId = $oGlobal->GetUserData(TdbPkgComment::URL_NAME_ID);
            if (!empty($iActiveCommentId)) {
                $oComment = TdbPkgComment::GetInstanceFromId($iActiveCommentId);
            }
        }

        return $oComment;
    }

    /**
     * return TdbPkgComment - or a child of that class (if the fields class, class_subtype and class_type have been set).
     *
     * @param string $sId
     * @param bool   $bReload
     *
     * @return TdbPkgComment
     */
    public static function GetInstanceFromId($sId, $bReload = false)
    {
        static $aInstance = array();
        $sInstanceIdent = 'x'.$sId;
        if ($bReload) {
            $aInstance[$sInstanceIdent] = null;
        }
        if (!array_key_exists($sInstanceIdent, $aInstance) || null == $aInstance[$sInstanceIdent]) {
            $oComment = TdbPkgComment::GetNewInstance();
            if ($oComment->Load($sId)) {
                $oCommentType = $oComment->GetFieldPkgCommentType();
                if ($oCommentType && !empty($oCommentType->sqlData['pkg_comment_class_name'])) {
                    $aRow = $oComment->sqlData;
                    $sClassName = $oCommentType->sqlData['pkg_comment_class_name'];
                    $oComment = new $sClassName();
                    $oComment->LoadFromRow($aRow);
                }
                $aInstance[$sInstanceIdent] = $oComment;

                return $oComment;
            } else {
                return null;
            }
        } else {
            return $aInstance[$sInstanceIdent];
        }
    }

    /**
     * Checks if the comment is active comment.
     *
     * @return bool
     */
    public function IsActiveComment()
    {
        $bIsActiveComment = false;
        $oGlobal = TGlobal::instance();
        $iActiveCommentId = $oGlobal->GetUserData(TdbPkgComment::URL_NAME_ID);
        if ($iActiveCommentId == $this->id) {
            $bIsActiveComment = true;
        }

        return $bIsActiveComment;
    }

    /**
     * Function returns the action for the active comment.
     * If no action was defined function returns re comment. This is needed for compatibility to older version
     * which not know the url parameter.
     *
     * @return string|bool
     */
    public function GetActionModeForActiveComment()
    {
        $oGlobal = TGlobal::instance();
        $sActionMode = false;
        if ($this->IsActiveComment()) {
            $sAction = $oGlobal->GetUserData(self::URL_ACTION_PARAMETER);
            if (!empty($sAction) && in_array($sAction, $this->aActionModes)) {
                $sActionMode = $sAction;
            } else {
                $sActionMode = 'recomment';
            }
        }

        return $sActionMode;
    }

    /**
     * Returns link to re comment a comment.
     * Returns a valid url only if user is logged in or guest can write comments was set to true in module config and
     * comment was not marked as deleted.
     *
     * note: You can use this function only if the view was rendered from module MTPkgCommentCore
     *
     * @param bool|int $iCommentPage
     * @param bool     $bUseFullURL
     *
     * @return bool|string
     */
    public function GetURLToReComment($iCommentPage = false, $bUseFullURL = false)
    {
        $url = false;
        if ($this->ReCommentIsAllowed()) {
            $aUrlData = $this->GetCommentURLData($iCommentPage);
            $aUrlData[self::URL_ACTION_PARAMETER] = $this->aActionModes[1];
            $activePageService = $this->getActivePageService();
            if (true === $bUseFullURL) {
                $url = $activePageService->getLinkToActivePageAbsolute($aUrlData, array('module_fnc'));
            } else {
                $url = $activePageService->getLinkToActivePageRelative($aUrlData, array('module_fnc'));
            }
            $url .= '#'.$this->getUrlNormalizationUtil()->normalizeUrl(TGlobal::Translate(self::URL_NAME_JUMPER));
        }

        return $url;
    }

    /**
     * Returns url to edit the comment. Returns a valid url only if user is logged in
     * and comment was written from logged in user else returns false.
     *
     * @param bool|int $iCommentPage
     * @param bool     $bUseFullURL
     *
     * @return bool|string
     */
    public function GetURLToEditComment($iCommentPage = false, $bUseFullURL = false)
    {
        $url = false;
        if ($this->ChangingCommentIsAllowed()) {
            $aUrlData = $this->GetCommentURLData($iCommentPage);
            $aUrlData[self::URL_ACTION_PARAMETER] = $this->aActionModes[0];
            $activePageService = $this->getActivePageService();
            if (true === $bUseFullURL) {
                $url = $activePageService->getLinkToActivePageAbsolute($aUrlData, array('module_fnc'));
            } else {
                $url = $activePageService->getLinkToActivePageRelative($aUrlData, array('module_fnc'));
            }
            $url .= '#'.$this->getUrlNormalizationUtil()->normalizeUrl(TGlobal::Translate(self::URL_NAME_JUMPER));
        }

        return $url;
    }

    /**
     * Returns the url to delete the comment. Returns a valid url only if user is logged in
     * and comment was written from logged in user else returns false.
     *
     * note: You can use this function only if the view was rendered from module MTPkgCommentCore.
     *
     * @param bool $iCommentPage
     * @param bool $bUseFullURL
     *
     * @return bool|string
     */
    public function GetURLToDeleteComment($iCommentPage = false, $bUseFullURL = false)
    {
        $sUrl = false;
        if ($this->ChangingCommentIsAllowed()) {
            $aUrlData = $this->GetCommentURLData($iCommentPage);
            $sUrl = TTools::GetExecuteMethodOnCurrentModuleURL('DeleteComment', $aUrlData, $bUseFullURL);
        }

        return $sUrl;
    }

    /**
     * Returns the url to report the comment.
     *
     * @param bool $iCommentPage
     * @param bool $bUseFullURL
     *
     * @return string
     */
    public function GetURLToReportComment($iCommentPage = false, $bUseFullURL = false)
    {
        $aUrlData = array();
        $aUrlData['commentid'] = $this->id;
        if (is_numeric($iCommentPage)) {
            $aUrlData[TGlobal::Translate(self::URL_NAME_ID_PAGE)] = $iCommentPage;
        }
        $sUrl = TTools::GetExecuteMethodOnCurrentModuleURL('ReportComment', $aUrlData, $bUseFullURL);

        return $sUrl;
    }

    /**
     * Check if its allowed to re comment the comment.
     * Its allowed if user is logged in or guest can write comments was set to true in module config
     * and comment was not marked as deleted.
     *
     * @return bool
     */
    protected function ReCommentIsAllowed()
    {
        $bReCommentIsAllowed = true;
        $oGlobal = TGlobal::instance();
        $oModule = $oGlobal->GetExecutingModulePointer();
        $oUser = TdbDataExtranetUser::GetInstance();
        if ($oModule instanceof MTPkgCommentCore) {
            $oModuleConfig = $oModule->GetConfig();
            if (!$oModuleConfig->AllowGuestComments() && !$oUser->IsLoggedIn()) {
                $bReCommentIsAllowed = false;
            }
        }
        if ($this->fieldMarkAsDeleted) {
            $bReCommentIsAllowed = false;
        }

        return $bReCommentIsAllowed;
    }

    /**
     * Checks if user is logged in and comment was posted from this user.
     * If this is ok user can delete or edit the comment.
     *
     * @return bool
     */
    public function ChangingCommentIsAllowed()
    {
        $bEditCommentIsAllowed = true;
        $oUser = TdbDataExtranetUser::GetInstance();
        if ($oUser->IsLoggedIn()) {
            if (strval($oUser->id) != strval($this->fieldDataExtranetUserId)) {
                $bEditCommentIsAllowed = false;
            }
        } else {
            $bEditCommentIsAllowed = false;
        }
        if ($bEditCommentIsAllowed && $this->fieldMarkAsDeleted) {
            $bEditCommentIsAllowed = false;
        }

        return $bEditCommentIsAllowed;
    }

    /**
     * Returns array with url data for the comment.
     *
     * @param bool|int $iCommentPage
     *
     * @return array
     */
    protected function GetCommentURLData($iCommentPage = false)
    {
        $aUrlData = array();
        $aUrlData[TGlobal::Translate(self::URL_NAME_ID)] = $this->id;
        if (is_numeric($iCommentPage)) {
            $aUrlData[TGlobal::Translate(self::URL_NAME_ID_PAGE)] = $iCommentPage;
        }

        return $aUrlData;
    }

    /**
     * Returns all child comments for the comment.
     *
     * @param null $iLanguageId
     *
     * @return TdbPkgCommentList
     */
    public function GetChildComments($iLanguageId = null)
    {
        $oList = $this->GetFromInternalCache('CommentChildList');
        if (null === $oList) {
            $sQuery = "SELECT * FROM `pkg_comment`
                  WHERE `pkg_comment`.`item_id` = '".MySqlLegacySupport::getInstance()->real_escape_string($this->fieldItemId)."'
                    AND `pkg_comment_id`='".MySqlLegacySupport::getInstance()->real_escape_string($this->id)."'
                    AND `pkg_comment_type_id` = '".MySqlLegacySupport::getInstance()->real_escape_string($this->fieldPkgCommentTypeId)."'
               ORDER BY `pkg_comment`.`created_timestamp` DESC";
            if (null === $iLanguageId) {
                $iLanguageId = self::getLanguageService()->getActiveLanguageId();
            }
            $oList = TdbPkgCommentList::GetList();
            $oList->SetLanguage($iLanguageId);
            $oList->Load($sQuery);
            $this->SetInternalCache('CommentChildList', $oList);
        }

        return $oList;
    }

    /**
     * render the list using a given view.
     *
     * @param string $sViewName     - view name
     * @param array  $aCallTimeVars - optional parameters passed from the calling method to the view
     *
     * @return string
     */
    public function Render($sViewName, $aCallTimeVars = array())
    {
        $oView = new TViewParser();
        $oView = $this->GetAdditionalViewVariables($oView, $sViewName, $aCallTimeVars);
        $oView->AddVar('aCallTimeVars', $aCallTimeVars);

        return $oView->RenderObjectPackageView($sViewName, self::VIEW_PATH, 'Customer');
    }

    protected function GetCacheTrigger($sViewName)
    {
        $aClearCacheParameter = array();
        $aClearCacheParameter[] = array('table' => 'pkg_comment', 'id' => $this->id);
        $aClearCacheParameter[] = array('table' => 'data_extranet_user', 'id' => $this->fieldDataExtranetUserId);
        if ('report' !== $sViewName) {
            $oChildCommentList = $this->GetChildComments();
            while ($oChildComment = $oChildCommentList->Next()) {
                $aClearCacheParameter[] = array('table' => 'pkg_comment', 'id' => $oChildComment->id);
            }
            $oChildCommentList->GoToStart();
        }

        return $aClearCacheParameter;
    }

    /**
     * Get additional variable to show in view.
     *
     * @param  TViewParser
     * @param  string
     *
     * @return TViewParser
     */
    protected function GetAdditionalViewVariables($oView, $sViewName, &$aCallTimeVars)
    {
        if ('report' != $sViewName) {
            $oChildCommentList = $this->GetChildComments();
            $oView->AddVar('oChildCommentList', $oChildCommentList);
        }
        if ((array_key_exists('sAnnounceCommentLink', $aCallTimeVars) && strstr($aCallTimeVars['sAnnounceCommentLink'], 'javascript:alert')) || !array_key_exists('sAnnounceCommentLink', $aCallTimeVars)) {
            if (array_key_exists('iAktPage', $aCallTimeVars)) {
                $aCallTimeVars['sAnnounceCommentLink'] = $this->GetURLToReportComment($aCallTimeVars['iAktPage']);
            } else {
                $aCallTimeVars['sAnnounceCommentLink'] = $this->GetURLToReportComment();
            }
        }
        $oView->AddVar('oComment', $this);
        $oCommentUser = TdbDataExtranetUser::GetNewInstance();
        /** @var $oCommentUser TdbDataExtranetUser */
        $oCommentUser->Load($this->fieldDataExtranetUserId);
        $oView->AddVar('oCommentUser', $oCommentUser);

        return $oView;
    }

    /**
     * return object being commented on.
     *
     * @return TCMSRecord
     */
    public function GetCommentedObject()
    {
        $oCommentedObject = $this->GetFromInternalCache('oCommentedObject');
        if (is_null($oCommentedObject)) {
            $query = "SELECT `cms_tbl_conf`.`name`
                    FROM `cms_tbl_conf`
              INNER JOIN `pkg_comment_type` ON `cms_tbl_conf`.`id` = `pkg_comment_type`.`cms_tbl_conf_id`
                   WHERE `pkg_comment_type`.`id` = '".MySqlLegacySupport::getInstance()->real_escape_string($this->fieldPkgCommentTypeId)."'
                 ";
            if ($aTable = MySqlLegacySupport::getInstance()->fetch_assoc(MySqlLegacySupport::getInstance()->query($query))) {
                $sTableName = $aTable['name'];
                $sObjectName = TCMSTableToClass::GetClassName(TCMSTableToClass::PREFIX_CLASS, $sTableName);
                $oCommentedObject = call_user_func(array($sObjectName, 'GetNewInstance'));
                if (false == $oCommentedObject->Load($this->fieldItemId)) {
                    $oCommentedObject = false;
                }
            }
            $this->SetInternalCache('oCommentedObject', $oCommentedObject);
        }

        return $oCommentedObject;
    }

    /**
     * @return ActivePageServiceInterface
     */
    private function getActivePageService()
    {
        return \ChameleonSystem\CoreBundle\ServiceLocator::get('chameleon_system_core.active_page_service');
    }

    /**
     * @return UrlNormalizationUtil
     */
    private function getUrlNormalizationUtil()
    {
        return \ChameleonSystem\CoreBundle\ServiceLocator::get('chameleon_system_core.util.url_normalization');
    }
}
