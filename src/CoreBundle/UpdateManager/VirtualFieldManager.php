<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ChameleonSystem\CoreBundle\UpdateManager;

use Doctrine\DBAL\Connection;

class VirtualFieldManager implements VirtualFieldManagerInterface
{
    /**
     * @var Connection
     */
    private $databaseConnection;

    /**
     * Uses tablename as key and fieldnames as values.
     *
     * @var array<string, string[]>
     */
    private $virtualFields;

    /**
     * @return void
     */
    public function setDatabaseConnection(Connection $connection)
    {
        $this->databaseConnection = $connection;
    }

    /**
     * {@inheritDoc}
     */
    public function getVirtualFieldsForTable($tableName)
    {
        $virtualFields = $this->getVirtualFields();

        return isset($virtualFields[$tableName]) ? $virtualFields[$tableName] : [];
    }

    /**
     * @return (mixed|string)[][]
     *
     * @psalm-return array<array<mixed|string>>
     */
    private function getVirtualFields()
    {
        if (null !== $this->virtualFields) {
            return $this->virtualFields;
        }

        $this->virtualFields = [];
        $query = "select cms_tbl_conf.name AS tablename, cms_field_conf.name AS fieldname
FROM cms_field_conf
INNER JOIN cms_tbl_conf on cms_field_conf.cms_tbl_conf_id = cms_tbl_conf.id
INNER JOIN cms_field_type on cms_field_conf.cms_field_type_id = cms_field_type.id
WHERE cms_field_type.mysql_type = ''";
        $all = $this->databaseConnection->fetchAllAssociative($query);
        foreach ($all as $data) {
            $tablename = $data['tablename'];
            if (!isset($this->virtualFields[$tablename])) {
                $this->virtualFields[$tablename] = [];
            }
            $this->virtualFields[$tablename][] = $data['fieldname'];
        }

        return $this->virtualFields;
    }
}
