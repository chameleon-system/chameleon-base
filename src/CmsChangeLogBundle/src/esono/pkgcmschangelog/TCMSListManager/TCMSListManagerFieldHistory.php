<?php

use ChameleonSystem\CoreBundle\ServiceLocator;
use ChameleonSystem\CoreBundle\Translation\ChameleonTranslator;
use Doctrine\DBAL\Connection;

class TCMSListManagerFieldHistory extends TCMSListManagerFullGroupTable
{

    /**
     * {@inheritDoc}
     */
    protected function DefineInterface(): void
    {
        $this->methodCallAllowed = ['restoreFieldValueVersion'];
    }

    public function restoreFieldValueVersion(): void
    {
        // TODO: Implement service or inline functionality to restore selected revision.
        // Selected id is supplied at "recordId" parameter in request body.
    }

    /**
     * {@inheritDoc}
     */
    public function AddFields(): void
    {
        // Only fields necessary for a comprehensive listing of field value changes are defined, default fields from parent are explicitly excluded.

        $translator = $this->getTranslator();
        $linkField = ['id'];

        ++$this->columnCount;
        $this->tableObj->AddHeaderField($translator->trans('chameleon_system_cms_change_log.column.change_type'), 'left', null, 1, false);
        $this->tableObj->AddColumn('pkg_cms_changelog_set_change_type', 'left', null, $linkField);

        ++$this->columnCount;
        $this->tableObj->AddHeaderField($translator->trans('chameleon_system_cms_change_log.column.changed_on'), 'left', null, 1, false);
        $this->tableObj->AddColumn('pkg_cms_changelog_set_modify_date', 'left', null, $linkField);

        ++$this->columnCount;
        $this->tableObj->AddHeaderField($translator->trans('chameleon_system_cms_change_log.column.old_value'), 'left', null, 1, false);
        $this->tableObj->AddColumn('value_old', 'left', [$this, 'getFieldTextWithAttributes'], $linkField);
    }

    /**
     * {@inheritDoc}
     */
    public function GetCustomRestriction(): string
    {
        $changeLogFieldConfigurationId = $this->sRestriction;

        return sprintf(
            '`pkg_cms_changelog_item`.`cms_field_conf` = %s',
            $this->getDatabaseConnection()->quote($changeLogFieldConfigurationId)
        );
    }

    /**
     * @return string
     */
    protected function _GetRecordClickJavaScriptFunctionName(): string
    {
        return 'restoreFieldValueVersion';
    }

    // Formatting

    /**
     * @param $field
     * @param array $row
     * @param string $fieldName
     * @return string
     */
    public function getFieldTextWithAttributes($field, array $row, string $fieldName): string
    {
        $originalFieldValue = unserialize($row[$fieldName], ['allowed_classes' => []]);
        $serializedFieldPayload = json_encode(['value' => $originalFieldValue], JSON_UNESCAPED_UNICODE);
        $encodedFieldPayload = htmlspecialchars($serializedFieldPayload, ENT_QUOTES);

        return sprintf('<span data-field-restorable-value="%s">%s</span>', $encodedFieldPayload, $originalFieldValue);
    }

    // Dependencies

    /**
     * @return Connection
     */
    private function getDatabaseConnection(): Connection
    {
        return ServiceLocator::get('database_connection');
    }

    /**
     * @return ChameleonTranslator
     */
    private function getTranslator(): ChameleonTranslator
    {
        return ServiceLocator::get('chameleon_system_core.translator');
    }

}