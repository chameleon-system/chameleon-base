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
        $this->tableObj->AddColumn('value_old', 'left', null, $linkField);
    }

    /**
     * {@inheritDoc}
     */
    public function GetCustomRestriction(): string
    {
        $changeLogFieldConfigurationId = $this->sRestriction;
        $recordIds = $this->getRestrictionRecordIds($changeLogFieldConfigurationId);

        if (0 === count($recordIds)) {
            return '';
        }

        return sprintf(' %s.`id` IN (%s)', $this->getQuotedTableName(), $this->getQuotedElements($recordIds));
    }

    /**
     * @return string
     */
    protected function _GetRecordClickJavaScriptFunctionName(): string
    {
        return 'restoreFieldValueVersion';
    }

    // Subquery

    /**
     * @param string $fieldConfigurationId
     * @return string[]
     */
    private function getRestrictionRecordIds(string $fieldConfigurationId): array
    {
        $connection = $this->getDatabaseConnection();

        try {
            $stmt = $connection->executeQuery($this->getIdRestrictionQuery(), ['fieldConfigurationId' => $fieldConfigurationId]);
            return $stmt->fetchAll(PDO::FETCH_COLUMN);
        } catch (\Doctrine\DBAL\DBALException $e) {
            return [];
        }
    }

    // Query Form

    private function getIdRestrictionQuery(): string
    {
        return 'SELECT
                    `pkg_cms_changelog_item`.`id`
               FROM `pkg_cms_changelog_item`
          LEFT JOIN `pkg_cms_changelog_set`
                 ON `pkg_cms_changelog_set`.`id` = `pkg_cms_changelog_item`.`pkg_cms_changelog_set_id`
	          WHERE `pkg_cms_changelog_item`.`cms_field_conf` = :fieldConfigurationId
           ORDER BY `pkg_cms_changelog_set`.`modify_date` DESC';
    }

    // String Form

    /**
     * @return string
     */
    private function getQuotedTableName(): string
    {
        return $this->getDatabaseConnection()->quoteIdentifier($this->oTableConf->sqlData['name']);
    }

    /**
     * @param string[] $elements
     * @return string
     */
    private function getQuotedElements(array $elements): string
    {
        $connection = $this->getDatabaseConnection();

        return implode(',', array_map(function(string $id) use ($connection) {
            return $connection->quote($id);
        }, $elements));
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