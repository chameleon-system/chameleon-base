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

/**
 * generates a list of all users with roles, groups and accumulated rights.
 */
class CMSUserRightsOverview extends TModelBase
{
    public function Execute()
    {
        $this->data = parent::Execute();

        $this->data['sRightsOverview'] = $this->GetUserRightsOverview();

        return $this->data;
    }

    protected function GetUserRightsOverview()
    {
        $sRightsOverview = '';
        $oCmsUserList = TdbCmsUserList::GetList();
        /* @var $oCmsUserList TdbCmsUserList */
        $oCmsUserList->ChangeOrderBy(['`cms_user`.`name`' => 'ASC']);
        $count = 0;
        while ($oCmsUser = $oCmsUserList->Next()) {
            /** @var $oCmsUser TdbCmsUser */
            $sPageBreak = '';
            if ($count > 0) {
                $sPageBreak = ' style="page-break-before:always"';
            }
            $sRightsOverview .= '<h1'.$sPageBreak.'>'.$oCmsUser->GetName()."</h1>\n";
            ++$count;

            $sRightsOverview .= '<h2>'.ChameleonSystem\CoreBundle\ServiceLocator::get('translator')->trans('chameleon_system_core.cms_module_cms_user_rights_overview.portal')."</h2>\n";
            $oPortals = $oCmsUser->GetFieldCmsPortalList();
            while ($oPortal = $oPortals->Next()) {
                /* @var $oPortal TdbCmsPortal */
                $sRightsOverview .= $oPortal->GetName().', ';
            }

            if (', ' == substr($sRightsOverview, -2)) {
                $sRightsOverview = substr($sRightsOverview, 0, -2);
            }

            // Groups
            $aGroupIDs = [];
            $sRightsOverview .= '<h2>'.ChameleonSystem\CoreBundle\ServiceLocator::get('translator')->trans('chameleon_system_core.cms_module_cms_user_rights_overview.groups')."</h2>\n";
            $oGroups = $oCmsUser->GetFieldCmsUsergroupList();
            while ($oGroup = $oGroups->Next()) {
                /* @var $oGroup TdbCmsUserGroup */
                $sRightsOverview .= $oGroup->GetName().', ';
                $aGroupIDs[] = $oGroup->id;
            }

            $databaseConnection = $this->getDatabaseConnection();
            $groupIdListString = implode(',', array_map([$databaseConnection, 'quote'], $aGroupIDs));

            if (', ' == substr($sRightsOverview, -2)) {
                $sRightsOverview = substr($sRightsOverview, 0, -2);
            }

            // Roles
            $aRights = [];
            $sRightsOverview .= "<table>
          <tr>
            <td>\n";
            $sRightsOverview .= '<h2>'.ChameleonSystem\CoreBundle\ServiceLocator::get('translator')->trans('chameleon_system_core.cms_module_cms_user_rights_overview.rolls').'</h2>
           ';
            $oRoles = $oCmsUser->GetFieldCmsRoleList();
            while ($oRole = $oRoles->Next()) {
                /* @var $oRole TdbCmsRole */
                $sRightsOverview .= '<strong>'.$oRole->GetName().'</strong>';
                $quotedRoleId = $databaseConnection->quote($oRole->id);

                $oRights = $oRole->GetFieldCmsRightList();
                while ($oRight = $oRights->Next()) {
                    $aRights[$oRight->id] = $oRight->GetName();
                }

                // right: create new record
                $sQuery = "SELECT * FROM `cms_tbl_conf`
          LEFT JOIN `cms_tbl_conf_cms_role_mlt` ON `cms_tbl_conf_cms_role_mlt`.`source_id` = `cms_tbl_conf`.`id`
          WHERE `cms_tbl_conf_cms_role_mlt`.`target_id` = $quotedRoleId
          AND `cms_tbl_conf`.`cms_usergroup_id` IN ($groupIdListString)
          ";

                $sTableRight = '';
                $oCmsTblConfList = TdbCmsTblConfList::GetList($sQuery);
                /** @var $oCmsTblConfList TdbCmsTblConfList */
                while ($oTable = $oCmsTblConfList->Next()) {
                    $sTableRight .= $oTable->GetName().', ';
                }

                if (!empty($sTableRight)) {
                    $sRightsOverview .= '<table style="margin-left: 10px;">
            <tr>
              <td>
                <u>'.ChameleonSystem\CoreBundle\ServiceLocator::get('translator')->trans('chameleon_system_core.cms_module_cms_user_rights_overview.permission_create_new').'</u><br />
              ';
                    $sRightsOverview .= $sTableRight;
                    $sRightsOverview .= '</td>
            </tr>';
                }

                // right: change record
                $sQuery = "SELECT * FROM `cms_tbl_conf`
          LEFT JOIN `cms_tbl_conf_cms_role1_mlt` ON `cms_tbl_conf_cms_role1_mlt`.`source_id` = `cms_tbl_conf`.`id`
          WHERE `cms_tbl_conf_cms_role1_mlt`.`target_id` = $quotedRoleId
          AND `cms_tbl_conf`.`cms_usergroup_id` IN ($groupIdListString)
          ";

                $sTableRight = '';
                $oCmsTblConfList = TdbCmsTblConfList::GetList($sQuery);
                /** @var $oCmsTblConfList TdbCmsTblConfList */
                while ($oTable = $oCmsTblConfList->Next()) {
                    $sTableRight .= $oTable->GetName().', ';
                }

                if (!empty($sTableRight)) {
                    $sRightsOverview .= '<table style="margin-left: 10px;">
            <tr>
              <td>
                <u>'.ChameleonSystem\CoreBundle\ServiceLocator::get('translator')->trans('chameleon_system_core.cms_module_cms_user_rights_overview.permission_edit').'</u><br />
              ';
                    $sRightsOverview .= $sTableRight;
                    $sRightsOverview .= '</td>
            </tr>';
                }

                // right: delete record
                $sQuery = "SELECT * FROM `cms_tbl_conf`
          LEFT JOIN `cms_tbl_conf_cms_role2_mlt` ON `cms_tbl_conf_cms_role2_mlt`.`source_id` = `cms_tbl_conf`.`id`
          WHERE `cms_tbl_conf_cms_role2_mlt`.`target_id` = $quotedRoleId
          AND `cms_tbl_conf`.`cms_usergroup_id` IN ($groupIdListString)
          ";

                $sTableRight = '';
                $oCmsTblConfList = TdbCmsTblConfList::GetList($sQuery);
                /** @var $oCmsTblConfList TdbCmsTblConfList */
                while ($oTable = $oCmsTblConfList->Next()) {
                    $sTableRight .= $oTable->GetName().', ';
                }

                if (!empty($sTableRight)) {
                    $sRightsOverview .= '<table style="margin-left: 10px;">
            <tr>
              <td>
                <u>'.ChameleonSystem\CoreBundle\ServiceLocator::get('translator')->trans('chameleon_system_core.cms_module_cms_user_rights_overview.permission_delete').'</u><br />
              ';
                    $sRightsOverview .= $sTableRight;
                    $sRightsOverview .= '</td>
            </tr>';
                }

                // right: change all records
                $sQuery = "SELECT * FROM `cms_tbl_conf`
          LEFT JOIN `cms_tbl_conf_cms_role3_mlt` ON `cms_tbl_conf_cms_role3_mlt`.`source_id` = `cms_tbl_conf`.`id`
          WHERE `cms_tbl_conf_cms_role3_mlt`.`target_id` = $quotedRoleId
          AND `cms_tbl_conf`.`cms_usergroup_id` IN ($groupIdListString)
          ";

                $sTableRight = '';
                $oCmsTblConfList = TdbCmsTblConfList::GetList($sQuery);
                /** @var $oCmsTblConfList TdbCmsTblConfList */
                while ($oTable = $oCmsTblConfList->Next()) {
                    $sTableRight .= $oTable->GetName().', ';
                }

                if (!empty($sTableRight)) {
                    $sRightsOverview .= '<table style="margin-left: 10px;">
            <tr>
              <td>
                <u>'.ChameleonSystem\CoreBundle\ServiceLocator::get('translator')->trans('chameleon_system_core.cms_module_cms_user_rights_overview.permission_change_all').'</u><br />
              ';
                    $sRightsOverview .= $sTableRight;
                    $sRightsOverview .= '</td>
            </tr>';
                }

                // right: edit languages
                $sQuery = "SELECT * FROM `cms_tbl_conf`
          LEFT JOIN `cms_tbl_conf_cms_role4_mlt` ON `cms_tbl_conf_cms_role4_mlt`.`source_id` = `cms_tbl_conf`.`id`
          WHERE `cms_tbl_conf_cms_role4_mlt`.`target_id` = $quotedRoleId
          AND `cms_tbl_conf`.`cms_usergroup_id` IN ($groupIdListString)
          ";

                $sTableRight = '';
                $oCmsTblConfList = TdbCmsTblConfList::GetList($sQuery);
                /** @var $oCmsTblConfList TdbCmsTblConfList */
                while ($oTable = $oCmsTblConfList->Next()) {
                    $sTableRight .= $oTable->GetName().', ';
                }

                if (!empty($sTableRight)) {
                    $sRightsOverview .= '<table style="margin-left: 10px;">
            <tr>
              <td>
                <u>'.ChameleonSystem\CoreBundle\ServiceLocator::get('translator')->trans('chameleon_system_core.cms_module_cms_user_rights_overview.permission_translate').'</u><br />
              ';
                    $sRightsOverview .= $sTableRight;
                    $sRightsOverview .= '</td>
            </tr>';
                }

                $sRightsOverview .= '</table>';
            }
            $sRightsOverview .= '</td>
        </tr>
        </table>';

            if (', ' == substr($sRightsOverview, -2)) {
                $sRightsOverview = substr($sRightsOverview, 0, -2);
            }

            $sRightsOverview .= '<h2>'.ChameleonSystem\CoreBundle\ServiceLocator::get('translator')->trans('chameleon_system_core.cms_module_cms_user_rights_overview.headline')."</h2>\n";
            foreach ($aRights as $sRight) {
                $sRightsOverview .= $sRight.', ';
            }

            if (', ' == substr($sRightsOverview, -2)) {
                $sRightsOverview = substr($sRightsOverview, 0, -2);
            }

            // select all tables and get role rights

            $sRightsOverview .= "<hr>\n";
        }

        return $sRightsOverview;
    }

    /**
     * @return Connection
     */
    private function getDatabaseConnection()
    {
        return ChameleonSystem\CoreBundle\ServiceLocator::get('database_connection');
    }
}
