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

class MTPkgCommentCore extends TUserCustomModelBase
{
    /**
     * @var TdbPkgCommentModuleConfig
     */
    private $oModConf = null;

    /**
     * @var TCMSRecord
     */
    private $oActiveCommentTypeItem = null;

    /**
     * @var TdbPkgComment|null
     */
    protected $oActiveComment = null;

    /**
     * @var bool
     */
    protected $bSuppressRedirectAfterAction = false;

    /**
     * Initialize the module
     * Get module config and active comment.
     */
    public function Init()
    {
        parent::Init();
        $this->GetConfig();
        $this->GetActiveComment();
    }

    /**
     * @param TdbPkgCommentModuleConfig $oModuleConfiguration
     * @return void
     */
    public function SetModuleConfig($oModuleConfiguration)
    {
        $this->oModConf = $oModuleConfiguration;
    }

    /**
     * method allows us to inject an object that we want to comment on.
     *
     * @param TCMSRecord $oActiveCommentTypeItem
     *
     * @return void
     */
    public function SetActiveCommentTypeItem($oActiveCommentTypeItem)
    {
        $this->oActiveCommentTypeItem = $oActiveCommentTypeItem;
    }

    /**
     * @param bool $bSuppressRedirectAfterAction
     * @return void
     */
    public function SetSuppressRedirectAfterAction($bSuppressRedirectAfterAction)
    {
        $this->bSuppressRedirectAfterAction = $bSuppressRedirectAfterAction;
    }

    /**
     * Returns the active comment.
     *
     * @return TdbPkgComment
     */
    protected function GetActiveComment()
    {
        if (is_null($this->oActiveComment)) {
            $this->oActiveComment = TPkgComment::GetInstance();
        }

        return $this->oActiveComment;
    }

    public function &Execute()
    {
        parent::Execute();
        $sWhatToShow = $this->aModuleConfig['view'];
        $this->data['oModconf'] = $this->GetConfig();
        $this->data['bAllowGuestComments'] = $this->data['oModconf']->AllowGuestComments();
        if ('report' == $sWhatToShow) {
            $this->ExecuteReportView();
        } else {
            $this->ExecuteStandardView();
        }

        return $this->data;
    }

    /**
     * Execute for view report.
     *
     * @return void
     */
    protected function ExecuteReportView()
    {
        $this->data['oComment'] = $this->GetActiveComment();
        $this->data['oActiveArticle'] = $this->GetActiveCommentTypeItem();
    }

    /**
     * Execute for view standard.
     *
     * @return void
     */
    protected function ExecuteStandardView()
    {
        $oActiveComment = $this->GetActiveComment();
        $this->data['oActiveArticle'] = $this->GetActiveCommentTypeItem();
        $sActiveCommentId = false;
        $sAction = false;
        if (null !== $oActiveComment) {
            $sActiveCommentId = $oActiveComment->id;
            $sAction = $oActiveComment->GetActionModeForActiveComment();
        }
        $this->data['oResponseComment'] = $oActiveComment;
        $this->data['sResponseId'] = $sActiveCommentId;
        $this->data['sAction'] = $sAction;
    }

    /**
     * Get the module config.
     *
     * @return TdbPkgCommentModuleConfig
     */
    public function GetConfig()
    {
        if (is_null($this->oModConf)) {
            $this->oModConf = TdbPkgCommentModuleConfig::GetNewInstance();
            if (!$this->oModConf->LoadFromField('cms_tpl_module_instance_id', $this->instanceID)) {
                $this->oModConf = null;
            }
        }

        return $this->oModConf;
    }

