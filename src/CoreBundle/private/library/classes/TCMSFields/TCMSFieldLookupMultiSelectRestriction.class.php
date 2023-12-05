<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use ChameleonSystem\DatabaseMigration\DataModel\LogChangeDataModel;
use ChameleonSystem\DatabaseMigration\Query\MigrationQueryData;
use Symfony\Contracts\Translation\TranslatorInterface;

class TCMSFieldLookupMultiSelectRestriction extends TCMSFieldLookupMultiselect
{
    const INVERSE_EMPTY_FIELD_NAME_POST_NAME = '_inverse_empty';

    /**
     * {@inheritdoc}
     */
    public function ChangeFieldTypePreHook()
    {
        parent::ChangeFieldTypePreHook();
        $connection = $this->getDatabaseConnection();

        $query = sprintf(
            'ALTER TABLE %s DROP %s ',
            $connection->quoteIdentifier($this->sTableName),
            $connection->quoteIdentifier($this->getInverseEmptyFieldName())
        );
        $connection->executeQuery($query);
        $queryData = array(new LogChangeDataModel($query));
        TCMSLogChange::WriteTransaction($queryData);
    }

    /**
     * {@inheritdoc}
     */
    public function ChangeFieldTypePostHook()
    {
        parent::ChangeFieldTypePostHook();
        $connection = $this->getDatabaseConnection();
        $query = sprintf(
            "ALTER TABLE %s ADD %s ENUM ('0','1') DEFAULT '0' NOT NULL",
            $connection->quoteIdentifier($this->sTableName),
            $connection->quoteIdentifier($this->getInverseEmptyFieldName())
        );
        $connection->executeQuery($query);
        $queryData = array(new LogChangeDataModel($query));
        TCMSLogChange::WriteTransaction($queryData);
    }

