<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

class TAccessManagerExtraFunctions
{
    /**
     * a list of the extra rights a user has.
     *
     * @var array
     */
    public $rightList = array();

    /**
     * load all rights for the user from the database.
     *
     * @param TAccessManagerUser $user
     *
     * @throws ErrorException
     * @throws TPkgCmsException_Log
     */
    public function InitFromDatabase($user)
    {
        // first clear existing rights
        $this->rightList = array();
        // get a list of the roles from the user
        $userRoles = $user->roles->RoleIds();
        if (false !== $userRoles) {
            // the user has roles
            $query = "SELECT DISTINCT `cms_right`.*
                    FROM `cms_role_cms_right_mlt`
              INNER JOIN `cms_right` ON `cms_role_cms_right_mlt`.`target_id` = `cms_right`.`id`
                   WHERE `cms_role_cms_right_mlt`.`source_id` IN ({$userRoles})
                 ";
            $rights = MySqlLegacySupport::getInstance()->query($query);
            while ($right = MySqlLegacySupport::getInstance()->fetch_assoc($rights)) {
                $this->rightList[$right['id']] = $right['name'];
            }
        }
    }

    /**
     * returns true if the user holds that right.
     *
     * @param string $name
     *
     * @return bool
     */
    public function HasRight($name)
    {
        $nameList = array_values($this->rightList);
        if (in_array($name, $nameList)) {
            return true;
        } else {
            return false;
        }
    }
}