    /**
     * Validate input data and write the comment to database.
     * If you set parameter $sCommentId this comment will be saved with new comment.
     *
     * @param string $sCommentId
     *
     * @return TCMSstdClass|false
     */
    public function WriteComment($sCommentId = null)
    {
        $oUser = TdbDataExtranetUser::GetInstance();
        $oModuleConfig = $this->GetConfig();
        $oGlobal = TGlobal::instance();
        $aData = array();
        $aData['comment'] = $oGlobal->GetUserData('commentsavetext');
        if ($oModuleConfig) {
            $aData['pkg_comment_type_id'] = $oModuleConfig->fieldPkgCommentTypeId;
        }
        $aData['item_id'] = '';
        if ($oGlobal->UserDataExists('objectid')) {
            $aData['item_id'] = $oGlobal->GetUserData('objectid');
        }
        if (empty($aData['item_id'])) {
            $oActiveItem = $this->GetActiveCommentTypeItem();
            if ($oActiveItem) {
                $aData['item_id'] = $oActiveItem->id;
            }
        }

        if (!is_null($sCommentId)) {
            $aData['id'] = $sCommentId;
        }
        $aData = $this->AddCustomDataToCommentBeforeSave($aData);
        $bDoSave = $this->ValidateCommentData($aData);
        $oNewComment = false;
        if ($bDoSave) {
            $aData['data_extranet_user_id'] = $oUser->id;
            if (is_null($sCommentId)) {
                $aData['created_timestamp'] = date('Y-m-d H:i:s');
            }
            $oTableManager = TTools::GetTableEditorManager('pkg_comment', $sCommentId);
            $oTableManager->AllowEditByAll(true);
            $oNewComment = $oTableManager->Save($aData);
            $oNewComment->fieldComment = $aData['comment'];
            $oTableManager->AllowEditByAll(false);
            $oNewCommentObject = TdbPkgComment::GetNewInstance($oNewComment->id);

            TCacheManager::PerformeTableChange('pkg_comment_module_config', $oModuleConfig->id);

            $this->CommentSaveSuccessHook($oNewCommentObject, $aData);
        }
        $iPage = $oGlobal->GetUserData(TdbPkgComment::URL_NAME_ID_PAGE);
        $aAddURLParameter = array();
        if (!empty($iPage)) {
            $aAddURLParameter[TdbPkgComment::URL_NAME_ID_PAGE] = $iPage;
        }
        $this->RedirectToItemPage($aAddURLParameter);

        return $oNewComment;
    }

    /**
     * hook is called if the comment save was a success.
     *
     * @param TdbPkgComment $oComment
     * @param array $aRawData
     *
     * @return void
     */
    protected function CommentSaveSuccessHook($oComment, $aRawData)
    {
        $oConfig = &TCMSConfig::GetInstance();
        $oConfigParameter = $oConfig->GetConfigParameter('dbversion-pkgComActivityFeed', false, true);
        if (!is_null($oConfigParameter)) {
            TdbPkgComActivityFeedObject::AddActivity($oComment);
        }
        $oMessageManager = TCMSMessageManager::GetInstance();
        $oMessageManager->AddMessage(TdbPkgComment::MESSAGE_CONSUMER_NAME.$oComment->fieldItemId, 'commentsave');
    }

    /**
     * use this method to modify data of a comment before saving it, e.g. when
     * you have custom fields for you comment you need to fill.
     *
     * @param array<string, mixed> $aData
     * @return array<string, mixed>
     */
    protected function AddCustomDataToCommentBeforeSave($aData)
    {
        return $aData;
    }

    /**
     * Validates user input data for comment.
     *
     * @param array $aData
     *
     * @return bool
     */
    protected function ValidateCommentData($aData)
    {
        $oModConf = $this->GetConfig();
        /** @var $oModConf TdbPkgCommentModuleConfig */
        $oUser = TdbDataExtranetUser::GetInstance();
        $oMessageManager = TCMSMessageManager::GetInstance();
        $bValid = true;
        if (!is_null($aData) && is_array($aData)) {
            if (!array_key_exists('item_id', $aData) || empty($aData['item_id'])) {
                $bValid = false;
                $oMessageManager->AddMessage(TdbPkgComment::MESSAGE_CONSUMER_NAME, 'commentfailure');
            }
            if ($bValid) {
                if (!array_key_exists('pkg_comment_type_id', $aData) || empty($aData['pkg_comment_type_id'])) {
                    $bValid = false;
                    $oMessageManager->AddMessage(TdbPkgComment::MESSAGE_CONSUMER_NAME.$aData['item_id'], 'commentfailure');
                }
                if (!$oModConf->AllowGuestComments() && !$oUser->IsLoggedIn()) {
                    $bValid = false;
                    $oMessageManager->AddMessage(TdbPkgComment::MESSAGE_CONSUMER_NAME.$aData['item_id'], 'commentnotloggedin');
                }
                if (!array_key_exists('comment', $aData) || empty($aData['comment'])) {
                    $bValid = false;
                    $oMessageManager->AddMessage(TdbPkgComment::MESSAGE_CONSUMER_NAME.$aData['item_id'], 'nocommenttext');
                }
            }
        } else {
            $oMessageManager->AddMessage(TdbPkgComment::MESSAGE_CONSUMER_NAME, 'commentfailure');
            $bValid = false;
        }

        return $bValid;
    }

