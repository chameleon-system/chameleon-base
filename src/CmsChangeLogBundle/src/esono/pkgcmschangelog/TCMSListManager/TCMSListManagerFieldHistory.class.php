<?php

use ChameleonSystem\CoreBundle\ServiceLocator;
use Doctrine\DBAL\Connection;

class TCMSListManagerFieldHistory extends TCMSListManagerFullGroupTable
{


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
     * @param Connection $connection
     * @return string
     */
    private function getQuotedTableName(Connection $connection): string
    {
        return $connection->quoteIdentifier($this->oTableConf->sqlData['name']);
    }

    /**
     * @param Connection $connection
     * @param string[] $ids
     * @return string
     */
    private function getQuotedRestrictionIds(Connection $connection, array $ids): string
    {
        return implode(',', array_map(function(string $id) use ($connection) {
            return $connection->quote($id);
        }, $ids));
    }

    // Dependencies

    /**
     * @return Connection
     */
    private function getDatabaseConnection(): Connection
    {
        return ServiceLocator::get('database_connection');
    }

}