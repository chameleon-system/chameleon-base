<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use ChameleonSystem\CoreBundle\Util\UrlUtil;

/**
 * manages saving, inserting, and deleting data from a table.
/**/
class TCMSTableEditorChangeLog extends TCMSTableEditorChangeLogAutoParent
{
    /**
     * the original data of the row before a save overwrites the data.
     *
     * @var TCMSField[]
     */
    protected $oOldFields = array();

    /**
     * @var bool
     */
    protected $bIsUpdate;

    /**
     * @var array $aForbiddenTables a list of tables for which logging is not allowed
     */
    protected $aForbiddenTables = array('pkg_cms_changelog_set', 'pkg_cms_changelog_item');

    /**
     * {@inheritdoc}
     */
    protected function GetCustomMenuItems()
    {
        parent::GetCustomMenuItems();

        if ($this->oTableConf->fieldChangelogActive) {
            $aParam = array();
            $aParam['pagedef'] = 'tablemanager';
            $aParam['id'] = TTools::GetCMSTableId('pkg_cms_changelog_set');
            $aParam['sRestrictionField'] = 'modified_id';
            $aParam['sRestriction'] = $this->oTable->id;

            $oMenuItem = new TCMSTableEditorMenuItem();
            $oMenuItem->sItemKey = 'getdisplayvalue';
            $oMenuItem->sDisplayName = TGlobal::Translate('chameleon_system_cms_change_log.action.show_changes');
            $oMenuItem->sIcon = TGlobal::GetStaticURLToWebLib('/images/icons/page_edit.png');
            $oMenuItem->sOnClick = "document.location.href='".$this->getUrlUtil()->getArrayAsUrl($aParam, '?')."'";
            $this->oMenuItems->AddItem($oMenuItem);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function Save(&$postData, $bDataIsInSQLForm = false)
    {
        if ($this->oTableConf->fieldChangelogActive) {
            $this->failOnForbiddenTables();

            $this->bIsUpdate = isset($postData['id']);
        }

        return parent::Save($postData, $bDataIsInSQLForm);
    }

    /**
     * Not used anymore. Handled with $this->oTablePreChangeData.
     *
     * @param array $postData
     */
    protected function savePreSaveValues(array $postData)
    {
        $oPostTable = $this->GetNewTableObjectForEditor();
        if (isset($postData['id'])) {
            $oPostTable->Load($postData['id']);
            $this->oOldFields = $this->getArrayFromIterator($this->oTableConf->GetFields($oPostTable));
            $this->bIsUpdate = true;
        } else {
            $this->bIsUpdate = false;
        }

        $aFieldsToRemove = array();

        foreach ($this->oOldFields as $oOldField) {
            // values that weren't given in the change operation cannot be changed
            if (!array_key_exists($oOldField->name, $postData)) {
                $aFieldsToRemove[] = $oOldField;
            } else {
                if ($oOldField instanceof TCMSMLTField) {
                    /** @var TCMSMLTField $oOldField */
                    $oOldField->data = $oOldField->getMltValues();
                }
            }
        }

        foreach ($aFieldsToRemove as $oOldField) {
            unset($this->oOldFields[$oOldField->name]);
        }
    }

    /**
     * @param TIterator $iterator
     *
     * @return TCMSField[]
     */
    private function getArrayFromIterator(TIterator $iterator)
    {
        $retValue = array();
        while (false !== $item = $iterator->Next()) {
            /** @var $item TCMSField */
            $retValue[$item->name] = $item;
        }

        return $retValue;
    }

    /**
     * gets called after save if all posted data was valid.
     *
     * @param TIterator  $oFields    holds an iterator of all field classes from DB table with the posted values or default if no post data is present
     * @param TCMSRecord $oPostTable holds the record object of all posted data
     */
    protected function PostSaveHook(&$oFields, &$oPostTable)
    {
        parent::PostSaveHook($oFields, $oPostTable);

        if ($this->oTableConf->fieldChangelogActive) {
            $aDiff = $this->computeDifferences($oFields, $oPostTable);

            if (count($aDiff) > 0) {
                $sChangeSetId = $this->createChangeSet('UPDATE');
                $this->createChangeItems($sChangeSetId, $aDiff);
            }
        }
    }

    /**
     * computes differences between new and old values.
     *
     * @param TIterator  $newFields  holds an iterator of all field classes from DB table with the posted values or default if no post data is present
     * @param TCMSRecord $oPostTable holds the record object of all posted data
     *
     * @return array
     */
    protected function computeDifferences(&$newFields, &$oPostTable)
    {
        $result = [];

        $oldFields = $this->getOldFields();

        $newFields->GoToStart();
        /** @var TCMSField $newField */
        while ($newField = $newFields->next()) {
            /** @var TCMSField $oldField */
            if (isset($oldFields[$newField->name])) {
                $oldField = $oldFields[$newField->name];

                if ($oldField instanceof TCMSMLTField) {
                    $oldField->data = $oldField->getMltValues();
                }
            } else {
                if ($this->bIsUpdate) {
                    continue;
                }
                $oldField = clone $newField;
                $oldField->data = $oldField->oDefinition->fieldFieldDefaultValue;
            }

            $newField = clone $newField;
            if ($newField instanceof TCMSMLTField) {
                $newField->data = $newField->getMltValues();
            } else {
                $newField->data = $newField->ConvertPostDataToSQL();
            }

            if (is_array($oldField->data)) {
                if (isset($oldField->data['x']) && '-' === $oldField->data['x']) {
                    unset($oldField->data['x']);
                }
            }
            if (is_array($newField->data)) {
                if (isset($newField->data['x']) && '-' === $newField->data['x']) {
                    unset($newField->data['x']);
                }
            }

            $equalsVisitor = new TCMSFieldEqualsVisitor($oldField, $newField);
            $isEqual = $equalsVisitor->check();
            if (!$isEqual) {
                $fieldId = $newField->oDefinition->id;
                if ($newField->getBEncryptedData()) {
                    $result[] = array($fieldId, '', '');
                } else {
                    $result[] = array($fieldId, $oldField->data, $newField->data);
                }
            }
        }

        return $result;
    }

    /**
     * @param string $sAction
     *
     * @return string
     */
    protected function createChangeSet($sAction)
    {
        $oChangeSet = TdbPkgCmsChangelogSet::GetNewInstance();
        $oTableEditor = TTools::GetTableEditorManager($oChangeSet->table);
        $oTableEditor->AllowEditByAll(true);
        $oTableEditor->Insert();

        $aData = array();
        $aData['id'] = $oTableEditor->sId;
        $aData['modify_date'] = date('Y-m-d H:i:s');
        $aData['cms_user'] = TCMSUser::GetActiveUser()->id;
        $aData['cms_tbl_conf'] = $this->sTableId;
        $aData['modified_id'] = $this->oTable->id;
        $aData['modified_name'] = $this->getNameColumnValue();
        $aData['change_type'] = $sAction;
        $oTableEditor->AllowEditByAll(true);
        $oTableEditor->Save($aData);

        return $oTableEditor->sId;
    }

    /**
     * @param string $sChangeSetId
     * @param array  $aDiff
     */
    protected function createChangeItems($sChangeSetId, $aDiff)
    {
        for ($i = 0; $i < count($aDiff); ++$i) {
            $oChangeItem = TdbPkgCmsChangelogItem::GetNewInstance();
            $oTableEditor = TTools::GetTableEditorManager($oChangeItem->table);
            $oTableEditor->AllowEditByAll(true);
            $oTableEditor->sId = $sChangeSetId;
            $oTableEditor->Insert();

            $aData = array();
            $aData['id'] = $oTableEditor->sId;
            $aData['pkg_cms_changelog_set_id'] = $sChangeSetId;
            $aData['cms_field_conf'] = $aDiff[$i][0];
            $aData['value_old'] = serialize($aDiff[$i][1]);
            $aData['value_new'] = serialize($aDiff[$i][2]);

            $oTableEditor->Save($aData);
        }
    }

    /**
     * {@inheritdoc}
     */
    protected function getNameColumnValue()
    {
        $sNameValue = '';
        $sNameColumn = $this->oTableConf->GetNameColumn();
        if ('' === $sNameColumn) {
            return $sNameValue;
        }
        $sNameColumnCallback = $this->oTableConf->GetNameFieldCallbackFunction();

        if (null !== $sNameColumnCallback) {
            $sNameValue = call_user_func($sNameColumnCallback, $this->oTable->sqlData[$sNameColumn], $this->oTable->sqlData);
        } else {
            $sNameValue = $this->oTable->sqlData[$sNameColumn];
        }

        return $sNameValue;
    }

    /**
     * {@inheritdoc}
     */
    protected function PostInsertHook(&$oFields)
    {
        parent::PostInsertHook($oFields);

        if ($this->oTableConf->fieldChangelogActive) {
            $this->failOnForbiddenTables();
            $sChangeSetId = $this->createChangeSet('INSERT');

            $aDiff = array();
            foreach ($oFields as $oField) {
                if ('' !== $oField->data) {
                    $aDiff[] = array($oField->id, '', $oField->data);
                }
            }
            $this->createChangeItems($sChangeSetId, $aDiff);
        }
    }

    /**
     * {@inheritdoc}
     */
    protected function DeleteExecute()
    {
        parent::DeleteExecute();

        if ($this->oTableConf->fieldChangelogActive) {
            $this->failOnForbiddenTables();
            $this->createChangeSet('DELETE');
        }
    }

    /**
     * @throws TPkgCmsException
     */
    protected function failOnForbiddenTables()
    {
        if (in_array($this->oTableConf->fieldName, $this->aForbiddenTables)) {
            throw new TPkgCmsException('Tried to log a change within a table which does not allow this: '.$this->oTableConf->fieldName);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function SaveField($sFieldName, $sFieldContent, $bTriggerPostSaveHook = false)
    {
        if ($this->oTableConf->fieldChangelogActive) {
            $this->failOnForbiddenTables();

            $this->bIsUpdate = null !== $this->sId;
        }

        return parent::SaveField(
            $sFieldName,
            $sFieldContent,
            $bTriggerPostSaveHook
        );
    }

    /**
     * {@inheritdoc}
     */
    protected function AddMLTConnectionExecute($oField, $iConnectedID)
    {
        parent::AddMLTConnectionExecute($oField, $iConnectedID);

        if ($this->oTableConf->fieldChangelogActive) {
            $this->failOnForbiddenTables();
            $sChangeSetId = $this->createChangeSet('UPDATE');
            $change = array($oField->oDefinition->id, '', $iConnectedID);
            /** @var TCMSField $oField */
            $this->createChangeItems($sChangeSetId, array($change));
        }
    }

    /**
     * removes one connection from mlt.
     *
     * @param TCMSField $oField       mlt field object
     * @param int       $iConnectedID the connected record id that will be removed
     */
    protected function RemoveMLTConnectionExecute($oField, $iConnectedID)
    {
        parent::RemoveMLTConnectionExecute($oField, $iConnectedID);

        if ($this->oTableConf->fieldChangelogActive) {
            $this->failOnForbiddenTables();
            $sChangeSetId = $this->createChangeSet('DELETE');
            $change = array($oField->oDefinition->id, $iConnectedID, '');
            /** @var TCMSField $oField */
            $this->createChangeItems($sChangeSetId, array($change));
        }
    }

    /**
     * @return array
     */
    private function getOldFields(): array
    {
        return $this->oTablePreChangeData->getFieldsIndexed();
    }

    /**
     * @return UrlUtil
     */
    private function getUrlUtil()
    {
        return \ChameleonSystem\CoreBundle\ServiceLocator::get('chameleon_system_core.util.url');
    }
}
