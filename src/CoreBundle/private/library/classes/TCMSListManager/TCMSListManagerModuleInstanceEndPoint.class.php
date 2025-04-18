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
use Doctrine\DBAL\Connection;

class TCMSListManagerModuleInstanceEndPoint extends TCMSListManagerFullGroupTable
{
    /**
     * array of allowed modules for the current spot.
     */
    public ?array $aPermittedModules = null;

    /**
     * {@inheritdoc}
     */
    public function Init($oTableConf)
    {
        $tableConf = TdbCmsTblConf::GetNewInstance();
        $tableConf->LoadFromField('name', 'cms_tpl_module_instance');
        parent::Init($tableConf);
    }

    public function _AddFunctionColumn()
    {
    }

    public function AddFields()
    {
        parent::AddFields();
        $jsParas = $this->_GetRecordClickJavaScriptParameters();

        ++$this->columnCount;

        $siteText = ServiceLocator::get('translator')->trans('chameleon_system_core.list_module_instance.column_name_pages');
        $this->tableObj->AddHeaderField(['id' => $siteText.'&nbsp;&nbsp;'], 'left', null, 1, false);
        $this->tableObj->AddColumn('id', 'left', [$this, 'CallBackTemplateEngineInstancePages'], $jsParas, 1);
    }

    /**
     * by returning false the "new entry" button in the list can be supressed.
     *
     * @return bool
     */
    public function ShowNewEntryButton()
    {
        return false;
    }

    public function _GetRecordClickJavaScriptFunctionName()
    {
        return 'LoadCMSInstance';
    }

    /**
     * Add custom joins to the query.
     *
     * @return string
     */
    protected function GetFilterQueryCustomJoins()
    {
        return '  LEFT JOIN `cms_tpl_module_cms_usergroup_mlt` ON `cms_tpl_module_instance`.`cms_tpl_module_id` = `cms_tpl_module_cms_usergroup_mlt`.`source_id`
            LEFT JOIN `cms_tpl_module_cms_portal_mlt` ON `cms_tpl_module_instance`.`cms_tpl_module_id`  = `cms_tpl_module_cms_portal_mlt`.`source_id` ';
    }

    /**
     * any custom restrictions can be added to the query by overwriting this function.
     */
    public function GetCustomRestriction()
    {
        $query = parent::GetCustomRestriction();

        /** @var SecurityHelperAccess $securityHelper */
        $securityHelper = ServiceLocator::get(SecurityHelperAccess::class);

        if (!empty($query)) {
            $query .= ' AND ';
        }
        $query .= "`cms_tpl_module`.`show_in_template_engine` = '1'";

        if (!is_null($this->aPermittedModules)) {
            $databaseConnection = $this->getDatabaseConnection();
            $permittedModulesString = implode(',', array_map([$databaseConnection, 'quote'], array_keys($this->aPermittedModules)));
            $query .= " AND `cms_tpl_module`.`classname` IN ($permittedModulesString)";
        }
        if (array_key_exists('sModuleRestriction', $this->tableObj->_postData) && !empty($this->tableObj->_postData['sModuleRestriction'])) {
            $query .= " AND `cms_tpl_module`.`classname` = '".MySqlLegacySupport::getInstance()->real_escape_string($this->tableObj->_postData['sModuleRestriction'])."'";
        }

        $sUserGroupRestriction = '';
        $userGroups = $securityHelper->getUser()?->getGroups();
        if (null === $userGroups) {
            $userGroups = [];
        }
        $sGroupList = implode(
            ', ',
            array_map(fn ($id) => $this->getDatabaseConnection()->quote($id),
                array_keys(
                    $userGroups
                ))
        );
        if (!empty($sGroupList)) {
            $sUserGroupRestriction = " OR `cms_tpl_module_cms_usergroup_mlt`.`target_id` IN ({$sGroupList})";
        }

        $query .= " AND (`cms_tpl_module`.`is_restricted` = '0'{$sUserGroupRestriction})";

        // add portal restrictions
        $portals = $securityHelper->getUser()?->getPortals();
        if (null === $portals) {
            $portals = [];
        }
        $sPortalList = implode(
            ', ',
            array_map(fn ($id) => $this->getDatabaseConnection()->quote($id),
                array_keys(
                    $portals
                ))
        );
        if (!empty($sPortalList)) {
            $sPortalRestriction = ' OR `cms_tpl_module_cms_portal_mlt`.`target_id` IN ('.$sPortalList.')';
        }
        $query .= ' AND (
      (SELECT COUNT(`target_id`) FROM `cms_tpl_module_cms_portal_mlt` WHERE `source_id` = `cms_tpl_module`.`id`)=0
      '.$sPortalRestriction.'
      ) ';

        return $query;
    }

    protected function AddRowPrefixFields()
    {
    }

    /**
     * add table-specific buttons to the editor (add them directly to $this->oMenuItems).
     */
    protected function GetCustomMenuItems()
    {
        parent::GetCustomMenuItems();
        $this->oMenuItems->RemoveItem('sItemKey', 'deleteall');
        $this->oMenuItems->RemoveItem('sItemKey', 'edittableconf');
    }

    /**
     * returns the navigation breadcrumbs of the module instance.
     *
     * @param string $id
     * @param array $row
     *
     * @return string
     */
    public function CallBackTemplateEngineInstancePages($id, $row)
    {
        $query = "SELECT `cms_tpl_page`.*
                  FROM `cms_tpl_page_cms_master_pagedef_spot`
            INNER JOIN `cms_tpl_page` ON `cms_tpl_page_cms_master_pagedef_spot`.`cms_tpl_page_id` = `cms_tpl_page`.`id`
                 WHERE `cms_tpl_page_cms_master_pagedef_spot`.`cms_tpl_module_instance_id` = '".MySqlLegacySupport::getInstance()->real_escape_string($id)."'
              ORDER BY `cms_tpl_page`.`tree_path_search_string`
               ";
        $pageString = '';

        $oCmsTplPageList = TdbCmsTplPageList::GetList($query);
        while ($oCmsTplPage = $oCmsTplPageList->Next()) {
            $path = $this->getBreadcrumbsFromPaths($oCmsTplPage->fieldTreePathSearchString);
            if (empty($path)) {
                $path = ServiceLocator::get('translator')->trans('chameleon_system_core.list_module_instance.no_usages_found_in_navigation_node');
            }
            $pageString .= '<div class="font-weight-bold">'.TGlobal::OutHTML($oCmsTplPage->fieldName).' (ID '.TGlobal::OutHTML($oCmsTplPage->id).'):</div>
            <div>'.$path.'</div>';
        }

        return $pageString;
    }

    private function getBreadcrumbsFromPaths(string $paths): string
    {
        $renderedPaths = '';
        $pathElementList = explode(' ', $paths);
        foreach ($pathElementList as $path) {
            $path = ltrim($path, '/');

            $treeSubPath = str_replace('/', '</li><li class="breadcrumb-item">', $path);
            $renderedPaths .= sprintf('<ol class="breadcrumb p-1 mb-0"><li class="breadcrumb-item"><i class="fas fa-sitemap"></i></li><li class="breadcrumb-item">%s</li></ol>', $treeSubPath);
        }

        return $renderedPaths;
    }

    private function getDatabaseConnection(): Connection
    {
        return ServiceLocator::get('database_connection');
    }
}
