<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use ChameleonSystem\CoreBundle\ServiceLocator;
use ChameleonSystem\SecurityBundle\Service\SecurityHelperAccess;

class CMSInterface extends TCMSModelBase
{
    public function Execute()
    {
        $oGlobal = TGlobal::instance();
        $iInterfaceId = $oGlobal->GetUserData('iInterfaceId');
        $this->data['aMessages'] = [];
        if ('' != $iInterfaceId) {
            $oInterface = TdbCmsInterfaceManager::GetInterfaceManagerObject($iInterfaceId);
            $oInterface->Init();
            $oInterface->RunImport();
            $this->data['aMessages'] = $oInterface->GetEventInfos(); // "oh no, my import or export failed!";
            // else echo "juhuuu! My import or export worked!";
        }

        parent::Execute();
        /** @var SecurityHelperAccess $securityHelper */
        $securityHelper = ServiceLocator::get(SecurityHelperAccess::class);

        $groups = $securityHelper->getUser()?->getGroups();
        $aGroupListUser = TTools::MysqlRealEscapeArray($groups);
        $sGroupSQL = '';
        foreach ($aGroupListUser as $sGroupId => $sGroupSystemName) {
            if ('' != $sGroupSQL) {
                $sGroupSQL .= ',';
            }
            $sGroupSQL .= "'".$sGroupId."'";
        }
        if (TTools::FieldExists('cms_interface_manager', 'restrict_to_user_groups')) {
            $sQuery = "SELECT `cms_interface_manager`.*
                   FROM `cms_interface_manager`
              LEFT JOIN `cms_interface_manager_cms_usergroup_mlt` ON `cms_interface_manager`.`id` = `cms_interface_manager_cms_usergroup_mlt`.`source_id`
                  WHERE `cms_interface_manager`.`restrict_to_user_groups` = '0'
                     OR `cms_interface_manager_cms_usergroup_mlt`.`target_id` IN ({$sGroupSQL})
               ORDER BY `cms_interface_manager`.`name`
                ";
        } else {
            $sQuery = 'SELECT `cms_interface_manager`.*
                   FROM `cms_interface_manager`
               ORDER BY `cms_interface_manager`.`name`
                ';
        }

        $this->data['oInterfaces'] = TdbCmsInterfaceManagerList::GetList($sQuery);

        return $this->data;
    }
}
