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

class TAccessManagerGroups
{
    /**
     * a list of groups to which a user has been assigned
     * holds the translation strings as values.
     *
     * @deprecated
     *
     * @var array
     */
    public $list = array();

    /**
     * a list of groups to which a user has been assigned
     * holds the systemIdentifiers as values.
     *
     * @var array
     */
    protected $aGroupListByIdentifier = array();

    /**
     * adds a group to the user object.
     *
     * note: will overwrite the group if it exists
     *
     * @param string $id
     * @param string $name
     * @param string $identifier
     */
    public function AddGroup($id, $name, $identifier = null)
    {
        $this->list[$id] = $name;
        if (!is_null($identifier)) {
            $this->aGroupListByIdentifier[$id] = $identifier;
        } else {
            $this->aGroupListByIdentifier[$id] = 'systemIdentifier_missing';
        }
    }

    /**
     * return a comma separated list of group ids, or false if no groups assigned.
     *
     * @return string
     */
    public function GroupList()
    {
        if (count($this->aGroupListByIdentifier) < 1) {
            return false;
        }

        $databaseConnection = $this->getDatabaseConnection();

        return implode(',', array_map(array($databaseConnection, 'quote'), array_keys($this->aGroupListByIdentifier)));
    }

    /**
     * return the user group ids for the current users.
     *
     * @return array
     */
    public function GetGroupListAsArray()
    {
        return $this->aGroupListByIdentifier;
    }

    /**
     * get group id by name IF the user is in that group. else return false.
     *
     * @param string $name - translation name or systemIdentifier of the group
     *
     * @return mixed - returns group id or false
     */
    public function GetGroupId($name)
    {
        reset($this->list);
        foreach ($this->list as $id => $groupName) {
            if ($groupName == $name) {
                return $id;
            }
        }

        reset($this->aGroupListByIdentifier);
        foreach ($this->aGroupListByIdentifier as $id => $groupName) {
            if ($groupName == $name) {
                return $id;
            }
        }

        return false;
    }

    /**
     * get group id by system identifier.
     *
     * @param string $id
     *
     * @return mixed - returns group name or false
     */
    public function GetGroupSystemIdentifier($id)
    {
        if (array_key_exists($id, $this->aGroupListByIdentifier)) {
            return $this->aGroupListByIdentifier[$id];
        } else {
            return false;
        }
    }

    /**
     * returns true if the that groupId exists, else false.
     *
     * @param string $groupId
     *
     * @return bool
     */
    public function IsInGroup($groupId)
    {
        if (array_key_exists($groupId, $this->list)) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * @return Connection
     */
    private function getDatabaseConnection()
    {
        return \ChameleonSystem\CoreBundle\ServiceLocator::get('database_connection');
    }
}
