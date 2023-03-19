<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ChameleonSystem\AutoclassesBundle\CacheWarmer;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception;

class AutoclassesDatabaseAdapter implements AutoclassesDatabaseAdapterInterface
{
    private Connection $conn;

    /**
     * {@inheritdoc}
     */
    public function setConnection(Connection $conn): void
    {
        $this->conn = $conn;
    }

    /**
     * {@inheritdoc}
     * @throws Exception
     */
    public function getTableClassList(): array
    {
        $all = $this->conn->fetchAllAssociative('SELECT `name` from `cms_tbl_conf`');

        return $this->getNamesArray($all);
    }

    /**
     * {@inheritdoc}
     * @throws Exception
     */
    public function getVirtualClassList(): array
    {
        $all = $this->conn->fetchAllAssociative('SELECT `name_of_entry_point` as `name` FROM `pkg_cms_class_manager`');

        return $this->getNamesArray($all);
    }

    /**
     * @return string[]
     */
    private function getNamesArray(array $all): array
    {
        $result = [];
        foreach ($all as $entry) {
            $result[] = $entry['name'];
        }

        return $result;
    }

    /**
     * {@inheritdoc}
     * @throws Exception
     */
    public function getTableNameForId(string $id): ?string
    {
        $result = $this->conn->fetchAllAssociative('SELECT `name` from `cms_tbl_conf` WHERE id=:id', ['id' => $id]);
        if (0 === count($result)) {
            return null;
        }

        return $result[0]['name'];
    }
}
