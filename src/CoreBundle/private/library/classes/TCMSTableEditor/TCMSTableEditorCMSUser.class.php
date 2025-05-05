<?php

use ChameleonSystem\CoreBundle\ServiceLocator;
use ChameleonSystem\CoreBundle\Util\InputFilterUtilInterface;
use ChameleonSystem\CoreBundle\Util\UrlUtil;
use ChameleonSystem\SecurityBundle\Service\SecurityHelperAccess;
use ChameleonSystem\SecurityBundle\Voter\CmsPermissionAttributeConstants;
use ChameleonSystem\SecurityBundle\Voter\CmsUserRoleConstants;
use Symfony\Contracts\Translation\TranslatorInterface;

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
 * /**/
class TCMSTableEditorCMSUser extends TCMSTableEditor
{
    private TranslatorInterface $translator;

    public function DefineInterface()
    {
        parent::DefineInterface();
        $externalFunctions = ['CopyUserRights', 'ActivateUser'];
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
        $this->translator = $this->getTranslator();

        if (true === $this->isSwitchToUserAllowed()) {
            $oMenuItem = new TCMSTableEditorMenuItem();
            $oMenuItem->setTitle($this->translator->trans('chameleon_system_core.table_editor_user.action_login_as_user'));
            $oMenuItem->sItemKey = 'changeuser';
            $oMenuItem->sIcon = 'fas fa-user-check';

            $oMenuItem->href = PATH_CMS_CONTROLLER.'?'.$this->getUrlUtil()->getArrayAsUrl(
                [
                    '_switch_user' => $this->oTable->sqlData['login'],
                ], '', '&'
            );
            $this->oMenuItems->AddItem($oMenuItem);
        }
        if (true === $this->isCopyPermissionsAllowed()) {
            $oMenuItem = new TCMSTableEditorMenuItem();
            $oMenuItem->setTitle($this->translator->trans('chameleon_system_core.table_editor_cms_user.clone_permissions'));
            $oMenuItem->sItemKey = 'copyUserRights';
            $oMenuItem->sIcon = 'fas fa-user-plus';
            $oMenuItem->sOnClick = 'openCopyUserRightsDialog();';
            $this->oMenuItems->AddItem($oMenuItem);
        }
        if (true === $this->isActivateUserAllowed()) {
            $oMenuItem = new TCMSTableEditorMenuItem();
            $oMenuItem->setTitle($this->translator->trans('chameleon_system_core.table_editor_cms_user.mail_login_data'));
            $oMenuItem->sItemKey = 'activateUser';
            $oMenuItem->sIcon = 'fas fa-check';
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

    private function getUrlUtil(): UrlUtil
    {
        return ServiceLocator::get('chameleon_system_core.util.url');
    }

    /**
     * @return bool
     */
    private function isSwitchToUserAllowed()
    {
        /** @var SecurityHelperAccess $securityHelper */
        $securityHelper = ServiceLocator::get(SecurityHelperAccess::class);
        $userId = $securityHelper->getUser()?->getId();

        return 'www' !== $this->oTable->sqlData['login']
            && $this->sId !== $userId
            && $this->CurrentUserHasEditPermissionToThisUser()
            && $securityHelper->isGranted('CMS_RIGHT_CMS_AUTO_SWITCH_TO_ANY_USER')
        ;
    }

    /**
     * @return bool
     */
    private function isCopyPermissionsAllowed()
    {
        /** @var SecurityHelperAccess $securityHelper */
        $securityHelper = ServiceLocator::get(SecurityHelperAccess::class);

        return $securityHelper->getUser()?->getId() !== $this->sId
            && true === $securityHelper->isGranted(CmsUserRoleConstants::CMS_ADMIN)
        ;
    }

    /**
     * @return bool
     */
    private function isActivateUserAllowed()
    {
        /** @var SecurityHelperAccess $securityHelper */
        $securityHelper = ServiceLocator::get(SecurityHelperAccess::class);

        return 'www' !== $this->oTable->sqlData['login']
            && $securityHelper->getUser()?->getId() !== $this->sId
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
        // owner can always edit
        if ($this->IsOwner($postData)) {
            return true;
        }

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
        /** @var SecurityHelperAccess $securityHelper */
        $securityHelper = ServiceLocator::get(SecurityHelperAccess::class);

        if (false === $securityHelper->isGranted(
            CmsPermissionAttributeConstants::TABLE_EDITOR_EDIT,
            $this->oTableConf
        )) {
            return false;
        }
        $user = $securityHelper->getUser();

        if (null === $user) {
            return false;
        }

        if (false === $securityHelper->isGranted(CmsUserRoleConstants::CMS_ADMIN)) {
            // Continue only if we have an ID. If we do not, this is a new record, so the check is not needed.
            if (null !== $this->sId && !empty($this->sId)) {
                $oTargetUser = TdbCmsUser::GetNewInstance();
                $oTargetUser->Load($this->sId);
                // If the target user is an admin, and the current user is not, then we do not grant edit permission.
                if ($oTargetUser->IsAdmin()) {
                    return false;
                }

                // Also, the user may only edit users that have at least one portal in common.
                $allowedPortals = array_keys($user->getPortals());
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
        /** @var SecurityHelperAccess $securityHelper */
        $securityHelper = ServiceLocator::get(SecurityHelperAccess::class);

        if (false === $securityHelper->isGranted(CmsPermissionAttributeConstants::TABLE_EDITOR_DELETE, $this->oTableConf->fieldName)) {
            return false;
        }

        if (true === $securityHelper->isGranted(CmsUserRoleConstants::CMS_ADMIN)) {
            return true;
        }

        if ($this->sId === $securityHelper->getUser()?->getId()) {
            // you cannot delete yourself.
            return false;
        }

        $oTargetUser = TdbCmsUser::GetNewInstance($this->sId);

        // Also, the user may only delete users that have at least one portal in common.
        $userPortals = $securityHelper->getUser()?->getPortals();
        if (null === $userPortals) {
            $userPortals = [];
        }
        $allowedPortals = array_keys($userPortals);
        $portalsOfTargetUser = $oTargetUser->GetFieldCmsPortalIdList();

        return 0 === \count($portalsOfTargetUser)
            || \count(array_intersect($allowedPortals, $portalsOfTargetUser)) > 0;
    }

    /**
     * copies the user group and role from one user to the current user.
     */
    public function CopyUserRights()
    {
        if (false === $this->isCopyPermissionsAllowed()) {
            return;
        }

        if (null !== $this->getInputFilterUtil()->getFilteredInput('copyUserRightsUserID')) {
            $copyUserRightsUserID = $this->getInputFilterUtil()->getFilteredInput('copyUserRightsUserID');
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

            $filterUtil = $this->getInputFilterUtil();
            $postData = $this->oTable->sqlData;
            foreach ($postData as $key => $value) {
                $valueOfKey = $filterUtil->getFilteredInput($key);
                if (null !== $valueOfKey) {
                    $postData[$key] = $valueOfKey;
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
        $this->translator = $this->getTranslator();

        $aIncludes[] = "<script type=\"text/javascript\">
      function openCopyUserRightsDialog() {
        CreateModalIFrameDialogFromContent($('#copyUserRightsDialog').html(), 0, 0, '".$this->translator->trans('chameleon_system_core.table_editor_cms_user.select_source_user')."');
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
        $this->translator = $this->getTranslator();

        $sDialogContent = '<div id="copyUserRightsDialog" class="d-none">
        <form name="copyUserRightsForm" id="copyUserRightsForm">
          <input type="hidden" name="pagedef" value="'.$this->getInputFilterUtil()->getFilteredInput('pagedef').'" />
          <input type="hidden" name="tableid" value="'.$this->oTableConf->id.'" />
          <input type="hidden" name="id" value="'.$this->sId."\" />
          <input type=\"hidden\" name=\"module_fnc[contentmodule]\" value=\"CopyUserRights\" />
          <input type=\"hidden\" name=\"_noModuleFunction\" value=\"true\" />\n";

        $query = "SELECT * FROM `cms_user` WHERE `id` != '".MySqlLegacySupport::getInstance()->real_escape_string($this->sId)."' ORDER BY `name`, `firstname`";
        $cmsUserList = TdbCmsUserList::GetList($query);
        $count = 0;
        $cmsUserList->SetPagingInfo(0, 50); // limit to at most 50 users

        $listContent = '';

        while ($cmsUser = $cmsUserList->Next()) {
            if ('www' !== $cmsUser->fieldLogin && $cmsUser->fieldShowAsRightsTemplate) {
                ++$count;

                $listContent .= '
                <div class="mt-5 mb-3">
                    <label>
                        <input class="" id="radioUser'.$count.'" type="radio" name="copyUserRightsUserID" value="'.TGlobal::OutHTML($cmsUser->id).'" />
                        <span class="pl-2 font-weight-bold font-xl">'.$cmsUser->GetName().'</span>
                    </label>
                </div>';

                $listContent .= '<div class="parts ml-5">';

                $userGroups = $cmsUser->GetFieldCmsUsergroupList();
                if ($userGroups->Length() > 0) {
                    $listContent .= '<div class="font-weight-bold mt-3">'.$this->translator->trans('chameleon_system_core.table_editor_cms_user.user_group')."</div>\n";
                    $listContent .= '<div class="row mt-2">';
                    while ($userGroup = $userGroups->Next()) {
                        $listContent .= '
                            <div class="col-12 col-lg-4 col-xl-3 my-1">
                                <div class="border-bottom border-right pl-2 py-1">
                                '.$userGroup->GetName().'
                                </div>
                            </div>';
                    }
                    $listContent .= '</div>';
                }

                $roles = $cmsUser->GetFieldCmsRoleList();
                if ($roles->Length() > 0) {
                    $listContent .= '<div class="font-weight-bold mt-3">'.$this->translator->trans('chameleon_system_core.table_editor_cms_user.user_rolls').'</div>';

                    $listContent .= '<div class="row mt-2">';
                    while ($role = $roles->Next()) {
                        $listContent .= '
                            <div class="col-12 col-lg-4 col-xl-3 my-1">
                                <div class="border-bottom border-right pl-2 py-1">
                                '.$role->GetName().'
                                </div>
                            </div>';
                    }
                    $listContent .= '</div>';
                }

                $portals = $cmsUser->GetFieldCmsPortalList();
                if ($portals->Length() > 0) {
                    $listContent .= '<div class="font-weight-bold font mt-3">'.$this->translator->trans('chameleon_system_core.table_editor_cms_user.portal').'</div>';

                    $listContent .= '<div class="row mt-2">';
                    while ($portal = $portals->Next()) {
                        $listContent .= '
                            <div class="col-12 col-lg-4 col-xl-3 my-1">
                                <div class="border-bottom border-right pl-2 py-1">
                                '.$portal->GetName().'
                                </div>
                            </div>';
                    }
                    $listContent .= '</div>';
                }

                $listContent .= "</div>
              \n";
            }
        }

        $submitButton = '';
        if (0 !== $count) {
            $submitButton = '<button type="submit" class="mt-4 mb-1 btn btn-primary"> <span class="far fa-clone mr-1"></span>'.$this->translator->trans('chameleon_system_core.table_editor_cms_user.action_copy_permissions').'</button>';
        }
        $sDialogContent .= $submitButton.$listContent.$submitButton."
        </form>
      </div>\n";

        $aIncludes[] = $sDialogContent;
        $aIncludes[] = '<form name="activateUserForm" id="activateUserForm">
          <input type="hidden" name="pagedef" value="'.$this->getInputFilterUtil()->getFilteredInput('pagedef').'" />
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
        /** @var SecurityHelperAccess $securityHelper */
        $securityHelper = ServiceLocator::get(SecurityHelperAccess::class);

        if (!is_null($this->oTable) && is_array($this->oTable->sqlData)) {
            $bIsOwner = ($this->oTable->id === $securityHelper->getUser()?->getId());
        } elseif (is_array($aPostData)) {
            $recId = null;
            if (array_key_exists('id', $aPostData)) {
                $recId = $aPostData['id'];
            }
            $bIsOwner = ($recId === $securityHelper->getUser()?->getId());
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
    protected function PrepareFieldsForSave($oFields)
    {
        parent::PrepareFieldsForSave($oFields);

        $roleField = $oFields->FindItemWithProperty('name', 'cms_role_mlt');
        if (false === $roleField || '' === $roleField->data) {
            return;
        }

        $roleField->data = $this->getRestrictedRoleChanges($roleField->data);
    }

    /**
     * Undoes any role assignments (both adding and removing) the user is not allowed to make.
     * A user that is not a CMS administrator is only allowed to modify roles they own themselves.
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
        /** @var SecurityHelperAccess $securityHelper */
        $securityHelper = ServiceLocator::get(SecurityHelperAccess::class);
        $roles = $securityHelper->getUser()?->getRoles();

        $rolesOfActiveUser = array_keys($roles);
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
        /** @var SecurityHelperAccess $securityHelper */
        $securityHelper = ServiceLocator::get(SecurityHelperAccess::class);

        return $securityHelper->isGranted(CmsUserRoleConstants::CMS_ADMIN);
    }

    /**
     * prevent empty cms_current_edit_language field on save
     * because if this field is empty the user isn't able to login to the cms backend.
     *
     * @param TIterator $oFields
     * @param TCMSRecord $oPostTable
     */
    protected function PostSaveHook($oFields, $oPostTable)
    {
        parent::PostSaveHook($oFields, $oPostTable);

        if (is_array($this->oTable->sqlData) && isset($this->oTable->sqlData['cms_current_edit_language']) && '' == $this->oTable->sqlData['cms_current_edit_language']) {
            $this->SaveField('cms_current_edit_language', 'de');
        }
    }

    private function getRedirect(): ICmsCoreRedirect
    {
        return ServiceLocator::get('chameleon_system_core.redirect');
    }

    private function getTranslator(): TranslatorInterface
    {
        return ServiceLocator::get('translator');
    }

    private function getInputFilterUtil(): InputFilterUtilInterface
    {
        return ServiceLocator::get('chameleon_system_core.util.input_filter');
    }
}
