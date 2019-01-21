<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * add a menu item to switch to any user if the current user has that right.
/**/
class TCMSTableEditorCMSUser extends TCMSTableEditor
{
    public function DefineInterface()
    {
        parent::DefineInterface();
        $externalFunctions = array('CopyUserRights', 'ActivateUser', 'SwitchToUser');
        $this->methodCallAllowed = array_merge($this->methodCallAllowed, $externalFunctions);
    }

    /**
     * change method so that the user name is not duplicated...
     */
    protected function OnBeforeCopy()
    {
        parent::OnBeforeCopy();

        $sUserNameBase = 'newuser';
        $sUserName = $sUserNameBase;
        $iCount = 1;
        $bNameAvailable = false;
        do {
            $query = "SELECT * FROM `cms_user` WHERE `login` = '".MySqlLegacySupport::getInstance()->real_escape_string($sUserName)."'";
            $tRes = MySqlLegacySupport::getInstance()->query($query);
            if (MySqlLegacySupport::getInstance()->num_rows($tRes) > 0) {
                $sUserName = $sUserNameBase.'-'.$iCount;
            } else {
                $bNameAvailable = true;
            }
            ++$iCount;
        } while (!$bNameAvailable);
        $this->oTable->sqlData['login'] = $sUserName;
    }

    /**
     * {@inheritdoc}
     */
    protected function GetCustomMenuItems()
    {
        parent::GetCustomMenuItems();

        if (true === $this->isSwitchToUserAllowed()) {
            $oMenuItem = new TCMSTableEditorMenuItem();
            $oMenuItem->sDisplayName = TGlobal::Translate('chameleon_system_core.table_editor_user.action_login_as_user');
            $oMenuItem->sItemKey = 'changeuser';
            $oMenuItem->sIcon = TGlobal::GetStaticURLToWebLib('/images/icons/user_go.png');

            $aParam = array(
                'pagedef' => TGlobal::instance()->GetUserData('pagedef'),
                'tableid' => $this->oTableConf->id,
                'id' => $this->sId,
                'module_fnc' => array('contentmodule' => 'SwitchToUser'),
                '_noModuleFunction' => 'true',
            );
            $sURL = PATH_CMS_CONTROLLER.'?'.TTools::GetArrayAsURLForJavascript($aParam);

            $oMenuItem->sOnClick = "document.location.href='{$sURL}';";
            $this->oMenuItems->AddItem($oMenuItem);
        }
        if (true === $this->isCopyPermissionsAllowed()) {
            $oMenuItem = new TCMSTableEditorMenuItem();
            $oMenuItem->sDisplayName = TGlobal::Translate('chameleon_system_core.table_editor_cms_user.clone_permissions');
            $oMenuItem->sItemKey = 'copyUserRights';
            $oMenuItem->sIcon = TGlobal::GetStaticURLToWebLib('/images/icons/group_go.png');
            $oMenuItem->sOnClick = 'openCopyUserRightsDialog();';
            $this->oMenuItems->AddItem($oMenuItem);
        }
        if (true === $this->isActivateUserAllowed()) {
            $oMenuItem = new TCMSTableEditorMenuItem();
            $oMenuItem->sDisplayName = TGlobal::Translate('chameleon_system_core.table_editor_cms_user.mail_login_data');
            $oMenuItem->sItemKey = 'activateUser';
            $oMenuItem->sIcon = TGlobal::GetStaticURLToWebLib('/images/icons/tick.png');
            $oMenuItem->sOnClick = 'ActivateUser();';
            $this->oMenuItems->AddItem($oMenuItem);
        }

        if (false === $this->isSaveUserAllowed()) {
            $this->oMenuItems->RemoveItem('sItemKey', 'save');
        }
        if (false === $this->isCopyUserAllowed()) {
            $this->oMenuItems->RemoveItem('sItemKey', 'copy');
        }
        if (false === $this->isDeleteUserAllowed()) {
            $this->oMenuItems->RemoveItem('sItemKey', 'delete');
        }
    }

