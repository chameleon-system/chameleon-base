<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ChameleonSystem\CoreBundle\Tests\TableEditor\NestedSet\Mocks;

use ChameleonSystem\CoreBundle\TableEditor\NestedSet\NodeInterface;
use Doctrine\DBAL\Connection;

class NestedSetHelperTestNodeMock implements NodeInterface
{
    private $data = [];
    /**
     * @var Connection
     */
    private $databaseConnection;

    public function getId()
    {
        return $this->data['id'];
    }

    public function getParentId()
    {
        return ('' !== $this->data['parent_id']) ? $this->data['parent_id'] : null;
    }

    public function getLeft()
    {
        return $this->data['lft'];
    }

    public function getRight()
    {
        return $this->data['rgt'];
    }

    public function loadFromArray($sData)
    {
        $this->data = $sData;
    }

    public function loadFromId($id)
    {
        $query = 'select * from tree where id = :id';
        $this->data = $this->databaseConnection->fetchAssociative($query, ['id' => $id]);
    }

    public function setDatabaseConnection(Connection $connection)
    {
        $this->databaseConnection = $connection;
    }
}
