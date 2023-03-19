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

interface AutoclassesDatabaseAdapterInterface
{
    /**
     * @param Connection $conn
     *
     * @return void
     */
    public function setConnection(Connection $conn): void;

    /**
     * @return string[]
     */
    public function getTableClassList(): array;

    /**
     * @return string[]
     */
    public function getVirtualClassList(): array;

    /**
     * @param string $id
     * @return string|null
     */
    public function getTableNameForId(string $id): ?string;
}
