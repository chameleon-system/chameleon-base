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
 * an interface to permissions (of a table or a given user).
/**/
class TAccessManagerPermissions
{
    /**
     * name of the table for which these permissions are set.
     *
     * @var string
     */
    protected $table = '';

    /**
     * is allowed to create new records.
     *
     * @var bool
     */
    public $new = false;

    /**
     * may delete records (from other users)
     * is always allowed to delete owned records.
     *
     * @var bool
     */
    public $delete = false;

    /**
     * may edit other users records (may always edit his own).
     *
     * @var bool
     */
    public $edit = false;

    /**
     * allow editing of records which user has not created.
     *
     * @var bool
     */
    public $showAll = false;

    /**
     * is allowed to create new language record.
     *
     * @var bool
     */
    public $newLanguage = false;

    /**
     * is allowed to publish records of this table.
     *
     * @var bool
     *
     * @deprecated since 6.2.0 - workflow is not supported anymore
     */
    public $workflowPublish = false;

    /**
     * is allowed to see all records in readonly mode.
     *
     * @var bool
     */
    public $readonly = false;

    /**
     * @deprecated since 6.3.0
     *
     * is allowed to create and load record revisions.
     *
     * @var bool
     */
    public $revisionManagement = false;

    /**
     * gets permission bits for a given table and user.
     *
     * @param string             $table
     * @param TAccessManagerUser $user
     */
    public function GetPermissionsFromDatabase($table, $user)
    {
        $cacheName = $table.'_'.$user->id;
        static $internalCache = array();
        if (array_key_exists($cacheName, $internalCache)) {
            $tableObj = $internalCache[$cacheName];
        } else {
            $query = "SELECT * FROM `cms_tbl_conf` WHERE `name` = '".MySqlLegacySupport::getInstance()->real_escape_string($table)."'";
            $tableObj = MySqlLegacySupport::getInstance()->fetch_assoc(MySqlLegacySupport::getInstance()->query($query));
            $internalCache[$cacheName] = $tableObj;
        }
        if ($tableObj) {
            // in order to have any rights at all the user has to be in the same group
            if ($user->IsInGroups($tableObj['cms_usergroup_id'])) {
                // now check each bit
                $table_id = $tableObj['id'];
                $this->new = $this->GetNewPermissionStatus($user, $table_id);
                $this->delete = $this->GetDeletePermissionStatus($user, $table_id);
                $this->edit = $this->GetEditPermissionStatus($user, $table_id);
                $this->showAll = $this->GetShowAllPermissionStatus($user, $table_id);
                $this->newLanguage = $this->GetNewLanguagePermissionStatus($user, $table_id);
                $this->readonly = $this->GetShowAllReadOnlyPermissionStatus($user, $table_id);
                $this->revisionManagement = $this->GetRevisionManagementPermissionStatus($user, $table_id);
            } else {
                $this->ResetPermissions();
            } // all false since we are not part of the group
        } else {
            trigger_error("Table with name [{$table}] does not exist!", E_USER_WARNING);
        }
    }

    /**
     * set all bits to false.
     */
    public function ResetPermissions()
    {
        $this->new = false;
        $this->delete = false;
        $this->edit = false;
        $this->showAll = false;
        $this->newLanguage = false;
        $this->readonly = false;
        $this->revisionManagement = false;
    }

    /**
     * get the permission status for the new right
     * note we assume that the user is in the same group as the table.
     *
     * @param TAccessManagerUser $oAccessManagerUser
     * @param string             $table_id
     *
     * @return bool
     */
    public function GetNewPermissionStatus($oAccessManagerUser, $table_id)
    {
        static $requestCache = array();
        if (array_key_exists($table_id, $requestCache)) {
            $role_array = $requestCache[$table_id];
        } else {
            $query = "SELECT * FROM `cms_tbl_conf_cms_role_mlt`
                     WHERE `source_id` = '".MySqlLegacySupport::getInstance()->real_escape_string($table_id)."'
                   ";
            $rolesWithNewPermission = MySqlLegacySupport::getInstance()->query($query);
            $role_array = array();
            while ($roleWithNewPermission = MySqlLegacySupport::getInstance()->fetch_assoc($rolesWithNewPermission)) {
                $role_array[] = $roleWithNewPermission['target_id'];
            }
            $requestCache[$table_id] = $role_array;
        }

        return $oAccessManagerUser->IsInRoles($role_array);
    }

