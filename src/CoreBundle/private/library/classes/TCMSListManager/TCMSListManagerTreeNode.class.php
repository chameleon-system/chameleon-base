<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

class TCMSListManagerTreeNode extends TCMSListManagerFullGroupTable
{
    protected $sTreeNodeTableID = null;

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

        $sURL = PATH_CMS_CONTROLLER.'?'.TTools::GetArrayAsURLForJavascript(array('tableid' => $this->sTreeNodeTableID, 'pagedef' => 'tableeditorPopup'));
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

        $oGlobal = TGlobal::instance();

        $tableInUserGroup = $oGlobal->oUser->oAccessManager->user->IsInGroups($this->oTableConf->sqlData['cms_usergroup_id']);
        if ($tableInUserGroup) {
            if ($oGlobal->oUser->oAccessManager->HasNewPermission($this->oTableConf->sqlData['name'])) {
                // remove old new button
                $this->oMenuItems->RemoveItem('sItemKey', 'new');

                // add new button
                $oMenuItem = new TCMSTableEditorMenuItem();
                $oMenuItem->sDisplayName = TGlobal::Translate('chameleon_system_core.action.new');
                $oMenuItem->sIcon = 'fas fa-plus';
                $oMenuItem->sItemKey = 'new';

                $aParameter = array('pagedef' => 'tableeditorPopup', 'id' => $this->oTableConf->id, 'tableid' => $this->sTreeNodeTableID, 'module_fnc' => array('contentmodule' => 'Insert'));
                $aAdditionalParams = $this->GetHiddenFieldsHook();
                if (is_array($aAdditionalParams) && count($aAdditionalParams) > 0) {
                    $aParameter = array_merge($aParameter, $aAdditionalParams);
                }

                $oMenuItem->sOnClick = "document.location.href='".PATH_CMS_CONTROLLER.'?'.TTools::GetArrayAsURLForJavascript($aParameter)."'";
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
        $this->tableObj->AddHeaderField(array('active' => TGlobal::Translate('chameleon_system_core.record.is_active')), 'left', null, 1, false, 100);
        $this->tableObj->AddColumn('id', 'left', array($this, 'CallBackActivationStatus'), $jsParas, 1);
        ++$this->columnCount;
    }

    /**
     * returns a checkbox field for image file selection with javascript onlick.
     *
     * @param string $id
     * @param array  $row
     *
     * @return string
     */
    public function CallBackActivationStatus($id, $row)
    {
        $oTree = TdbCmsTree::GetNewInstance();
        $oTree->Load($row['cms_tree_id']);
        $oCurrentActiveTreeConnection = $oTree->GetActivePageTreeConnectionForTree();

        $html = '<i class="fas fa-unlink" title="'.TGlobal::OutHTML(TGlobal::Translate('chameleon_system_core.list_tree_node.state_disabled')).'"></i> '.TGlobal::OutHTML(TGlobal::Translate('chameleon_system_core.list_tree_node.state_disabled'));
        if (false !== $oCurrentActiveTreeConnection && $oCurrentActiveTreeConnection->id == $row['id']) {
            if ('1' == $row['active']) {
                $html = '<i class="fas fa-check-square text-success" title="'.TGlobal::OutHTML(TGlobal::Translate('chameleon_system_core.list_tree_node.state_active_and_live')).'"></i>';
            } else {
                $html = '<i class="far fa-trash-alt text-danger" title="'.TGlobal::OutHTML(TGlobal::Translate('chameleon_system_core.list_tree_node.state_active_but_not_live')).'"></i>';
            }
        } else {
            if ('1' == $row['active']) {
                $html = '<i class="far fa-trash-alt text-danger" title="'.TGlobal::OutHTML(TGlobal::Translate('chameleon_system_core.list_tree_node.state_active_but_not_live')).'"></i>';
            }
        }

        if ('0000-00-00 00:00:00' !== $row['start_date']) {
            $html .= TGlobal::OutHTML(TGlobal::Translate('chameleon_system_core.field_tree_node.date_starting_on')).' '.$row['start_date'].'<br />';
        }
        if ('0000-00-00 00:00:00' !== $row['end_date']) {
            $html .= ' '.TGlobal::OutHTML(TGlobal::Translate('chameleon_system_core.field_tree_node.date_ending_on')).' '.$row['end_date'];
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
