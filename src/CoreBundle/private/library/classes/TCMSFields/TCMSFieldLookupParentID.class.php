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
 * through the config parameter "bShowLinkToParentRecord=true" you can activate a link
 * that can be used to jump to the parent record (assuming the user has the right permissions)
 * by setting bAllowEdit=true you can activate the right to select a different parent
 * note: all items will be made available.
/**/
class TCMSFieldLookupParentID extends TCMSFieldLookup
{
    public function GetHTML()
    {
        $sHTML = '';
        $bAllowEdit = $this->oDefinition->GetFieldtypeConfigKey('bAllowEdit');
        if ($bAllowEdit) {
            $sHTML = parent::GetHTML();
        } else {
            $sHTML = $this->getLinkToParentRecordIfSet();
        }

        return $sHTML;
    }

    /**
     * @return string
     */
    protected function getLinkToParentRecordIfSet()
    {
        if (empty($this->data)) {
            return TGlobal::Translate('chameleon_system_core.field_lookup.nothing_selected');
        }

        $tblName = $this->GetConnectedTableName();
        $item = new TCMSRecord();
        $item->table = $tblName;
        $item->Load($this->data);
        $showLinkToParentRecord = $this->oDefinition->GetFieldtypeConfigKey('bShowLinkToParentRecord');

        $html = $this->_GetHiddenField();

        if ('true' == $showLinkToParentRecord && '' !== $this->data) {
            $foreignTableName = $this->GetConnectedTableName();
            $global = TGlobal::instance();
            if ($global->oUser->oAccessManager->HasEditPermission($foreignTableName)) {
                $html .= '<div class="d-flex align-items-center">';
                $itemName = $item->GetName();
                if ('' !== $itemName) {
                    $html .= '<div class="mr-2">' . $itemName . '</div>';
                }
                $html .= '<div class="switchToRecordBox">' . TCMSRender::DrawButton(TGlobal::Translate('chameleon_system_core.field_lookup.switch_to'), "javascript:document.location.href='".$this->GetEditLinkForParentRecord()."';", 'fas fa-download') . '</div>';
                $html .= '</div>';
            }
        }
        return $html;
    }

    /**
     * {@inheritdoc}
     */
    public function GetReadOnly()
    {
        return $this->getLinkToParentRecordIfSet();
    }

    public function GetDisplayType()
    {
        $displayType = parent::GetDisplayType();

        $bAllowEdit = $this->oDefinition->GetFieldtypeConfigKey('bAllowEdit');
        if ('none' === $displayType && !$bAllowEdit) {
            $displayType = 'readonly';
        }

        return $displayType;
    }

    /**
     * return link to edit the parent record.
     *
     * @return string
     */
    protected function GetEditLinkForParentRecord()
    {
        $foreignTableName = $this->GetConnectedTableName();
        $oTableConf = TdbCmsTblConf::GetNewInstance();
        $oTableConf->LoadFromField('name', $foreignTableName);

        $sEditLink = '';

        $sLinkParams = array('pagedef' => 'tableeditor', 'tableid' => $oTableConf->id, 'id' => urlencode($this->data));
        $sLink = PATH_CMS_CONTROLLER.'?'.TTools::GetArrayAsURLForJavascript($sLinkParams);

        return $sLink;
    }

    /**
     * called on each field when a record is inserted.
     *
     * @param string $iRecordId
     */
    public function PostInsertHook($iRecordId)
    {
        parent::PostInsertHook($iRecordId);

        if (!empty($this->data)) {
            TCacheManager::PerformeTableChange($this->GetConnectedTableName(), $this->data);
        }
    }

    /**
     * called on each field when a record is saved.
     *
     * @param string $iRecordId
     */
    public function PostSaveHook($iRecordId)
    {
        parent::PostSaveHook($iRecordId);

        if (!empty($this->data)) {
            TCacheManager::PerformeTableChange($this->GetConnectedTableName(), $this->data);
        }
    }

    /**
     * return the new charset latin1 so that we get more memory
     * size for a record.
     *
     * @return string
     */
    public function _GetSQLCharset()
    {
        return ' CHARACTER SET latin1 COLLATE latin1_general_ci';
    }
}
