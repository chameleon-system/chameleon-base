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
     * init user and fetches all relevant rights information from database.
     *
     * @deprecated
     *
     * @param int $user_id
     */
    public function InitFromDatabase($user_id)
    {
        $query = "SELECT * FROM `cms_user` WHERE `id` = '".MySqlLegacySupport::getInstance()->real_escape_string($user_id)."'";
        if ($user = MySqlLegacySupport::getInstance()->fetch_assoc(MySqlLegacySupport::getInstance()->query($query))) {
            $this->id = $user_id;
            // get the roles
            $query = "SELECT R.*
                    FROM `cms_user_cms_role_mlt` AS MLT
              INNER JOIN `cms_role` AS R ON MLT.`target_id` = R.`id`
                   WHERE MLT.`source_id` = '".MySqlLegacySupport::getInstance()->real_escape_string($user_id)."'
                 ";
            $roles = MySqlLegacySupport::getInstance()->query($query);
            while ($role = MySqlLegacySupport::getInstance()->fetch_assoc($roles)) {
                $this->roles->AddRole($role['id'], $role['name']);
            }
            unset($roles);
            unset($role);
            // get groups...
            $query = "SELECT G.*
                    FROM `cms_user_cms_usergroup_mlt` AS MLT
              INNER JOIN `cms_usergroup` AS G ON MLT.`target_id` = G.`id`
                   WHERE MLT.`source_id` = '".MySqlLegacySupport::getInstance()->real_escape_string($user_id)."'
                 ";
            $groups = MySqlLegacySupport::getInstance()->query($query);
            while ($group = MySqlLegacySupport::getInstance()->fetch_assoc($groups)) {
                $this->groups->AddGroup($group['id'], $group['name'], $group['internal_identifier']);
            }

            // fetch portals
            if (array_key_exists('cms_portal_mlt', $user)) {
                $query = "SELECT `cms_portal`.*
                      FROM `cms_user_cms_portal_mlt`
                 LEFT JOIN `cms_portal` ON `cms_user_cms_portal_mlt`.`target_id` = `cms_portal`.`id`
                     WHERE `cms_user_cms_portal_mlt`.`source_id` = '".MySqlLegacySupport::getInstance()->real_escape_string($user_id)."'
                   ";

                $oActiveUser = &TCMSUser::GetActiveUser();
                if (!is_null($oActiveUser)) {
                    $sActiveEditPortal = $oActiveUser->GetActiveEditPortal();
                    if (!is_null($sActiveEditPortal)) {
                        $sPortalID = $sActiveEditPortal->id;
                        $query .= " AND `cms_portal`.`id` = '".MySqlLegacySupport::getInstance()->real_escape_string($sPortalID)."'";
                    }
                }

                $portals = MySqlLegacySupport::getInstance()->query($query);
                while ($portal = MySqlLegacySupport::getInstance()->fetch_assoc($portals)) {
                    $this->portals->AddPortal($portal['id'], $portal['name']);
                }
            } else {
                $this->portals->hasNoPortals = true;
            }

            // fetch edit languages
            if (array_key_exists('cms_language_mlt', $user)) {
                $query = "SELECT L.*
                      FROM `cms_user_cms_language_mlt` AS MLT
                 LEFT JOIN `cms_language` AS L ON MLT.`target_id` = L.`id`
                     WHERE MLT.`source_id` = '".MySqlLegacySupport::getInstance()->real_escape_string($user_id)."'
                   ";
                $editLanguages = MySqlLegacySupport::getInstance()->query($query);
                while ($language = MySqlLegacySupport::getInstance()->fetch_assoc($editLanguages)) {
                    $this->editLanguages->AddEditLanguage($language['id'], $language['name']);
                }
            } else {
                $this->editLanguages->hasNoEditLanguages = true;
            }
        } else {
            // user not found... throw an error.. :)
            trigger_error('User with id ['.MySqlLegacySupport::getInstance()->real_escape_string($user_id).'] does not exist!', E_USER_WARNING);
            // and logout user
            TCMSUser::Logout(); // TODO (also below) is this proper (vs normal exception)? At least log accordingly?
        }
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
