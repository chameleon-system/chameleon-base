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
 * container plus functions to manage user rights.
/**/
class TAccessManagerUser
{
    /**
     * user id.
     *
     * @var string
     */
    public $id = null;

    /**
     * user groups.
     *
     * @var TAccessManagerGroups
     */
    public $groups = null;

    /**
     * user roles.
     *
     * @var TAccessManagerRoles
     */
    public $roles = null;

    /**
     * the portals the user is a member of.
     *
     * @var TAccessManagerPortals
     */
    public $portals = null;

    /**
     * the languages the user can edit.
     *
     * @var TAccessManagerEditLanguages
     */
    public $editLanguages = null;

    public function __construct()
    {
        $this->groups = new TAccessManagerGroups();
        $this->roles = new TAccessManagerRoles();
        $this->portals = new TAccessManagerPortals();
        $this->editLanguages = new TAccessManagerEditLanguages();
    }

    /**
     * init user from tdb object and fetches all relevant rights information.
     *
     * @param TCMSUser $oCmsUser - note this may TCMSUser or TdbCmsUser, so only use TCMSUser methods/properties
     */
    public function InitFromObject(&$oCmsUser = null)
    {
        if (!is_null($oCmsUser)) {
            $this->id = $oCmsUser->id;
            if ('www' != $oCmsUser->sqlData['login']) { // www user has no rights, so for performance reasons, we can prevent some unnecessary queries here
                // get the roles
                /** $oRoles TdbCmsRolesList */
                $oRoles = $oCmsUser->GetMLT('cms_role_mlt');
                /** $oRoles TdbCmsRoles */
                while ($oRole = $oRoles->Next()) {
                    $this->roles->AddRole($oRole->id, $oRole->sqlData['name']);
                }

                // get groups
                /** $oUserGroups TdbCmsUsergroupList */
                $oUserGroups = $oCmsUser->GetMLT('cms_usergroup_mlt');
                /** $oUserGroup TdbCmsUsergroup */
                while ($oUserGroup = $oUserGroups->Next()) {
                    $this->groups->AddGroup($oUserGroup->id, $oUserGroup->sqlData['name'], $oUserGroup->sqlData['internal_identifier']);
                }

                // get portals
                /** $oPortals TdbCmsPortalList */
                $oPortals = $oCmsUser->GetMLT('cms_portal_mlt');
                /** $oPortal TdbCmsPortal */
                while ($oPortal = $oPortals->Next()) {
                    $this->portals->AddPortal($oPortal->id, $oPortal->GetName());
                }

                // get edit languages
                /** $oLanguages TdbCmsLanguageList */
                $oLanguages = $oCmsUser->GetMLT('cms_language_mlt');
                /** $oLanguages TdbCmsLanguage */
                while ($oLanguage = $oLanguages->Next()) {
                    $this->editLanguages->AddEditLanguage($oLanguage->id, $oLanguage->GetName());
                }
            }
        } else {
            // user not found... throw an error.. :)
            trigger_error('User with id ['.MySqlLegacySupport::getInstance()->real_escape_string($this->id).'] does not exist!', E_USER_WARNING);
            // and logout user
            TCMSUser::Logout();
        }
    }

    /**
     * returns true the user is a member of any of the listed groups
     * note: groups may be an array of group ids, or just one group id.
     *
     * @param mixed $groups
     *
     * @return bool
     */
    public function IsInGroups($groups)
    {
        if (!is_array($groups)) {
            $groupList = array($groups);
        } else {
            $groupList = $groups;
        }
        foreach ($groupList as $id) {
            if ($this->groups->IsInGroup($id)) {
                return true;
            }
        }

        return false;
    }

    /**
     * returns true if the user is a member of any of the listed roles
     * note: roles may be an array of role ids/identifier names , or just one role id/identifier name.
     *
     * @param array $roles
     *
     * @return bool
     */
    public function IsInRoles($roles)
    {
        if (!is_array($roles)) {
            $roleList = array($roles);
        } else {
            $roleList = $roles;
        }
        foreach ($roleList as $id) {
            if ($this->roles->IsInRole($id)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Returns true if the user is administrator (i.e. has the cms_admin role).
     *
     * @return bool
     */
    public function IsAdmin()
    {
        return $this->roles->isAdmin;
    }
}