    /**
     * Edit active comment if is allowed.
     *
     * @return TCMSstdClass|false
     */
    public function EditComment()
    {
        $oActiveComment = $this->GetActiveComment();
        if (!is_null($oActiveComment) && $this->EditCommentIsAllowed()) {
            return $this->WriteComment($oActiveComment->id);
        } else {
            return false;
        }
    }

    /**
     * Checks if user is allowed to edit a commen.
     *
     * @return bool
     */
    protected function EditCommentIsAllowed()
    {
        $bEditCommentIsAllowed = false;
        $oCommentToEdit = TdbPkgComment::GetInstance();
        if ($oCommentToEdit->ChangingCommentIsAllowed()) {
            $bEditCommentIsAllowed = true;
        }

        return $bEditCommentIsAllowed;
    }

    /**
     * If user is allowed comment will be deleted.
     *
     * @return bool
     */
    public function DeleteComment()
    {
        $oActiveComment = $this->GetActiveComment();
        $oGlobal = TGlobal::instance();
        if (!is_null($oActiveComment) && $this->EditCommentIsAllowed()) {
            $oModuleConfig = $this->GetConfig();
            $oTableManager = TTools::GetTableEditorManager('pkg_comment', $oActiveComment->id);
            if (!empty($oModuleConfig->fieldCommentOnDelete)) {
                $oActiveComment->sqlData['comment'] = $oModuleConfig->fieldCommentOnDelete;
                $oActiveComment->sqlData['mark_as_deleted'] = 1;
                $oTableManager->AllowEditByAll(true);
                $oTableManager->Save($oActiveComment->sqlData);
                $oTableManager->AllowEditByAll(false);
            } else {
                $oTableManager->AllowDeleteByAll(true);
                $oTableManager->Delete();
                $oTableManager->AllowDeleteByAll(false);
                TCacheManager::PerformeTableChange('pkg_comment_module_Config', $oModuleConfig->id);
            }

            $oMessageManager = TCMSMessageManager::GetInstance();
            $oMessageManager->AddMessage(TdbPkgComment::MESSAGE_CONSUMER_NAME.$oActiveComment->fieldItemId, 'deletecomment');
            $iPage = $oGlobal->GetUserData(TdbPkgComment::URL_NAME_ID_PAGE);
            $aAddURLParameter = array();
            if (!empty($iPage)) {
                $aAddURLParameter[TdbPkgComment::URL_NAME_ID_PAGE] = $iPage;
            }
            $this->RedirectToItemPage($aAddURLParameter);

            return true;
        } else {
            return false;
        }
    }

