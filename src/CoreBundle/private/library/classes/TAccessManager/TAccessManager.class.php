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
 * The class provides an api which allows a system to control who may access what
 * in a database environment. It is built around a table based right management.
 ***************************************************************************/
class TAccessManager
{
    /**
     * user object.
     *
     * @var TAccessManagerUser
     */
    public $user = null;

    /**
     * the users extra function list.
     *
     * @var TAccessManagerExtraFunctions
     */
    public $functions = null;

    /**
     * used to cache permissions.
     *
     * @var array
     */
    protected $permissionCache = array();

    /**
     * a string that states which action failed.
     *
     * @var string
     */
    protected $denialString = null;

    public function __construct()
    {
        $this->user = new TAccessManagerUser();
        $this->functions = new TAccessManagerExtraFunctions();
    }

    public function __get($sParamName)
    {
        if ('denialString' == $sParamName) {
            if (is_null($this->denialString)) {
                $this->denialString = TGlobal::Translate('chameleon_system_core.error.insufficient_permissions');
            }

            return $this->denialString;
        } else {
            trigger_error('invalid property'.$sParamName.'in TAccessManager requested', E_USER_ERROR);
        }
    }

    /**
     * load the data from the database.
     *
     * @deprecated
     *
     * @param string $user_id - id of the cms user
     */
    public function InitFromDatabase($user_id)
    {
        $this->user->InitFromDatabase($user_id);
        $this->functions->InitFromDatabase($this->user);
    }

    /**
     * load the data from tdb object.
     *
     * @param TCMSUser $oTdbCMsUser
     */
    public function InitFromObject(&$oTdbCMsUser = null)
    {
        $this->user->InitFromObject($oTdbCMsUser);
        $this->functions->InitFromDatabase($this->user);
    }

    /**
     * returns a permission object for the table "$table".
     *
     * @param string $table - name of the db-table
     *
     * @return TAccessManagerPermissions|bool
     */
    public function GetTablePermissions($table)
    {
        if (null === $this->user) {
            trigger_error('User needs to be Initiated before you can get table permissions', E_USER_ERROR);

            return false;
        }

        if (isset($this->permissionCache[$table])) {
            return $this->permissionCache[$table];
        }

        $oPermissions = new TAccessManagerPermissions();
        $oPermissions->GetPermissionsFromDatabase($table, $this->user);
        $this->permissionCache[$table] = $oPermissions;

        return $oPermissions;
    }

    /**
     * checks if the user has the permission to create a new entry in "table".
     *
     * @param string $table - name of the db-table
     *
     * @return bool - returns true if the user may insert new entries into the table, else false
     */
    public function HasNewPermission($table)
    {
        if (is_null($this->user)) {
            trigger_error('User needs to be Initiated before you can get table permissions', E_USER_ERROR);

            return false;
        } else {
            // get permissions...
            $permission = $this->GetTablePermissions($table);

            return $permission->new;
        }
    }

    /**
     * checks if the user has the permission to create a new language copy entry in "table".
     *
     * @param string $table - name of the db-table
     *
     * @return bool
     */
    public function HasNewLanguagePermission($table)
    {
        if (is_null($this->user)) {
            trigger_error('User needs to be Initiated before you can get table permissions', E_USER_ERROR);

            return false;
        } else {
            // get permissions...
            $permission = $this->GetTablePermissions($table);

            return $permission->newLanguage;
        }
    }

    /**
     * checks if the user has the permission to edit an entry in "table".
     *
     * @param string $table - name of the db-table
     *
     * @return bool
     */
    public function HasEditPermission($table)
    {
        if (is_null($this->user)) {
            trigger_error('User needs to be Initiated before you can get table permissions', E_USER_WARNING);

            return false;
        } else {
            // get permissions...
            $permission = $this->GetTablePermissions($table);

            return $permission->edit;
        }
    }

    /**
     * checks if the user has the permission to delete an entry in "table".
     *
     * @param string $table - name of the db-table
     *
     * @return bool - returns true if the user is allowed to delete any entry of the table, else false
     */
    public function HasDeletePermission($table)
    {
        if (is_null($this->user)) {
            trigger_error('User needs to be Initiated before you can get table permissions', E_USER_ERROR);

            return false;
        } else {
            // get permissions...
            $permission = $this->GetTablePermissions($table);

            return $permission->delete;
        }
    }

    /**
     * checks if the user has the permission to EDIT all entries in "table".
     *
     * @param string $table - name of the db-table
     *
     * @return bool - returns true if the user may edit any entry of the table, else false
     */
    public function HasShowAllPermission($table)
    {
        if (is_null($this->user)) {
            trigger_error('User needs to be Initiated before you can get table permissions', E_USER_ERROR);

            return false;
        } else {
            // get permissions...
            $permission = $this->GetTablePermissions($table);

            return $permission->showAll;
        }
    }

    /**
     * @deprecated since 6.3.0 - revision management is no longer supported
     *
     * checks if the user has the permission to create and load record revisions.
     *
     * @param string $table - name of the db-table
     *
     * @return bool - returns true if the user may create and load record revisions
     */
    public function HasRevisionManagementPermission($table)
    {
        return false;
    }

    /**
     * checks if the user has the permission to see all entries in "table" in read only mode.
     *
     * @param string $table - name of the db-table
     *
     * @return bool - returns true if the user is allowed to open all records of the table in readonly mode, else false
     */
    public function HasShowAllReadOnlyPermission($table)
    {
        if (is_null($this->user)) {
            trigger_error('User needs to be Initiated before you can get table permissions', E_USER_ERROR);

            return false;
        } else {
            // get permissions...
            $permission = $this->GetTablePermissions($table);

            return $permission->readonly;
        }
    }

    /**
     * checks if the user has the permission to use the function "$function".
     *
     * @param string $function - name of the requested function
     *
     * @return bool
     */
    public function PermitFunction($function)
    {
        if (is_null($this->user)) {
            trigger_error('User needs to be Initiated before you can get function permissions', E_USER_ERROR);

            return false;
        } else {
            return $this->functions->HasRight($function);
        }
    }

    /**
     * checks if the user has the permission to publish all tables of the
     * action items of a transaction.
     *
     * @param string $sTransactionID - id of the transaction
     *
     * @return bool
     *
     * @deprecated since 6.2.0 - workflow is not supported anymore
     */
    public function HasWorkflowPublishPermission($sTransactionID)
    {
        return false;
    }

    /**
     * checks if the user has the permission to edit all tables of the
     * action items of a transaction.
     *
     * @param string $sTransactionID - id of the transaction
     *
     * @return bool
     *
     * @deprecated since 6.2.0 - workflow is not supported anymore
     */
    public function HasWorkflowEditPermission($sTransactionID)
    {
        return false;
    }
}
