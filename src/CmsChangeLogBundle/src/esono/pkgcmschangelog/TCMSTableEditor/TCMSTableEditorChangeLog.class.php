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
 * /**/
class TCMSTableEditorChangeLog extends TCMSTableEditorChangeLogAutoParent
{
    /**
     * @var bool
     */
    protected $bIsUpdate;

    /**
     * @var array a list of tables for which logging is not allowed
     */
    protected $aForbiddenTables = ['pkg_cms_changelog_set', 'pkg_cms_changelog_item'];

    /**
     * {@inheritDoc}
     *
     * @return void
     */
    protected function GetCustomMenuItems()
    {
        parent::GetCustomMenuItems();

        if ($this->oTableConf->fieldChangelogActive) {
            $aParam = [];
            $aParam['pagedef'] = 'tablemanager';
            $aParam['id'] = TTools::GetCMSTableId('pkg_cms_changelog_set');
            $aParam['sRestrictionField'] = 'modified_id';
            $aParam['sRestriction'] = $this->oTable->id;

            $oMenuItem = new TCMSTableEditorMenuItem();
            $oMenuItem->sItemKey = 'getdisplayvalue';
            $oMenuItem->sDisplayName = ChameleonSystem\CoreBundle\ServiceLocator::get('translator')->trans('chameleon_system_cms_change_log.action.show_changes');
            $oMenuItem->sIcon = 'far fa-edit';
            $oMenuItem->href = $this->getUrlUtil()->getArrayAsUrl($aParam, '?');
            $this->oMenuItems->AddItem($oMenuItem);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function Save($postData, $bDataIsInSQLForm = false)
    {
        if ($this->oTableConf->fieldChangelogActive) {
            $this->failOnForbiddenTables();

            $this->bIsUpdate = isset($postData['id']);
        }

        return parent::Save($postData, $bDataIsInSQLForm);
    }

    /**
     * gets called after save if all posted data was valid.
     *
     * @param TIterator $oFields holds an iterator of all field classes from DB table with the posted values or default if no post data is present
     * @param TCMSRecord $oPostTable holds the record object of all posted data
     *
     * @return void
     */
    protected function PostSaveHook($oFields, $oPostTable)
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
     * @param TIterator $newFields holds an iterator of all field classes from DB table with the posted values or default if no post data is present
     * @param TCMSRecord $oPostTable holds the record object of all posted data
     *
     * @return array
     */
    protected function computeDifferences($newFields, $oPostTable)
    {
        $result = [];

        $oldFields = $this->getOldFields();

        $newFields->GoToStart();
        /** @var TCMSField $newField */
        while ($newField = $newFields->next()) {
            /* @var TCMSField $oldField */
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
                    $result[] = [$fieldId, '', ''];
                } else {
                    $result[] = [$fieldId, $oldField->data, $newField->data];
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

        /** @var ChameleonSystem\SecurityBundle\Service\SecurityHelperAccess $securityHelper */
        $securityHelper = ChameleonSystem\CoreBundle\ServiceLocator::get(ChameleonSystem\SecurityBundle\Service\SecurityHelperAccess::class);

        $aData = [];
        $aData['id'] = $oTableEditor->sId;
        $aData['modify_date'] = date('Y-m-d H:i:s');
        $aData['cms_user'] = $securityHelper->getUser()?->getId();
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
     * @param array $aDiff
     *
     * @return void
     */
    protected function createChangeItems($sChangeSetId, $aDiff)
    {
        for ($i = 0; $i < count($aDiff); ++$i) {
            $oChangeItem = TdbPkgCmsChangelogItem::GetNewInstance();
            $oTableEditor = TTools::GetTableEditorManager($oChangeItem->table);
            $oTableEditor->AllowEditByAll(true);
            $oTableEditor->sId = $sChangeSetId;
            $oTableEditor->Insert();

            $aData = [];
            $aData['id'] = $oTableEditor->sId;
            $aData['pkg_cms_changelog_set_id'] = $sChangeSetId;
            $aData['cms_field_conf'] = $aDiff[$i][0];
            $aData['value_old'] = serialize($aDiff[$i][1]);
            $aData['value_new'] = serialize($aDiff[$i][2]);

            $oTableEditor->Save($aData);
        }
    }

    /**
     * @return string
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
     * {@inheritDoc}
     */
    protected function PostInsertHook($oFields)
    {
        parent::PostInsertHook($oFields);

        if ($this->oTableConf->fieldChangelogActive) {
            $this->failOnForbiddenTables();
            $sChangeSetId = $this->createChangeSet('INSERT');

            $aDiff = [];
            foreach ($oFields as $oField) {
                if ('' !== $oField->data) {
                    $aDiff[] = [$oField->id, '', $oField->data];
                }
            }
            $this->createChangeItems($sChangeSetId, $aDiff);
        }
    }

    /**
     * {@inheritDoc}
     *
     * @return void
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
     * @return void
     *
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
     * {@inheritDoc}
     */
    protected function AddMLTConnectionExecute($oField, $iConnectedID)
    {
        parent::AddMLTConnectionExecute($oField, $iConnectedID);

        if ($this->oTableConf->fieldChangelogActive) {
            $this->failOnForbiddenTables();
            $sChangeSetId = $this->createChangeSet('UPDATE');
            $change = [$oField->oDefinition->id, '', $iConnectedID];
            /* @var TCMSField $oField */
            $this->createChangeItems($sChangeSetId, [$change]);
        }
    }

    /**
     * removes one connection from mlt.
     *
     * @param TCMSField $oField mlt field object
     * @param int $iConnectedID the connected record id that will be removed
     *
     * @return void
     */
    protected function RemoveMLTConnectionExecute($oField, $iConnectedID)
    {
        parent::RemoveMLTConnectionExecute($oField, $iConnectedID);

        if ($this->oTableConf->fieldChangelogActive) {
            $this->failOnForbiddenTables();
            $sChangeSetId = $this->createChangeSet('DELETE');
            $change = [$oField->oDefinition->id, $iConnectedID, ''];
            /* @var TCMSField $oField */
            $this->createChangeItems($sChangeSetId, [$change]);
        }
    }

    private function getOldFields(): array
    {
        return $this->oTablePreChangeData->getFieldsIndexed();
    }

    /**
     * @return UrlUtil
     */
    private function getUrlUtil()
    {
        return ChameleonSystem\CoreBundle\ServiceLocator::get('chameleon_system_core.util.url');
    }
}
