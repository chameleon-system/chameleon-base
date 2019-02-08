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
use Doctrine\DBAL\Connection;
use Symfony\Component\Translation\TranslatorInterface;

class TCMSListManagerFieldHistory extends TCMSListManagerFullGroupTable
{

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
     * @return TranslatorInterface
     */
    private function getTranslator(): TranslatorInterface
    {
        return ServiceLocator::get('translator');
    }

}