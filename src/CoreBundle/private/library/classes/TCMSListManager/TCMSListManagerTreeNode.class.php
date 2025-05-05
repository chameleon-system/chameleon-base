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
use ChameleonSystem\SecurityBundle\Voter\CmsPermissionAttributeConstants;

class TCMSListManagerTreeNode extends TCMSListManagerFullGroupTable
{
    protected $sTreeNodeTableID;

    /**
     * returns the name of the javascript function to be called when the user clicks on a
     * record within the table.
     *
     * @return string
     */
    public function _GetRecordClickJavaScriptFunctionName()
    {
        return 'openTreeNodeConnectionEditor';
    }

    /**
     * return an array of all js, css, or other header includes that are required
     * in the cms for this field. each include should be in one line, and they
     * should always be typed the same way so that no includes are included mor than once.
     *
     * @return array
     */
    public function GetHtmlHeadIncludes()
    {
        $aIncludes = parent::GetHtmlHeadIncludes();

        if (is_null($this->sTreeNodeTableID)) {
            $this->sTreeNodeTableID = TTools::GetCMSTableId('cms_tree_node');
        }

        $sURL = PATH_CMS_CONTROLLER.'?'.TTools::GetArrayAsURLForJavascript(['tableid' => $this->sTreeNodeTableID, 'pagedef' => 'tableeditorPopup']);
        $aIncludes[] = "
        <script type=\"text/javascript\">
        function openTreeNodeConnectionEditor(id) {
          var url = '".$sURL."&id=' + id;
          document.location.href = url;
        }
        </script>
      ";

        return $aIncludes;
    }

    /**
     * add table-specific buttons to the editor (add them directly to $this->oMenuItems).
     */
    protected function GetCustomMenuItems()
    {
        parent::GetCustomMenuItems();
        $this->oMenuItems->RemoveItem('sItemKey', 'edittableconf');

        if (is_null($this->sTreeNodeTableID)) {
            $this->sTreeNodeTableID = TTools::GetCMSTableId('cms_tree_node');
        }

        /** @var SecurityHelperAccess $securityHelper */
        $securityHelper = ServiceLocator::get(SecurityHelperAccess::class);
        $tableInUserGroup = $securityHelper->isGranted(CmsPermissionAttributeConstants::TABLE_EDITOR_ACCESS, $this->oTableConf->fieldName);
        if ($tableInUserGroup) {
            if ($securityHelper->isGranted(CmsPermissionAttributeConstants::TABLE_EDITOR_NEW, $this->oTableConf->sqlData['name'])) {
                // remove old new button
                $this->oMenuItems->RemoveItem('sItemKey', 'new');

                // add new button
                $oMenuItem = new TCMSTableEditorMenuItem();
                $oMenuItem->sDisplayName = ServiceLocator::get('translator')->trans('chameleon_system_core.action.new');
                $oMenuItem->sIcon = 'fas fa-plus';
                $oMenuItem->sItemKey = 'new';

                $aParameter = ['pagedef' => 'tableeditorPopup', 'id' => $this->oTableConf->id, 'tableid' => $this->sTreeNodeTableID, 'module_fnc' => ['contentmodule' => 'Insert']];
                $aAdditionalParams = $this->GetHiddenFieldsHook();
                if (is_array($aAdditionalParams) && count($aAdditionalParams) > 0) {
                    $aParameter = array_merge($aParameter, $aAdditionalParams);
                }

                $oMenuItem->href = PATH_CMS_CONTROLLER.'?'.TTools::GetArrayAsURLForJavascript($aParameter);
                $this->oMenuItems->AddItem($oMenuItem);
            }
        }
    }

    /**
     * use this method to add field columns between the standard columns and the function column.
     */
    protected function AddCustomColumns()
    {
        parent::AddCustomColumns();

        $jsParas = $this->_GetRecordClickJavaScriptParameters();
        $this->tableObj->AddHeaderField(['active' => ServiceLocator::get('translator')->trans('chameleon_system_core.record.is_active')], 'left', null, 1, false, 100);
        $this->tableObj->AddColumn('id', 'left', [$this, 'CallBackActivationStatus'], $jsParas, 1);
        ++$this->columnCount;
    }

    /**
     * returns a checkbox field for image file selection with javascript onlick.
     *
     * @param string $id
     * @param array $row
     *
     * @return string
     */
    public function CallBackActivationStatus($id, $row)
    {
        $oTree = TdbCmsTree::GetNewInstance();
        $oTree->Load($row['cms_tree_id']);
        $oCurrentActiveTreeConnection = $oTree->GetActivePageTreeConnectionForTree();

        $html = '<i class="fas fa-unlink" title="'.TGlobal::OutHTML(ServiceLocator::get('translator')->trans('chameleon_system_core.list_tree_node.state_disabled')).'"></i> '.TGlobal::OutHTML(ServiceLocator::get('translator')->trans('chameleon_system_core.list_tree_node.state_disabled'));
        if (false !== $oCurrentActiveTreeConnection && $oCurrentActiveTreeConnection->id == $row['id']) {
            if ('1' == $row['active']) {
                $html = '<i class="fas fa-check-square text-success" title="'.TGlobal::OutHTML(ServiceLocator::get('translator')->trans('chameleon_system_core.list_tree_node.state_active_and_live')).'"></i>';
            } else {
                $html = '<i class="far fa-trash-alt text-danger" title="'.TGlobal::OutHTML(ServiceLocator::get('translator')->trans('chameleon_system_core.list_tree_node.state_active_but_not_live')).'"></i>';
            }
        } else {
            if ('1' == $row['active']) {
                $html = '<i class="far fa-trash-alt text-danger" title="'.TGlobal::OutHTML(ServiceLocator::get('translator')->trans('chameleon_system_core.list_tree_node.state_active_but_not_live')).'"></i>';
            }
        }

        if ('0000-00-00 00:00:00' !== $row['start_date']) {
            $html .= TGlobal::OutHTML(ServiceLocator::get('translator')->trans('chameleon_system_core.field_tree_node.date_starting_on')).' '.$row['start_date'].'<br />';
        }
        if ('0000-00-00 00:00:00' !== $row['end_date']) {
            $html .= ' '.TGlobal::OutHTML(ServiceLocator::get('translator')->trans('chameleon_system_core.field_tree_node.date_ending_on')).' '.$row['end_date'];
        }

        return $html;
    }

    /**
     * adds the orderby info to the table.
     */
    public function AddSortInformation()
    {
        parent::AddSortInformation();
        $this->tableObj->orderList['active'] = 'DESC';
        $this->tableObj->orderList['start_date'] = 'ASC';
    }
}
