<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Doctrine\DBAL\Connection;

class TAccessManagerRoles
{
    const TAccessManagerRole_Admin = 'cms_admin';

    /**
     * a list of Roles to which a user has been assigned.
     *
     * @var array
     */
    public $list = array();

    /**
     * is set to true if one of the roles is chief_editor.
     *
     * @var bool
     */
    public $isAdmin = false;

    /**
     * will overwrite the Role if it exists.
     *
     * @param string $id
     * @param string $name
     */
    public function AddRole($id, $name)
    {
        $this->list[$id] = $name;
        if (self::TAccessManagerRole_Admin == $name) {
            $this->isAdmin = true;
        }
    }

    /**
     * get Role id by name.
     *
     * @param string $name
     *
     * @return mixed - returns role id or false
     */
    public function GetRoleId($name)
    {
        foreach ($this->list as $id => $RoleName) {
            if ($RoleName == $name) {
                return $id;
            }
        }

        return false;
    }

    /**
     * get Role name by id.
     *
     * @param string $id
     *
     * @return mixed - returns role name or false
     */
    public function GetRoleName($id)
    {
        if (array_key_exists($id, $this->list)) {
            return $this->list[$id];
        } else {
            return false;
        }
    }

    /**
     * returns true if the that RoleId exists, else false
     * accepts id or role identifier for lookup.
     *
     * @param string $RoleId
     *
     * @return bool
     */
    public function IsInRole($RoleId)
    {
        $returnVal = false;
        if (TTools::StringHasIDFormat($RoleId)) {
            if (array_key_exists($RoleId, $this->list)) {
                $returnVal = true;
            }
        } else {
            if (array_search($RoleId, $this->list)) {
                $returnVal = true;
            }
        }

        return $returnVal;
    }

    /**
     * returns a comman separated list of the role IDs
     * returns false if no roles have been set.
     *
     * @return string|bool
     */
    public function RoleIds()
    {
        if (!is_array($this->list) || count($this->list) < 1) {
            return false;
        }

        $databaseConnection = $this->getDatabaseConnection();

        return implode(',', array_map(array($databaseConnection, 'quote'), array_keys($this->list)));
    }

    /**
     * @return Connection
     */
    private function getDatabaseConnection()
    {
        return \ChameleonSystem\CoreBundle\ServiceLocator::get('database_connection');
    }
}
