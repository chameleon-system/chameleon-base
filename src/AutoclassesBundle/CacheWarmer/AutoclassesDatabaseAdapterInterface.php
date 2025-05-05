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
     * @return void
     */
    public function setConnection(Connection $conn);

    /**
     * @return string[]
     */
    public function getTableClassList();

    /**
     * @return string[]
     */
    public function getVirtualClassList();

    /**
     * @param string $id
     *
     * @return string|null
     */
    public function getTableNameForId($id);
}