    /**
     * get the permission status for the edit right
     * note we assume that the user is in the same group as the table.
     *
     * @param TAccessManagerUser $oAccessManagerUser
     * @param string             $table_id
     *
     * @return bool
     */
    public function GetEditPermissionStatus($oAccessManagerUser, $table_id)
    {
        static $requestCache = array();
        if (array_key_exists($table_id, $requestCache)) {
            $role_array = $requestCache[$table_id];
        } else {
            $query = "SELECT * FROM `cms_tbl_conf_cms_role1_mlt`
                     WHERE `source_id` = '".MySqlLegacySupport::getInstance()->real_escape_string($table_id)."'
                   ";
            $rolesWithEditPermission = MySqlLegacySupport::getInstance()->query($query);
            $role_array = array();
            while ($roleWithEditPermission = MySqlLegacySupport::getInstance()->fetch_assoc($rolesWithEditPermission)) {
                $role_array[] = $roleWithEditPermission['target_id'];
            }
            $requestCache[$table_id] = $role_array;
        }

        return $oAccessManagerUser->IsInRoles($role_array);
    }

    /**
     * get the permission status for the delete right
     * note we assume that the user is in the same group as the table.
     *
     * @param TAccessManagerUser $oAccessManagerUser
     * @param string             $table_id
     *
     * @return bool
     */
    public function GetDeletePermissionStatus($oAccessManagerUser, $table_id)
    {
        static $requestCache = array();
        if (array_key_exists($table_id, $requestCache)) {
            $role_array = $requestCache[$table_id];
        } else {
            $query = "SELECT * FROM `cms_tbl_conf_cms_role2_mlt`
                     WHERE `source_id` = '".MySqlLegacySupport::getInstance()->real_escape_string($table_id)."'
                   ";
            $rolesWithDeletePermission = MySqlLegacySupport::getInstance()->query($query);
            $role_array = array();
            while ($roleWithDeletePermission = MySqlLegacySupport::getInstance()->fetch_assoc($rolesWithDeletePermission)) {
                $role_array[] = $roleWithDeletePermission['target_id'];
            }
            $requestCache[$table_id] = $role_array;
        }

        return $oAccessManagerUser->IsInRoles($role_array);
    }

    /**
     * get the permission status for the show all right
     * note we assume that the user is in the same group as the table.
     *
     * @param TAccessManagerUser $oAccessManagerUser
     * @param string             $table_id
     *
     * @return bool
     */
    public function GetShowAllPermissionStatus($oAccessManagerUser, $table_id)
    {
        static $requestCache = array();
        if (array_key_exists($table_id, $requestCache)) {
            $role_array = $requestCache[$table_id];
        } else {
            $query = "SELECT * FROM `cms_tbl_conf_cms_role3_mlt`
                     WHERE `source_id` = '".MySqlLegacySupport::getInstance()->real_escape_string($table_id)."'
                   ";
            $rolesWithShowAllPermission = MySqlLegacySupport::getInstance()->query($query);
            $role_array = array();
            while ($roleWithShowAllPermission = MySqlLegacySupport::getInstance()->fetch_assoc($rolesWithShowAllPermission)) {
                $role_array[] = $roleWithShowAllPermission['target_id'];
            }
            $requestCache[$table_id] = $role_array;
        }

        return $oAccessManagerUser->IsInRoles($role_array);
    }