    /**
     * @return bool
     */
    private function isSwitchToUserAllowed()
    {
        $activeUser = &TCMSUser::GetActiveUser();

        return 'www' !== $this->oTable->sqlData['login']
            && $this->sId !== $activeUser->id
            && $this->CurrentUserHasEditPermissionToThisUser()
            && $activeUser->oAccessManager->PermitFunction('cms_auto_switch_to_any_user')
        ;
    }

    /**
     * @return bool
     */
    private function isCopyPermissionsAllowed()
    {
        $activeUser = &TCMSUser::GetActiveUser();

        return $activeUser->id !== $this->sId
            && true === $activeUser->oAccessManager->user->IsAdmin()
        ;
    }

    /**
     * @return bool
     */
    private function isActivateUserAllowed()
    {
        $activeUser = &TCMSUser::GetActiveUser();

        return 'www' !== $this->oTable->sqlData['login']
            && $activeUser->id !== $this->sId
            && $this->CurrentUserHasEditPermissionToThisUser()
        ;
    }

    /**
     * @return bool
     */
    private function isSaveUserAllowed()
    {
        return $this->CurrentUserHasEditPermissionToThisUser();
    }

    /**
     * @return bool
     */
    private function isCopyUserAllowed()
    {
        /**
         * @var $target TdbCmsUser
         */
        $target = $this->oTable;

        return
            false === $target->fieldIsSystem
            && $this->CurrentUserHasEditPermissionToThisUser()
        ;
    }

