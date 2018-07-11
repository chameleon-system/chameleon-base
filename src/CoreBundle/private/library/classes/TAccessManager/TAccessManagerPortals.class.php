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
 * a list of Portals to which a user has been assigned.
/**/
class TAccessManagerPortals
{
    /**
     * portal list - key = portal id, value = portal name.
     *
     * @var array
     */
    public $list = array();

    /**
     * set to true if the user has no portal field in the db.
     *
     * @var bool
     */
    public $hasNoPortals = false;

    /**
     * will overwrite the Portal if it exists.
     *
     * @param string $id
     * @param string $name
     */
    public function AddPortal($id, $name)
    {
        $this->list[$id] = $name;
    }

    /**
     * Returns a comma-separated list of portal IDs enclosed in apostrophes, or false if no portals are assigned.
     *
     * @return string|bool - string with comma-separated IDs, or false
     */
    public function PortalList()
    {
        if (count($this->list) < 1) {
            return false;
        } else {
            $aList = TTools::MysqlRealEscapeArray($this->list);

            return "'".implode("','", array_keys($aList))."'";
        }
    }

    /**
     * get portal id by name.
     *
     * @param string $name
     *
     * @return mixed - portal id or false
     */
    public function GetPortalId($name)
    {
        foreach ($this->list as $id => $PortalName) {
            if ($PortalName == $name) {
                return $id;
            }
        }

        return false;
    }

    /**
     * get portal name by id.
     *
     * @param string $id
     *
     * @return mixed - portal name or false
     */
    public function GetPortalName($id)
    {
        if (array_key_exists($id, $this->list)) {
            return $this->list[$id];
        } else {
            return false;
        }
    }

    /**
     * returns true if the that PortalId exists, else false.
     *
     * @param string $PortalId
     *
     * @return bool
     */
    public function IsInPortal($PortalId)
    {
        if (array_key_exists($PortalId, $this->list)) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * returns the first portal from the list, or false if list is empty.
     *
     * @return mixed - returns portal id or false
     */
    public function GetFirstPortal()
    {
        if (count($this->list) > 0) {
            reset($this->list);
            $tmp = array_keys($this->list);

            return current($tmp);
        } else {
            return false;
        }
    }
}