    /**
     * get the permission status for the read only show all right
     * note we assume that the user is in the same group as the table.
     *
     * @param TAccessManagerUser $oAccessManagerUser
     * @param string             $table_id
     *
     * @return bool
     */
    public function GetShowAllReadOnlyPermissionStatus($oAccessManagerUser, $table_id)
    {
        $hasRight = false;
        static $requestCache = array();
        if (array_key_exists($table_id, $requestCache)) {
            $role_array = $requestCache[$table_id];
        } else {
            $query = "SELECT * FROM `cms_tbl_conf_cms_role6_mlt`
                       WHERE `source_id` = '".MySqlLegacySupport::getInstance()->real_escape_string($table_id)."'
                     ";
            $rolesWithShowAllPermission = MySqlLegacySupport::getInstance()->query($query);
            $role_array = array();
            while ($roleWithShowAllPermission = MySqlLegacySupport::getInstance()->fetch_assoc($rolesWithShowAllPermission)) {
                $role_array[] = $roleWithShowAllPermission['target_id'];
            }
            $requestCache[$table_id] = $role_array;
        }

        $hasRight = $oAccessManagerUser->IsInRoles($role_array);

        return $hasRight;
    }

    /**
     * get the permission status for the new language record right
     * note we assume that the user is in the same group as the table.
     *
     * @param TAccessManagerUser $oAccessManagerUser
     * @param string             $table_id
     *
     * @return bool
     */
    public function GetNewLanguagePermissionStatus($oAccessManagerUser, $table_id)
    {
        $returnVal = false;

        static $requestCache = array();
        if (array_key_exists($table_id, $requestCache)) {
            $role_array = $requestCache[$table_id];
        } else {
            $role_array = array();
            $query = "SELECT * FROM `cms_tbl_conf_cms_role4_mlt`
                     WHERE `source_id` = '".MySqlLegacySupport::getInstance()->real_escape_string($table_id)."'
                   ";
            if ($rolesWithNewLanguagePermission = MySqlLegacySupport::getInstance()->query($query)) {
                while ($roleWithNewLanguagePermission = MySqlLegacySupport::getInstance()->fetch_assoc($rolesWithNewLanguagePermission)) {
                    $role_array[] = $roleWithNewLanguagePermission['target_id'];
                }
            }
            $requestCache[$table_id] = $role_array;
        }

        $returnVal = $oAccessManagerUser->IsInRoles($role_array);

        return $returnVal;
    }

    /**
     * get the permission status for the workflow publish right
     * note we assume that the user is in the same group as the table.
     *
     * @param TAccessManagerUser $oAccessManagerUser
     * @param string             $table_id
     *
     * @return bool
     *
     * @deprecated since 6.2.0 - workflow is not supported anymore
     */
    public function GetWorkflowPublishStatus($oAccessManagerUser, $table_id)
    {
        return false;
    }

    /**
     * @deprecated since 6.3.0
     *
     * get the permission status for the revision management right
     * note we assume that the user is in the same group as the table.
     *
     * @param TAccessManagerUser $oAccessManagerUser
     * @param string $table_id
     *
     * @return bool
     */
    public function GetRevisionManagementPermissionStatus($oAccessManagerUser, $table_id)
    {
        $returnVal = false;

        static $requestCache = array();
        if (array_key_exists($table_id, $requestCache)) {
            $role_array = $requestCache[$table_id];
        } else {
            $role_array = array();
            $query = "SELECT * FROM `cms_tbl_conf_cms_role7_mlt`
                       WHERE `source_id` = '".MySqlLegacySupport::getInstance()->real_escape_string($table_id)."'
                     ";
            if ($rolesWithRevisionManagementPermission = MySqlLegacySupport::getInstance()->query($query)) {
                while ($roleWithRevisionManagementPermission = MySqlLegacySupport::getInstance()->fetch_assoc($rolesWithRevisionManagementPermission)) {
                    $role_array[] = $roleWithRevisionManagementPermission['target_id'];
                }
            }
            $requestCache[$table_id] = $role_array;
        }
        $returnVal = $oAccessManagerUser->IsInRoles($role_array);

        return $returnVal;
    }
}
