<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ChameleonSystem\CoreBundle\TableEditor\NestedSet;

use Doctrine\DBAL\Connection;

class NestedSetHelperFactory implements NestedSetHelperFactoryInterface
{
    /**
     * @var Connection
     */
    private $connection;
    /**
     * @var NodeFactoryInterface
     */
    private $nodeFactory;

    public function __construct(NodeFactoryInterface $nodeFactory)
    {
        $this->nodeFactory = $nodeFactory;
    }

    /**
     * @return void
     */
    public function setDatabaseConnection(Connection $connection)
    {
        $this->connection = $connection;
    }

    /**
     * @param string $tableName
     * @param string $parentIdField
     * @param string $nodeSortField
     *
     * @return NestedSetHelperInterface
     */
    public function createNestedSetHelper($tableName, $parentIdField = 'parent_id', $nodeSortField = 'position')
    {
        $nestedSetHelper = new NestedSetHelper($this->nodeFactory, $tableName, $parentIdField, $nodeSortField);
        $nestedSetHelper->setDatabaseConnection($this->connection);

        return $nestedSetHelper;
    }
}
