<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Symfony\Component\HttpFoundation\Request;

class TCMSListManagerDocumentChooser extends TCMSListManagerFullGroupTable
{
    /**
     * add custom filter section.
     */
    protected function PostCreateTableObjectHook()
    {
        parent::PostCreateTableObjectHook();

        $filterSection = '<div class="form-group">
        <div class="">';

        $oTreeSelect = new TCMSRenderDocumentTreeSelectBox();

        /** @var Request $request */
        $request = ChameleonSystem\CoreBundle\ServiceLocator::get('request_stack')->getCurrentRequest();
        $documentTreeId = $request->get('cms_document_tree_id');
        if (null === $documentTreeId) {
            $documentTreeId = '';
        }

        $options = '<option value="">'.ChameleonSystem\CoreBundle\ServiceLocator::get('translator')->trans('chameleon_system_core.form.select_box_nothing_selected')."</option>\n";
        $options .= $oTreeSelect->GetTreeOptions($documentTreeId);

        $oViewRenderer = new ViewRenderer();
        $oViewRenderer->AddSourceObject('sInputClass', 'form-control form-control-sm');
        $oViewRenderer->AddSourceObject('sName', 'cms_document_tree_id');
        $oViewRenderer->AddSourceObject('sLabelText', ChameleonSystem\CoreBundle\ServiceLocator::get('translator')->trans('chameleon_system_core.document_chooser.tree_node'));
        $oViewRenderer->AddSourceObject('onChange', "document.forms['".TGlobal::OutHTML($this->tableObj->listName)."'].submit();");
        $oViewRenderer->AddSourceObject('options', $options);

        $filterSection .= $oViewRenderer->Render('userInput/form/selectFullOptionList.html.twig', null, false);
        $filterSection .= '</div>
        </div>
        ';

        $this->tableObj->searchBoxContent = $filterSection;

        $this->tableObj->aHiddenFieldIgnoreList = ['cms_document_tree_id'];
        $customSearchFieldParams = ['cms_document_tree_id' => $documentTreeId];
        $this->tableObj->AddCustomSearchFieldParameter($customSearchFieldParams);
    }

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
        return 'parent._SetDocument';
    }

    /**
     * add additional fields.
     */
    public function AddFields()
    {
        parent::AddFields();

        $jsParas = ['id'];
        ++$this->columnCount;
        $this->tableObj->AddHeaderField(['id' => '#'], 'left', null, 1, false);
        $this->tableObj->AddColumn('id', 'left', [$this, 'CallBackMediaSelectBox'], null, 1);

        ++$this->columnCount;
        $this->tableObj->AddHeaderField(['cms_filetype_id' => ChameleonSystem\CoreBundle\ServiceLocator::get('translator')->trans('chameleon_system_core.list_document.file_type')], 'left', null, 1, false);
        $this->tableObj->AddColumn('cms_filetype_id', 'left', [$this, 'CallBackDocumentFileType'], $jsParas, 1);

        ++$this->columnCount;
        $this->tableObj->AddHeaderField(['name' => ChameleonSystem\CoreBundle\ServiceLocator::get('translator')->trans('chameleon_system_core.list_document.title')], 'left', null, 1, false);
        $this->tableObj->AddColumn('name', 'left', null, $jsParas, 1);
        $this->tableObj->searchFields['`cms_document`.`name`'] = 'full';

        ++$this->columnCount;
        $this->tableObj->AddHeaderField(['filename' => ChameleonSystem\CoreBundle\ServiceLocator::get('translator')->trans('chameleon_system_core.list_document.file_name')], 'left', null, 1, false);
        $this->tableObj->AddColumn('filename', 'left', [$this, 'CallBackFilenameShort'], $jsParas, 1);
        $this->tableObj->searchFields['`cms_document`.`filename`'] = 'full';

        ++$this->columnCount;
        $this->tableObj->AddHeaderField(['filesize' => ChameleonSystem\CoreBundle\ServiceLocator::get('translator')->trans('chameleon_system_core.list_document.file_size')], 'left', null, 1, false);
        $this->tableObj->AddColumn('filesize', 'left', [$this, 'CallBackHumanRedableFileSize'], $jsParas, 1);
    }

    /**
     * restrict the list to show only images with given dimensions.
     */
    public function GetCustomRestriction()
    {
        $query = parent::GetCustomRestriction();

        $oGlobal = TGlobal::instance();
        $cms_document_tree_id = $oGlobal->GetUserData('cms_document_tree_id');

        if (!empty($cms_document_tree_id) || !empty($this->tableObj->_postData['cms_document_tree_id'])) {
            if (!empty($query)) {
                $query .= ' AND ';
            }
            $query .= '`'.MySqlLegacySupport::getInstance()->real_escape_string($this->oTableConf->sqlData['name'])."`.`cms_document_tree_id` = '".MySqlLegacySupport::getInstance()->real_escape_string($cms_document_tree_id)."'";
        }

        return $query;
    }

    /**
     * {@inheritdoc}
     */
    public function AddTableGrouping($columnCount = '')
    {
        $groupField = '`cms_document_tree`.`name`';

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
     * returns the filetype as rendered html.
     *
     * @param string $id
     * @param array $row
     *
     * @return string
     */
    public function CallBackDocumentFileType($id, $row)
    {
        $oFileDownload = new TCMSDownloadFile();
        /* @var $oFileDownload TCMSDownloadFile */
        $oFileDownload->Load($row['id']);

        $html = $oFileDownload->GetPlainFileTypeIcon();

        return $html;
    }
}