    /**
     * Validates input data and report comment per email.
     * Comment was marked as reported.
     *
     * @return bool
     */
    public function ReportComment()
    {
        $oGlobal = TGlobal::instance();
        $aData = array();
        $aData['reporttext'] = $oGlobal->GetUserData('reporttext');
        $aData['commentid'] = $oGlobal->GetUserData('commentid');
        $bSendReport = $this->ValidateReportData($aData);
        $oComment = TdbPkgComment::GetNewInstance();
        if ($bSendReport && $oComment->Load($aData['commentid'])) {
            $this->SendEMail($aData['commentid'], $aData['reporttext']);
            if (property_exists($oComment, 'fieldMarkAsReported')) {
                $oComment->sqlData['mark_as_reported'] = '1';
                $oTableManager = TTools::GetTableEditorManager('pkg_comment', $aData['commentid']);
                $oTableManager->AllowEditByAll(true);
                $oTableManager->Save($oComment->sqlData);
                $oTableManager->AllowEditByAll(false);
            }
            $oMessageManager = TCMSMessageManager::GetInstance();
            $oMessageManager->AddMessage(TdbPkgComment::MESSAGE_CONSUMER_NAME.$aData['commentid'], 'commentreport');
        }
        $iPage = $oGlobal->GetUserData(TdbPkgComment::URL_NAME_ID_PAGE);
        $aAddURLParameter = array();
        if (!empty($iPage)) {
            $aAddURLParameter[TdbPkgComment::URL_NAME_ID_PAGE] = $iPage;
        }
        $this->RedirectToItemPage($aAddURLParameter);

        return $bSendReport;
    }

    /**
     * Validate input for report comment.
     *
     * @param array $aData
     *
     * @return bool
     */
    protected function ValidateReportData($aData)
    {
        $oModConf = $this->GetConfig();
        /** @var $oModConf TdbPkgCommentModuleConfig */
        $oUser = TdbDataExtranetUser::GetInstance();
        $oMessageManager = TCMSMessageManager::GetInstance();
        $bValid = true;
        if (!is_null($aData) && is_array($aData)) {
            if (!array_key_exists('commentid', $aData) || empty($aData['commentid'])) {
                $bValid = false;
                $oMessageManager->AddMessage(TdbPkgComment::MESSAGE_CONSUMER_NAME, 'commentfailure');
            }
            if ($bValid) {
                $oCommentToRespond = TdbPkgComment::GetNewInstance();
                if ($oCommentToRespond->Load($aData['commentid'])) {
                    if ($oCommentToRespond->fieldMarkAsDeleted) {
                        $bValid = false;
                        $oMessageManager->AddMessage(TdbPkgComment::MESSAGE_CONSUMER_NAME.$aData['commentid'], 'commentactionnotallowed');
                    }
                } else {
                    $oMessageManager->AddMessage(TdbPkgComment::MESSAGE_CONSUMER_NAME, 'commentfailure');
                    $bValid = false;
                }
                if (!$oModConf->fieldGuestCanSeeComments && !$oUser->IsLoggedIn()) {
                    $bValid = false;
                    $oMessageManager->AddMessage(TdbPkgComment::MESSAGE_CONSUMER_NAME.$aData['commentid'], 'commentnotloggedin');
                }
                if (!$oModConf->fieldUseSimpleReporting) {
                    if (!array_key_exists('reporttext', $aData) || empty($aData['reporttext'])) {
                        $bValid = false;
                        $oMessageManager->AddMessage(TdbPkgComment::MESSAGE_CONSUMER_NAME.$aData['commentid'], 'noReportttext');
                    }
                }
            }
        } else {
            $oMessageManager->AddMessage(TdbPkgComment::MESSAGE_CONSUMER_NAME, 'commentfailure');
            $bValid = false;
        }

        return $bValid;
    }

    /**
     * Redirect to comment item page and set anchor to comment.
     *
     * @param array $aAddParameter
     *
     * @return void
     */
    protected function RedirectToItemPage($aAddParameter = array())
    {
        if (!$this->bSuppressRedirectAfterAction) {
            $oActivePage = $this->getActivePageService()->getActivePage();
            $this->getRedirect()->redirect($oActivePage->GetRealURLPlain($aAddParameter).'#'.TPkgComment::URL_NAME_JUMPER);
        }
    }

