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
 * extends the standard listing so that a preview image is shown, and if the
 * class is called with the right parameters it will show an assign button to
 * assign an image from the list to the calling record.
 * /**/
class TCMSListManagerWYSIWYGImage extends TCMSListManagerImagedatabase
{
    /**
     * we need this to overwrite the standard function column.
     */
    public function _AddFunctionColumn()
    {
    }

    /**
     * returns the name of the javascript function to be called when the user clicks on a
     * record within the table.
     *
     * @return string
     */
    public function _GetRecordClickJavaScriptFunctionName()
    {
        return 'parent.selectImage';
    }

    /**
     * add custom filter section.
     */
    protected function PostCreateTableObjectHook()
    {
        parent::PostCreateTableObjectHook();

        $filterSection = '<div class="form-group">
        <div class="">';

        $oTreeSelect = new TCMRenderMediaTreeSelectBox();

        $request = ChameleonSystem\CoreBundle\ServiceLocator::get('request_stack')->getCurrentRequest();
        $mediaTreeId = $request->get('cms_media_tree_id');
        if (null === $mediaTreeId) {
            $mediaTreeId = '';
        }

        $options = '<option value="">'.ChameleonSystem\CoreBundle\ServiceLocator::get('translator')->trans('Bitte wählen')."</option>\n";
        $options .= $oTreeSelect->GetTreeOptions($mediaTreeId);

        $oViewRenderer = new ViewRenderer();
        $oViewRenderer->AddSourceObject('sInputClass', 'form-control form-control-sm');
        $oViewRenderer->AddSourceObject('sName', 'cms_media_tree_id');
        $oViewRenderer->AddSourceObject('sLabelText', ChameleonSystem\CoreBundle\ServiceLocator::get('translator')->trans('chameleon_system_core.image_chooser.tree_node'));
        $oViewRenderer->AddSourceObject('onChange', "document.forms['".TGlobal::OutHTML($this->tableObj->listName)."'].submit();");
        $oViewRenderer->AddSourceObject('options', $options);

        $filterSection .= $oViewRenderer->Render('userInput/form/selectFullOptionList.html.twig', null, false);
        $filterSection .= '</div>
        </div>
        ';

        $this->tableObj->searchBoxContent = $filterSection;

        $this->tableObj->aHiddenFieldIgnoreList = ['cms_media_tree_id'];
        $customSearchFieldParams = ['cms_media_tree_id' => $mediaTreeId];
        $this->tableObj->AddCustomSearchFieldParameter($customSearchFieldParams);
    }

    /**
     * restrict the list to show only images with given dimensions.
     *
     * @return string
     */
    public function GetCustomRestriction()
    {
        $query = '';
        $query .= parent::GetCustomRestriction();

        $oGlobal = TGlobal::instance();
        $cms_media_tree_id = $oGlobal->GetUserData('cms_media_tree_id');

        if (!empty($cms_media_tree_id)) {
            if (!empty($query)) {
                $query .= ' AND ';
            }
            $query .= '`'.MySqlLegacySupport::getInstance()->real_escape_string($this->oTableConf->sqlData['name'])."`.`cms_media_tree_id` = '".MySqlLegacySupport::getInstance()->real_escape_string($cms_media_tree_id)."'";
        } elseif (!empty($this->tableObj->_postData['cms_media_tree_id'])) {
            $cms_media_tree_id = $this->tableObj->_postData['cms_media_tree_id'];
            if (!empty($query)) {
                $query .= ' AND ';
            }
            $query .= '`'.MySqlLegacySupport::getInstance()->real_escape_string($this->oTableConf->sqlData['name'])."`.`cms_media_tree_id` = '".MySqlLegacySupport::getInstance()->real_escape_string($cms_media_tree_id)."'";
        }

        $query = $this->AddMediaFilter($query);

        return $query;
    }

    /**
     *  Add media filter for images that be gone from URL.
     *
     * @param string $query
     *
     * @return string
     */
    protected function AddMediaFilter($query)
    {
        $oGlobal = TGlobal::instance();
        $sAllowedFileTypes = $oGlobal->GetUserData('sAllowedFileTypes');
        if (!is_null($sAllowedFileTypes) && !empty($sAllowedFileTypes)) {
            $sQuery = '';
            $aAllowedFileTypes = explode(',', $sAllowedFileTypes);
            for ($i = 0; $i < count($aAllowedFileTypes); ++$i) {
                if (0 == $i) {
                    $sOr = '';
                } else {
                    $sOr = ' OR ';
                }

                $sQuery .= $sOr.'`file_extension`='."'".MySqlLegacySupport::getInstance()->real_escape_string(strtolower(trim($aAllowedFileTypes[$i])))."'";
            }
            $oAllowedFileTypes = TdbCmsFiletypeList::GetList();
            $oAllowedFileTypes->AddFilterString($sQuery);
            $bIsFirstTime = true;
            if ($oAllowedFileTypes->Length() > 0) {
                while ($oAllowedFileTyp = $oAllowedFileTypes->Next()) {
                    if ($bIsFirstTime) {
                        $sOr = 'AND (';
                        $bIsFirstTime = false;
                    } else {
                        $sOr = ' OR ';
                    }
                    $query .= $sOr.'`cms_media`.`cms_filetype_id`='."'".MySqlLegacySupport::getInstance()->real_escape_string($oAllowedFileTyp->id)."'";
                }
                $query .= ' OR `cms_media`.`external_embed_code` != \'\' )';
            }
        }

        return $query;
    }

    public function AddTableGrouping($columnCount = '')
    {
        $groupField = '`cms_media_tree`.`name`';
        $list_group_field_column = 'category';

        $this->tableObj->showGroupSelector = false;
        $this->tableObj->AddGroupField([$list_group_field_column => $groupField], 'left', null, null, $this->columnCount);
        // $this->tableObj->showGroupSelectorText = 'Verzeichnis';
        $this->tableObj->showAllGroupsText = '['.ChameleonSystem\CoreBundle\ServiceLocator::get('translator')->trans('chameleon_system_core.list.group_show_all').']';
        $tmpArray = [$list_group_field_column => 'ASC'];
        $this->tableObj->orderList = array_merge($tmpArray, $this->tableObj->orderList);
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
}
