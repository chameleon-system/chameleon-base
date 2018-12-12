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
use Doctrine\DBAL\Connection;
use Symfony\Component\Translation\TranslatorInterface;

/**
 * manages the webpage list (links to the template engine interface).
 */
class TCMSListManagerCMSUser extends TCMSListManagerFullGroupTable
{
    /**
     * show only records that belong to the user (if the table contains the user id).
     */
    public function GetUserRestriction()
    {
        $query = parent::GetUserRestriction();
        $oUser = &TCMSUser::GetActiveUser();

        $restrictionQuery = '';
        if (!$oUser->oAccessManager->user->IsAdmin()) {
            // if the user is not an admin, then he may not view admin users and the public www user

            $tmpquery = "SELECT DISTINCT `cms_user_cms_role_mlt`.source_id
                       FROM `cms_user_cms_role_mlt`
                 INNER JOIN  `cms_role` ON `cms_user_cms_role_mlt`.`target_id` = `cms_role`.`id`
                      WHERE `cms_role`.`name` = 'cms_admin' OR `cms_role`.`name` = 'www'";
            $adminRes = MySqlLegacySupport::getInstance()->query($tmpquery);
            $aAdminIds = array();
            while ($aAdmin = MySqlLegacySupport::getInstance()->fetch_assoc($adminRes)) {
                $aAdminIds[] = $aAdmin['source_id'];
            }

            if (count($aAdminIds) > 0) {
                // get admin users and exclude them..
                $databaseConnection = $this->getDatabaseConnection();
                $adminIdString = implode(',', array_map(array($databaseConnection, 'quote'), $aAdminIds));
                $restrictionQuery = $this->CreateRestriction('id', "NOT IN ($adminIdString)");
            }
            if (!empty($restrictionQuery)) {
                $query .= " {$restrictionQuery}";
            }
        }

        return $query;
    }

    /**
     * shows the delete button only if user is not required from system.
     *
     * @param string $id
     * @param array  $row
     *
     * @return string
     */
    public function CallBackFunctionBlockDeleteButton($id, $row)
    {
        if (!$row['is_system']) {
            return parent::CallBackFunctionBlockDeleteButton($id, $row);
        }
        $translator = $this->getTranslator();

        return sprintf('<span title="%s" class="glyphicon glyphicon-remove" style="color: #d9534f; opacity: .5;"></span>',
            TGlobal::OutJS($translator->trans('chameleon_system_core.list.system_entry_delete_not_allowed'))
        );
    }

    /**
     * {@inheritdoc}
     */
    protected function usesManagedTables(): bool
    {
        return false;
    }

    /**
     * @return Connection
     */
    private function getDatabaseConnection()
    {
        return ServiceLocator::get('database_connection');
    }

    /**
     * @return TranslatorInterface
     */
    private function getTranslator()
    {
        return ServiceLocator::get('translator');
    }
}