    /**
     * Sends email with comment report data.
     *
     * @param string $sCommentId
     * @param string $sReportText
     *
     * @return void
     */
    protected function SendEMail($sCommentId, $sReportText)
    {
        $oMail = TdbDataMailProfile::GetProfile('reportcomment');
        if (is_null($oMail)) {
            $oMsgManager = TCMSMessageManager::GetInstance();
            $oMsgManager->AddMessage(MTExtranetCore::MSG_CONSUMER_NAME, 'ERROR-MAIL-PROFILE-NOT-DEFINED', array('name' => 'registration'));
        } else {
            $oReportUser = TdbDataExtranetUser::GetInstance();
            if ($oReportUser->fieldLastname || $oReportUser->fieldFirstname) {
                $oMail->ChangeFromAddress($oReportUser->fieldName, utf8_decode($oReportUser->fieldLastname.' '.$oReportUser->fieldFirstname));
            } elseif ($oReportUser->fieldAliasName) {
                $oMail->ChangeFromAddress($oReportUser->fieldName, $oReportUser->fieldAliasName);
            } else {
                $oMail->ChangeFromAddress($oReportUser->fieldName, '');
            }
            $ReportComment = TdbPkgComment::GetNewInstance();
            $ReportComment->Load($sCommentId);
            $oMail->AddData('commentid', $sCommentId);
            $oMail->AddData('commenttext', $ReportComment->fieldComment);
            $oMail->AddData('commentreport', $sReportText);
            $oMail->AddData('name', $oReportUser->fieldName);
            $oMail->SendUsingObjectView('emails', 'Customer');
        }
    }

    /**
     * If user is allowed to re comment a comment validate input data and create
     * a child comment.
     *
     * @return bool
     */
    public function RespondToComment()
    {
        $oUser = TdbDataExtranetUser::GetInstance();
        $oMessageManager = TCMSMessageManager::GetInstance();
        $oGlobal = TGlobal::instance();
        $oModuleConfig = $this->GetConfig();
        $aData = array();
        $aData['comment'] = $oGlobal->GetUserData('commentsavetext');
        $aData['pkg_comment_type_id'] = $oGlobal->GetUserData('commenttypeid');
        $aData['pkg_comment_id'] = $oGlobal->GetUserData('sresponseid');
        $aData['item_id'] = $oGlobal->GetUserData('objectid');
        $bIsValid = $this->ValidateRespondCommentData($aData);
        if ($bIsValid) {
            $aData['data_extranet_user_id'] = $oUser->id;
            $aData['created_timestamp'] = date('Y-m-d H:i:s');
            $oTableManager = TTools::GetTableEditorManager('pkg_comment', null);
            $oTableManager->AllowEditByAll(true);
            $oNewComment = $oTableManager->Save($aData);
            $oTableManager->AllowEditByAll(false);

            $oConfig = &TCMSConfig::GetInstance();
            $oConfigParameter = $oConfig->GetConfigParameter('dbversion-pkgComActivityFeed', false, true);
            if (!is_null($oConfigParameter)) {
                $oNewCommentObject = TdbPkgComment::GetNewInstance($oTableManager->sId);
                TdbPkgComActivityFeedObject::AddActivity($oNewCommentObject);
            }

            TCacheManager::PerformeTableChange('pkg_comment_module_Config', $oModuleConfig->id);
            $oMessageManager->AddMessage(TdbPkgComment::MESSAGE_CONSUMER_NAME.$oNewComment->id, 'commentsave');
        }
        $iPage = $oGlobal->GetUserData(TdbPkgComment::URL_NAME_ID_PAGE);
        $aAddURLParameter = array();
        if (!empty($iPage)) {
            $aAddURLParameter[TdbPkgComment::URL_NAME_ID_PAGE] = $iPage;
        }
        $this->RedirectToItemPage($aAddURLParameter);

        return $bIsValid;
    }

