<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace esono\pkgCmsCounter;

use Doctrine\DBAL\Connection;

class CmsCounter
{
    /**
     * @var Connection
     */
    private $db;

    public function __construct(Connection $db)
    {
        $this->db = $db;
    }

    /**
     * return current counter value for named counter with given owner. returns null if no entry found.
     *
     * @param string $systemName
     *
     * @return int|null
     */
    public function getCurrentValue(\TCMSRecord $owner, $systemName)
    {
        $query = 'SELECT `value`
                    FROM `pkg_cms_counter`
                   WHERE `owner_table_name` = :ownerTable
                     AND `owner` = :ownerId
                     AND `system_name` = :systemName
                   LIMIT 1';
        $stmt = $this->db->prepare($query);
        $result = $stmt->executeQuery(['ownerTable' => $owner->table, 'ownerId' => $owner->id, 'systemName' => $systemName]);
        if (0 === $result->rowCount()) {
            return null;
        }

        $match = $result->fetchNumeric();
        $result->free();

        return intval($match[0]);
    }

    /**
     * create or update counter.
     *
     * @param string $systemName
     * @param int $value
     *
     * @return void
     */
    public function set(\TCMSRecord $owner, $systemName, $value)
    {
        $query = 'INSERT INTO `pkg_cms_counter`
                          SET `id` = :id,
                              `owner_table_name` = :ownerTable,
                              `owner` = :ownerId,
                              `system_name` = :systemName,
                              `name` = :systemName,
                              `value` = :value
      ON DUPLICATE KEY UPDATE `value` = :value
            ';
        $statement = $this->db->prepare($query);
        $result = $statement->executeQuery([':id' => \TTools::GetUUID(), 'ownerTable' => $owner->table, 'ownerId' => $owner->id, 'systemName' => $systemName, 'value' => intval($value)]);
        $result->free();
    }

    /**
     * returns next free counter number and blocks it from being used by another call.
     *
     * @param string $systemName
     *
     * @return int
     */
    public function get(\TCMSRecord $owner, $systemName)
    {
        $counterValue = 0;
        $this->db->beginTransaction();
        $query = 'SELECT `id`, `value`
                    FROM `pkg_cms_counter`
                   WHERE `owner_table_name` = :ownerTable
                     AND `owner` = :ownerId
                     AND `system_name` = :systemName
                   LIMIT 1
              FOR UPDATE
                    ';
        $statement = $this->db->prepare($query);
        $result = $statement->executeQuery(['ownerTable' => $owner->table, 'ownerId' => $owner->id, 'systemName' => $systemName]);
        if (0 === $result->rowCount()) {
            $result->free();
            $query = 'INSERT INTO `pkg_cms_counter`
                              SET `id` = :id,
                                  `owner_table_name` = :ownerTable,
                                  `owner` = :ownerId,
                                  `system_name` = :systemName,
                                  `name` = :systemName,
                                  `value` = :value
            ';
            $statement = $this->db->prepare($query);
            $statement->executeQuery([':id' => \TTools::GetUUID(), 'ownerTable' => $owner->table, 'ownerId' => $owner->id, 'systemName' => $systemName, 'value' => 2]);
            $counterValue = 1;
        } else {
            $currentCounterData = $result->fetchAssociative();
            $result->free();
            $counterValue = $currentCounterData['value'];
            $query = 'UPDATE `pkg_cms_counter` SET `value` = `value` + 1 WHERE `id` = :id';
            $statement = $this->db->prepare($query);
            $statement->executeQuery(['id' => $currentCounterData['id']]);
        }

        $this->db->commit();

        return intval($counterValue);
    }
}
