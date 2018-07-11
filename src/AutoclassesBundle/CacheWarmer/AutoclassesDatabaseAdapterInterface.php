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
     */
    public function setConnection(Connection $conn);

    /**
     * @return array
     */
    public function getTableClassList();

    /**
     * @return array
     */
    public function getVirtualClassList();

    /**
     * @return string|null
     */
    public function getTableNameForId($id);
}