    /**
     * Validates input data for re comment a comment.
     *
     * @param array $aData
     *
     * @return bool
     */
    protected function ValidateRespondCommentData($aData)
    {
        $oMessageManager = TCMSMessageManager::GetInstance();
        $oUser = TdbDataExtranetUser::GetInstance();
        $bValid = true;
        if (!is_null($aData) && is_array($aData)) {
            if (!array_key_exists('item_id', $aData) || empty($aData['item_id'])) {
                $bValid = false;
                $oMessageManager->AddMessage(TdbPkgComment::MESSAGE_CONSUMER_NAME, 'commentfailure');
            }
            if ($bValid) {
                $oCommentToRespond = TdbPkgComment::GetInstance();
                if ($oCommentToRespond->fieldMarkAsDeleted) {
                    $bValid = false;
                    $oMessageManager->AddMessage(TdbPkgComment::MESSAGE_CONSUMER_NAME.$aData['item_id'], 'commentactionnotallowed');
                }
                $oModConf = $this->GetConfig();
                if (!$oModConf->AllowGuestComments() && !$oUser->IsLoggedIn()) {
                    $bValid = false;
                    $oMessageManager->AddMessage(TdbPkgComment::MESSAGE_CONSUMER_NAME.$aData['item_id'], 'commentnotloggedin');
                }
                if (!array_key_exists('comment', $aData) || empty($aData['comment'])) {
                    $bValid = false;
                    $oMessageManager->AddMessage(TdbPkgComment::MESSAGE_CONSUMER_NAME.$aData['item_id'], 'nocommenttext');
                }
                if (!array_key_exists('pkg_comment_type_id', $aData) || empty($aData['pkg_comment_type_id'])) {
                    $bValid = false;
                    $oMessageManager->AddMessage(TdbPkgComment::MESSAGE_CONSUMER_NAME.$aData['item_id'], 'commentfailure');
                }
                if (!array_key_exists('pkg_comment_id', $aData) || empty($aData['pkg_comment_id'])) {
                    $bValid = false;
                    $oMessageManager->AddMessage(TdbPkgComment::MESSAGE_CONSUMER_NAME.$aData['item_id'], 'commentnoparent');
                }
            }
        } else {
            $oMessageManager->AddMessage(TdbPkgComment::MESSAGE_CONSUMER_NAME, 'commentfailure');
            $bValid = false;
        }

        return $bValid;
    }

    /**
     * Define interface for calling from out side.
     */
    protected function DefineInterface()
    {
        parent::DefineInterface();
        $this->methodCallAllowed[] = 'WriteComment';
        $this->methodCallAllowed[] = 'ReportComment';
        $this->methodCallAllowed[] = 'RespondToComment';
        $this->methodCallAllowed[] = 'EditComment';
        $this->methodCallAllowed[] = 'DeleteComment';
        $this->methodCallAllowed[] = 'GetRssFeed';
    }

    /**
     * @return void
     */
    public function GetRssFeed()
    {
        $oConfig = $this->GetConfig();
        $oComments = $oConfig->GetComments();
        if ($oComments->Length() > 0) {
            $oActiveItem = $this->GetActiveComment();
            $oFeed = new TCMSRssHandler();
            /** @var $oFeed TCMSRssHandler* */
            $oFeed->AddItemMappingArray(array('comment' => 'summary', 'created_timestamp' => 'updated'));
            $oFeed->SetFeedTitle($oActiveItem->GetName());
            $i = $oComments->Length();
            while ($oComment = $oComments->Next()) {
                $oComment->sqlData['name'] = TGlobal::Translate('chameleon_system_comment.text.rss_feed_comment_name', array('%number%' => $i));
                $oFeed->AddItem($oComment->sqlData);
                --$i;
            }
            $oFeed->OutputAsRss();
        }
    }

    /**
     * if this function returns true, then the result of the execute function will be cached.
     *
     * @return bool
     */
    public function _AllowCache()
    {
        return false;
    }

    /**
     * Returns the active comment item.
     *
     * @return TCMSRecord
     */
    protected function GetActiveCommentTypeItem()
    {
        if (is_null($this->oActiveCommentTypeItem)) {
            $oConfig = $this->GetConfig();
            if ($oConfig) {
                $oPkgCommentType = $oConfig->GetFieldPkgCommentType();
                if ($oPkgCommentType) {
                    $this->oActiveCommentTypeItem = $oPkgCommentType->GetActiveItem();
                }
            }
        }

        return $this->oActiveCommentTypeItem;
    }

    /**
     * @return ActivePageServiceInterface
     */
    private function getActivePageService()
    {
        return \ChameleonSystem\CoreBundle\ServiceLocator::get('chameleon_system_core.active_page_service');
    }

    /**
     * @return ICmsCoreRedirect
     */
    private function getRedirect()
    {
        return \ChameleonSystem\CoreBundle\ServiceLocator::get('chameleon_system_core.redirect');
    }
}
