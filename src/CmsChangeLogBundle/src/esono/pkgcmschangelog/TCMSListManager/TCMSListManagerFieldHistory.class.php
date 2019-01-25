<?php

use ChameleonSystem\CoreBundle\ServiceLocator;
use Doctrine\DBAL\Connection;

class TCMSListManagerFieldHistory extends TCMSListManagerFullGroupTable
{


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