    /**
     * @return bool
     */
    private function isDeleteUserAllowed()
    {
        /**
         * @var $target TdbCmsUser
         */
        $target = $this->oTable;

        return
            null !== $this->sId
            && '' !== $this->sId
            && false === $target->fieldIsSystem
            && $this->hasActiveUserDeletePermissionForThisUser()
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function AllowEdit($postData = null)
    {
        $bAllowEdit = parent::AllowEdit($postData);
        $bIsCMSMode = TGlobal::IsCMSMode();
        if ($bAllowEdit) {
            if ($bIsCMSMode) {
                // need to move the id to this class to check the permission. this is a special case for the user table
                $oldId = $this->sId;
                if (!is_null($postData) && array_key_exists('id', $postData)) {
                    $this->sId = $postData['id'];
                }
                $bAllowEdit = $this->CurrentUserHasEditPermissionToThisUser();
                $this->sId = $oldId;
            } else {
                if (!$this->bAllowEditByWebUser) {
                    $bAllowEdit = false;
                }
            }
        }

        return $bAllowEdit;
    }

    /**
     * check if the current user is allowed to edit the target user.
     *
     * @return bool
     */
    protected function CurrentUserHasEditPermissionToThisUser()
    {
        if (true === $this->IsOwner()) {
            return true;
        }

        $activeUser = &TCMSUser::GetActiveUser();
        if (false === $activeUser->oAccessManager->HasEditPermission($this->oTableConf->sqlData['name'])) {
            return false;
        }

        if (false === $activeUser->oAccessManager->user->IsAdmin()) {
            // Continue only if we have an ID. If we do not, this is a new record, so the check is not needed.
            if (null !== $this->sId && !empty($this->sId)) {
                $oTargetUser = TdbCmsUser::GetNewInstance();
                $oTargetUser->Load($this->sId);
                $oTargetUser->_LoadAccessManager();
                // If the target user is an admin, and the current user is not, then we do not grant edit permission.
                if ($oTargetUser->oAccessManager->user->IsAdmin()) {
                    return false;
                }

                // Also, the user may only edit users that have at least one portal in common.
                $allowedPortals = $activeUser->GetFieldCmsPortalIdList();
                $portalsOfTargetUser = $oTargetUser->GetFieldCmsPortalIdList();
                if (count($portalsOfTargetUser) > 0
                    && 0 === count(array_intersect($allowedPortals, $portalsOfTargetUser))) {
                    return false;
                }
            }
        }

        return true;
    }

    /**
     * check if the current user is allowed to edit the target user.
     *
     * @return bool
     */
    protected function hasActiveUserDeletePermissionForThisUser()
    {
        if (true === $this->bAllowEditByAll) {
            return true;
        }
        if (true === $this->IsOwner()) {
            return false;
        }

        $activeUser = &TCMSUser::GetActiveUser();
        if (false === $activeUser->oAccessManager->HasDeletePermission($this->oTableConf->sqlData['name'])) {
            return false;
        }
        if (true === $activeUser->oAccessManager->user->IsAdmin()) {
            return true;
        }

        $oTargetUser = TdbCmsUser::GetNewInstance($this->sId);
        $oTargetUser->_LoadAccessManager();
        if ($oTargetUser->oAccessManager->user->IsAdmin()) {
            return false;
        }

        // Also, the user may only delete users that have at least one portal in common.
        $allowedPortals = $activeUser->GetFieldCmsPortalIdList();
        $portalsOfTargetUser = $oTargetUser->GetFieldCmsPortalIdList();

        return 0 === \count($portalsOfTargetUser) ||
            \count(array_intersect($allowedPortals, $portalsOfTargetUser)) > 0;
    }

    /**
     * switch from current user to another user.
     */
    public function SwitchToUser()
    {
        if (false === $this->isSwitchToUserAllowed()) {
            return;
        }
        $oUser = new TCMSUser();
        if (false === $oUser->Load($this->sId)) {
            return;
        }
        TCMSUser::ReleaseOpenLocks(TCMSUser::GetActiveUser()->id);
        $oUser->SetAsActiveUser();
        $this->getRedirect()->redirectToActivePage(array(
            'pagedef' => 'main',
            '_rmhist' => 'true',
            '_histid' => '0',
        ));
    }

    /**
     * copies the user group and role from one user to the current user.
     */
    public function CopyUserRights()
    {
        if (false === $this->isCopyPermissionsAllowed()) {
            return;
        }
        $oGlobal = TGlobal::instance();
        if ($oGlobal->UserDataExists('copyUserRightsUserID')) {
            $copyUserRightsUserID = $oGlobal->GetUserData('copyUserRightsUserID');
            $oCmsUser = TdbCmsUser::GetNewInstance();
            if ($oCmsUser->Load($copyUserRightsUserID)) {
                // reset group, role and portal rights
                $this->RemoveMLTConnection('cms_usergroup_mlt');
                $this->RemoveMLTConnection('cms_role_mlt');
                $this->RemoveMLTConnection('cms_portal_mlt');

                $aUserGroups = $oCmsUser->GetMLTIdList('cms_usergroup', 'cms_usergroup_mlt');
                foreach ($aUserGroups as $sUserGroupId) {
                    $this->AddMLTConnection('cms_usergroup_mlt', $sUserGroupId);
                }

                $aRoles = $oCmsUser->GetMLTIdList('cms_role', 'cms_role_mlt');
                foreach ($aRoles as $sRoleId) {
                    $this->AddMLTConnection('cms_role_mlt', $sRoleId);
                }

                $aPortals = $oCmsUser->GetMLTIdList('cms_portal', 'cms_portal_mlt');
                foreach ($aPortals as $sPortalId) {
                    $this->AddMLTConnection('cms_portal_mlt', $sPortalId);
                }
            }
        }
    }

    /**
     * activates a user and sends them an email containing a generated password.
     */
    public function ActivateUser()
    {
        if (false === $this->isActivateUserAllowed()) {
            return;
        }

        $oMailProfile = TDataMailProfile::GetProfile('new-registration');

        if (null === $oMailProfile) {
            $oMessageManager = TCMSMessageManager::GetInstance();
            $sConsumerName = TCMSTableEditorManager::MESSAGE_MANAGER_CONSUMER;
            $sMessageCode = 'ERROR_ACTIVATE_USER';
            $oMessageManager->AddMessage($sConsumerName, $sMessageCode);

            return;
        }

        $password = TTools::GenerateNicePassword();

        $oMailProfile->AddData('login', $this->oTable->sqlData['login']);
        $oMailProfile->AddData('name', $this->oTable->sqlData['firstname']);
        $oMailProfile->AddData('email', $this->oTable->sqlData['email']);
        $oMailProfile->AddData('password', $password);
        $oMailProfile->ChangeToAddress($this->oTable->sqlData['email'], $this->oTable->sqlData['name']);

        $bValidEmail = TTools::IsValidEMail($this->oTable->sqlData['email']);
        $sMessageCode = 'ERROR_ACTIVATE_USER';
        if ($bValidEmail) {
            $oMailProfile->SendUsingObjectView('TDataMailProfile', 'Core');

            $oGlobal = TGlobal::instance();
            $aPostTmpData = $oGlobal->GetUserData();

            $postData = $this->oTable->sqlData;
            foreach ($postData as $key => $value) {
                if (array_key_exists($key, $aPostTmpData)) {
                    $postData[$key] = $aPostTmpData[$key];
                }
            }

            $postData['crypted_pw'] = $password;
            $postData['crypted_pw_check'] = $password;
            $postData['allow_cms_login'] = '1';
            $this->Save($postData);
            $sMessageCode = 'SUCCESS_ACTIVATE_USER';
        }

        $oMessageManager = TCMSMessageManager::GetInstance();
        $sConsumerName = TCMSTableEditorManager::MESSAGE_MANAGER_CONSUMER;
        $oMessageManager->AddMessage($sConsumerName, $sMessageCode);
    }

    public function GetHtmlHeadIncludes()
    {
        $aIncludes = parent::GetHtmlHeadIncludes();

        $aIncludes[] = "<script type=\"text/javascript\">
      function openCopyUserRightsDialog() {
        CreateModalDialogFromContainer('copyUserRightsDialog');
      }

      function ActivateUser()
      {
        document.cmseditform['module_fnc[contentmodule]'].value = 'ActivateUser';
        $('#cmseditform').append('<input type=\"hidden\" name=\"_noModuleFunction\" value=\"true\" />');
        document.cmseditform.submit();
      }

      </script>";

        return $aIncludes;
    }

    public function GetHtmlFooterIncludes()
    {
        $aIncludes = parent::GetHtmlFooterIncludes();

        $oGlobal = TGlobal::instance();

        $sSubmitButton = TCMSRender::DrawButton(TGlobal::Translate('chameleon_system_core.table_editor_cms_user.action_copy_permissions'), "javascript:$('#copyUserRightsForm').submit();", URL_CMS.'/images/icons/group_go.png');

        $sDialogContent = '<div id="copyUserRightsDialog" style="display:none;">
      <h2>'.TGlobal::Translate('chameleon_system_core.table_editor_cms_user.select_source_user').'</h2>
        <form name="copyUserRightsForm" id="copyUserRightsForm">
          <input type="hidden" name="pagedef" value="'.$oGlobal->GetUserData('pagedef').'" />
          <input type="hidden" name="tableid" value="'.$this->oTableConf->id.'" />
          <input type="hidden" name="id" value="'.$this->sId."\" />
          <input type=\"hidden\" name=\"module_fnc[contentmodule]\" value=\"CopyUserRights\" />
          <input type=\"hidden\" name=\"_noModuleFunction\" value=\"true\" />\n";

        $query = "SELECT * FROM `cms_user` WHERE `id` != '".MySqlLegacySupport::getInstance()->real_escape_string($this->sId)."' ORDER BY `name`, `firstname`";
        $oCmsUserList = &TdbCmsUserList::GetList($query);
        $count = 0;
        $oCmsUserList->SetPagingInfo(0, 50); // limit to at most 50 users

        $sListContent = '';

        while ($oCmsUser = &$oCmsUserList->Next()) {
            if ('www' !== $oCmsUser->fieldLogin && $oCmsUser->fieldShowAsRightsTemplate) {
                ++$count;
                $rowClass = 'oddrow';
                if ($count % 2) {
                    $rowClass = 'evenrow';
                }

                $sListContent .= '<div class="'.$rowClass.'" style="padding: 5px;">
                <div style="float: left; width: 30px;"><input type="radio" name="copyUserRightsUserID" value="'.TGlobal::OutHTML($oCmsUser->id).'"></div>
                <div style="float: left; width: 620px;">
                  <h1 style="margin: 0px; line-height: 12px;">'.$oCmsUser->GetName().'</h1>
                  ';

                $oUserGroups = $oCmsUser->GetFieldCmsUsergroupList();
                if ($oUserGroups->Length() > 0) {
                    $sListContent .= '<h2 style="line-height: 12px;">'.TGlobal::Translate('chameleon_system_core.table_editor_cms_user.user_group')."</h2>\n";
                    while ($oUserGroup = &$oUserGroups->Next()) {
                        $sListContent .= '<div style="width: 140px;" class="checkboxDIV">'.$oUserGroup->GetName().'</div>';
                    }
                }

                $oRoles = $oCmsUser->GetFieldCmsRoleList();
                if ($oRoles->Length() > 0) {
                    $sListContent .= '<div class="cleardiv">&nbsp;</div>
                  <h2 style="line-height: 12px;">'.TGlobal::Translate('chameleon_system_core.table_editor_cms_user.user_rolls').'</h2>';

                    while ($oRole = &$oRoles->Next()) {
                        $sListContent .= '<div style="width: 140px;" class="checkboxDIV">'.$oRole->GetName().'</div>';
                    }
                }

                $oPortals = $oCmsUser->GetFieldCmsPortalList();
                if ($oPortals->Length() > 0) {
                    $sListContent .= '<div class="cleardiv">&nbsp;</div>
                  <h2 style="line-height: 12px;">'.TGlobal::Translate('chameleon_system_core.table_editor_cms_user.portal').'</h2>';

                    while ($oPortal = &$oPortals->Next()) {
                        $sListContent .= '<div style="width: 140px;" class="checkboxDIV">'.$oPortal->GetName().'</div>';
                    }
                }

                $sListContent .= "</div>
              <div class=\"cleardiv\">&nbsp;</div>
              </div>
              <div class=\"cleardiv\">&nbsp;</div>
              \n";
            }
        }
        if (0 == $count) {
            $sSubmitButton = '';
        }
        $sDialogContent .= $sSubmitButton."<div class=\"cleardiv\" style=\"margin-bottom: 10px;\">&nbsp;</div>\n".$sListContent.'<div style="padding-top: 10px;">
          '.$sSubmitButton."
          </div>
        </form>
      </div>\n";

        $aIncludes[] = $sDialogContent;
        $aIncludes[] = '<form name="activateUserForm" id="activateUserForm">
          <input type="hidden" name="pagedef" value="'.$oGlobal->GetUserData('pagedef').'" />
          <input type="hidden" name="tableid" value="'.$this->oTableConf->id.'" />
          <input type="hidden" name="id" value="'.$this->sId."\" />
          <input type=\"hidden\" name=\"module_fnc[contentmodule]\" value=\"ActivateUser\" />
          <input type=\"hidden\" name=\"_noModuleFunction\" value=\"true\" />
          </form>\n";

        return $aIncludes;
    }

    /**
     * returns true if the current cms user is the owner of the record.
     *
     * @param array $aPostData - used if $this->oTable is null (happens on insert)
     *
     * @return bool
     */
    public function IsOwner($aPostData = null)
    {
        if ($this->bAllowEditByAll) {
            return true;
        }
        $bIsOwner = false;
        $oCMSUser = TCMSUser::GetActiveUser();
        if (!is_null($oCMSUser) && !is_null($this->oTable) && is_array($this->oTable->sqlData)) {
            $bIsOwner = ($this->oTable->id == $oCMSUser->id);
        } elseif (is_array($aPostData)) {
            $recId = null;
            if (array_key_exists('id', $aPostData)) {
                $recId = $aPostData['id'];
            }
            $bIsOwner = ($recId == $oCMSUser->id);
        }

        return $bIsOwner;
    }

    /**
     * {@inheritdoc}
     */
    public function Delete($sId = null)
    {
        if (false === $this->isDeleteUserAllowed()) {
            TCMSMessageManager::GetInstance()->AddMessage(
                TCMSTableEditorManager::MESSAGE_MANAGER_CONSUMER,
                'ERROR_DELETING_USER'
            );

            return;
        }

        parent::Delete($sId);
    }

    /**
     * {@inheritdoc}
     */
    protected function PrepareFieldsForSave(&$oFields)
    {
        parent::PrepareFieldsForSave($oFields);

        $roleField = $oFields->FindItemWithProperty('name', 'cms_role_mlt');
        if (false === $roleField) {
            return;
        }

        $roleField->data = $this->getRestrictedRoleChanges($roleField->data);
    }

    /**
     * Undoes any role assignments (both adding and removing) the user is not allowed to make.
     * A user that is not a CMS administrator is only allowed to modify roles they own themselves.
     *
     * @param array $rolesToSave
     *
     * @return array
     */
    private function getRestrictedRoleChanges(array $rolesToSave)
    {
        if ($this->isCurrentUserAdmin()) {
            return $rolesToSave;
        }
        $rolesBeforeSave = $this->oTablePreChangeData->GetFieldCmsRoleIdList();
        unset($rolesToSave['x']);
        $rolesToAdd = array_diff($rolesToSave, $rolesBeforeSave);
        $rolesToRemove = array_diff($rolesBeforeSave, $rolesToSave);

        if (0 === \count($rolesToAdd) && 0 === \count($rolesToRemove)) {
            $rolesToSave['x'] = '-';

            return $rolesToSave;
        }

        // Get the new lists of roles to add/remove, restricted to the allowed roles.
        $activeUser = &TCMSUser::GetActiveUser();
        $rolesOfActiveUser = $activeUser->GetFieldCmsRoleIdList();
        $rolesToAdd = array_intersect($rolesToAdd, $rolesOfActiveUser);
        $rolesToRemove = array_intersect($rolesToRemove, $rolesOfActiveUser);

        // Apply the role adding/removal.
        $rolesToSave = $rolesBeforeSave;
        $rolesToSave = array_diff($rolesToSave, $rolesToRemove);
        $rolesToSave = array_merge($rolesToSave, $rolesToAdd);
        $rolesToSave['x'] = '-';

        return $rolesToSave;
    }

    /**
     * @return bool
     */
    private function isCurrentUserAdmin()
    {
        return TCMSUser::GetActiveUser()->oAccessManager->user->IsAdmin();
    }

    /**
     * prevent empty cms_current_edit_language field on save
     * because if this field is empty the user isn't able to login to the cms backend.
     *
     * @param \TIterator  $oFields
     * @param \TCMSRecord $oPostTable
     */
    protected function PostSaveHook(&$oFields, &$oPostTable)
    {
        parent::PostSaveHook($oFields, $oPostTable);

        if (is_array($this->oTable->sqlData) && isset($this->oTable->sqlData['cms_current_edit_language']) && '' == $this->oTable->sqlData['cms_current_edit_language']) {
            $this->SaveField('cms_current_edit_language', 'de');
        }
    }

    /**
     * @return ICmsCoreRedirect
     */
    private function getRedirect()
    {
        return \ChameleonSystem\CoreBundle\ServiceLocator::get('chameleon_system_core.redirect');
    }
}