    /**
     * {@inheritdoc}
     */
    public function GetHTML()
    {
        $translator = $this->getTranslator();
        $isInverseEmpty = $this->isInverseEmptyFieldSet();
        $checked = '';
        if (true === $isInverseEmpty) {
            $checked = 'checked="checked"';
        }
        $html = sprintf(
            '
            <div class="fieldMltInverseEmpty" >
                <input type="checkbox" name="%s" id="fieldMltInverseEmptyCheckbox%s" %s value="1" autocomplete="off">
                <label for="fieldMltInverseEmptyCheckbox%2$s">%s</label> 
                <span class="toolTipButton toolTipButton%2$s"><i class="fas fa-question-circle text-info" style="font-size: 1.3em; cursor: pointer; cursor: hand;" title="%s"></i></span>
                <div class="tooltipContainer alert alert-info">%s</div>
            </div>
             ',
            $this->getInverseEmptyFieldName(),
            $this->name,
            $checked,
            $translator->trans('chameleon_system_core.field_lookup_multi_select_restriction.check_label'),
            $translator->trans('chameleon_system_core.cms_module_table_editor.field_help'),
            $translator->trans('chameleon_system_core.field_lookup_multi_select_restriction.check_help')
        );
        $html .= sprintf('
        <script type="text/javascript"> 
            $(\'.toolTipButton%1$s\').siblings(\'.tooltipContainer\').hide();
            $(\'.toolTipButton%1$s\').bind(\'click\',function(){
                $(this).siblings(\'.tooltipContainer\').toggle();
            });
        </script>
        ', $this->name);

        return $html.' '.parent::GetHTML();
    }

    /**
     * @return bool
     */
    private function isInverseEmptyFieldSet()
    {
        $connection = $this->getDatabaseConnection();
        $query = sprintf(
            'SELECT %s.%s FROM %1$s WHERE `id` = :recordId ',
            $connection->quoteIdentifier($this->sTableName),
            $connection->quoteIdentifier($this->getInverseEmptyFieldName())
        );
        $result = $connection->fetchColumn($query, array('recordId' => $this->oTableRow->sqlData['id']));
        if (false === $result) {
            return false;
        }

        return '1' === $result;
    }

    /**
     * {@inheritdoc}
     */
    public function PostSaveHook($iRecordId)
    {
        $inverseEmptyFieldName = $this->getInverseEmptyFieldName();
        if (false === isset($this->oTableRow->sqlData[$inverseEmptyFieldName])) {
            $inverseEmptyValue = '0';
        } else {
            $inverseEmptyValue = $this->oTableRow->sqlData[$inverseEmptyFieldName];
        }

        $connection = $this->getDatabaseConnection();
        $connection->update(
            $this->oTableRow->table,
            array($inverseEmptyFieldName => $inverseEmptyValue),
            array('id' => $this->oTableRow->id)
        );
        $editLanguageIsoCode = $this->getBackendSession()->getCurrentEditLanguageIso6391();
        $migrationQueryData = new MigrationQueryData($this->oTableRow->table, $editLanguageIsoCode);
        $migrationQueryData
            ->setFields(array($inverseEmptyFieldName => $inverseEmptyValue))
            ->setWhereEquals(array('id' => $this->oTableRow->id));
        $queryData = array(new LogChangeDataModel($migrationQueryData, LogChangeDataModel::TYPE_INSERT));

        TCMSLogChange::WriteTransaction($queryData);
    }

    /**
     * {@inheritdoc}
     */
    public function RenderFieldMethodsString()
    {
        $methodOutput = parent::RenderFieldMethodsString();
        $methodData = $this->GetFieldMethodBaseDataArray();
        $targetTableName = $this->GetForeignTableName();
        $fieldMethodName = $this->GetFieldMethodName();
        $methodData['sMethodName'] = $fieldMethodName.'WithInverseEmptySelectionLogicList';
        $methodData['sParentMethodName'] = $fieldMethodName.'List';
        $methodData['sMethodDescription'] = "\n\t * If inverse logic for empty selection is on for this field, method returns null on empty lists. Otherwise it returns the empty list object.";
        $itemClassName = TCMSTableToClass::GetClassName(TCMSTableToClass::PREFIX_CLASS, $targetTableName);
        $methodData['sReturnType'] = $itemClassName.'List|null';
        $methodData['inverseEmptyFieldName'] = TCMSTableToClass::PREFIX_PROPERTY.TCMSTableToClass::ConvertToClassString(
                $this->getInverseEmptyFieldName()
            );
        $methodData['aParameters']['sOrderBy'] = self::GetMethodParameterArray(
            'string',
            "''",
            'a SQL order by string (without the order by)'
        );

        $viewParser = new TViewParser();
        $viewParser->bShowTemplatePathAsHTMLHint = false;
        $viewParser->AddVarArray($methodData);

        $methodCode = $viewParser->RenderObjectView('getobject', 'TCMSFields/TCMSFieldLookupMultiSelectRestriction');
        $viewParser->AddVar('sMethodCode', $methodCode);

        $methodOutput .= $viewParser->RenderObjectView('method', 'TCMSFields/TCMSFieldLookupMultiSelectRestriction');

        // IdList method
        $methodData['sMethodName'] = $fieldMethodName.'WithInverseEmptySelectionLogicIdList';
        $methodData['sParentMethodName'] = $fieldMethodName.'IdList';
        $methodData['sMethodDescription'] = "\n\t * If inverse logic for empty selection is on for this field, method returns null on empty lists. Otherwise it returns the empty array|string";
        $methodData['sReturnType'] = 'array|string|null';
        $methodData['aParameters']['bReturnAsCommaSeparatedString'] = self::GetMethodParameterArray(
            'bool',
            'false',
            "Set this to true if you need the ID list for a query e.g. WHERE `related_record_id` IN ('1','2','abcd-234')"
        );

        $viewParser->AddVarArray($methodData);
        $methodCode = $viewParser->RenderObjectView(
            'getobjectIDs',
            'TCMSFields/TCMSFieldLookupMultiSelectRestriction'
        );
        $viewParser->AddVar('sMethodCode', $methodCode);
        $methodOutput .= "\n".$viewParser->RenderObjectView(
                'method',
                'TCMSFields/TCMSFieldLookupMultiSelectRestriction'
            );

        return $methodOutput;
    }

    /**
     * @return string
     */
    private function getInverseEmptyFieldName()
    {
        return $this->name.self::INVERSE_EMPTY_FIELD_NAME_POST_NAME;
    }

    /**
     * {@inheritdoc}
     */
    public function RenderFieldPropertyString()
    {
        $viewParser = new TViewParser();
        $viewParser->bShowTemplatePathAsHTMLHint = false;
        $writeData = $this->GetInverseEmptyFieldWriterData();
        $viewParser->AddVarArray($writeData);

        return $viewParser->RenderObjectView('property', 'TCMSFields/TCMSField');
    }

    /**
     * {@inheritdoc}
     */
    public function RenderFieldPostLoadString()
    {
        $viewParser = new TViewParser();
        $viewParser->bShowTemplatePathAsHTMLHint = false;
        $writeData = $this->GetInverseEmptyFieldWriterData();
        $viewParser->AddVarArray($writeData);

        return $viewParser->RenderObjectView('postload', 'TCMSFields/TCMSFieldBoolean');
    }

    /**
     * @return array
     */
    protected function GetInverseEmptyFieldWriterData()
    {
        $fieldNotes = array();
        $fieldNoteHelp = trim($this->oDefinition->sqlData['049_helptext']);
        if (!empty($fieldNoteHelp)) {
            $fieldNoteHelp = wordwrap($fieldNoteHelp, 80);
            $fieldNotes = explode("\n", $fieldNoteHelp);
        }

        $writeData = array(
            'sFieldFullName' => $this->oDefinition->sqlData['translation'].' inverse empty selection logic',
            'aFieldDesc' => $fieldNotes,
            'sFieldType' => 'boolean',
            'sFieldVisibility' => 'public',
            'sFieldName' => TCMSTableToClass::PREFIX_PROPERTY.TCMSTableToClass::ConvertToClassString(
                    $this->getInverseEmptyFieldName()
                ),
            'sFieldDefaultValue' => 'false',
            'sFieldDatabaseName' => $this->getInverseEmptyFieldName(),
            'oDefinition' => $this->oDefinition,
            'sTableName' => $this->sTableName,
            'databaseConnection' => $this->getDatabaseConnection(),
        );

        return $writeData;
    }

    /**
     * @return TranslatorInterface
     */
    private function getTranslator()
    {
        return \ChameleonSystem\CoreBundle\ServiceLocator::get('translator');
    }
}
