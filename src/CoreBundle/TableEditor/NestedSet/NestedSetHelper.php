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

class NestedSetHelper implements NestedSetHelperInterface
{
    /**
     * @var Connection
     */
    private $connection;

    /**
     * @var string
     */
    private $tableName;

    /**
     * @var string
     */
    private $parentIdField;

    /**
     * @var string
     */
    private $nodeSortField;
    /**
     * @var NodeFactoryInterface
     */
    private $nodeFactory;

    /**
     * @param string $tableName
     * @param string $parentIdField
     * @param string $nodeSortField
     */
    public function __construct(NodeFactoryInterface $nodeFactory, $tableName, $parentIdField = 'parent_id', $nodeSortField = 'position')
    {
        $this->tableName = $tableName;
        $this->nodeFactory = $nodeFactory;
        $this->parentIdField = $parentIdField;
        $this->nodeSortField = $nodeSortField;
    }

    /**
     * @return void
     */
    public function setDatabaseConnection(Connection $connection)
    {
        $this->connection = $connection;
    }

    /**
     * call after creating a new node to update all siblings
     * {@inheritdoc}
     */
    public function newNode($nodeId, $parentId)
    {
        $newLeft = 0;
        $parent = (null !== $parentId) ? $this->getNodeData($parentId) : null;

        $siblings = $this->getChildren($parentId);

        $hasNoSiblingsOtherThanSelf = 1 === count($siblings);
        $firstSiblingIsSelf = ($siblings[0]->getId() === $nodeId);
        if ($hasNoSiblingsOtherThanSelf || $firstSiblingIsSelf) {
            $newLeft = 0;
            if (null !== $parent) {
                $newLeft = $parent->getLeft();
            }
            ++$newLeft;
        } else {
            foreach ($siblings as $sibling) {
                if ($sibling->getId() === $nodeId) {
                    break;
                }
                $newLeft = $sibling->getRight() + 1;
            }
        }
        $newRight = $newLeft + 1;

        $this->createSpaceForSubtree($newLeft, 2);
        $this->updateNodeLeftAndRightValues($nodeId, $newLeft, $newRight);
    }

    /**
     * call when a nodes data changes - this will update all siblings and children of the source and target nodes.
     *
     * @see http://www.ninthavenue.com.au/how-to-move-a-node-in-nested-sets-with-sql
     * @see http://mikehillyer.com/articles/managing-hierarchical-data-in-mysql/
     * {@inheritdoc}
     */
    public function updateNode(NodeInterface $node)
    {
        $newPosition = $this->calculateNodePosition($node);

        $subtreeWidth = $node->getRight() - $node->getLeft() + 1;

        $distanceFromOldPosition = $newPosition - $node->getLeft(); // newpos - oldpos
        if (0 === $distanceFromOldPosition) {
            return; // nothing changes
        }

        $tmppos = $node->getLeft();

        // backwards movement must account for new space
        if ($distanceFromOldPosition < 0) {
            $distanceFromOldPosition = $distanceFromOldPosition - $subtreeWidth;
            $tmppos = $tmppos + $subtreeWidth;
        }

        // create new space for subtree
        $this->createSpaceForSubtree($newPosition, $subtreeWidth);

        // move subtree into new space
        $query = "UPDATE `{$this->tableName}`
                     SET lft = lft + :distanceFromOldPosition, rgt = rgt + :distanceFromOldPosition
                   WHERE lft >= :tmppos AND rgt < :tmppos + :subtreeWidth";
        $this->connection->executeUpdate($query,
            [
                'distanceFromOldPosition' => $distanceFromOldPosition,
                'tmppos' => $tmppos,
                'subtreeWidth' => $subtreeWidth,
            ]
        );

        // remove old space vacated by subtree
        $this->removeSubtreeSpace($node->getRight(), $subtreeWidth);
    }

    /**
     * @param int $subtreeWidth
     * @param int $subtreePosition
     *
     * @return void
     */
    private function createSpaceForSubtree($subtreePosition, $subtreeWidth)
    {
        $query = "update `{$this->tableName}` SET lft = lft + :subtreeWidth WHERE lft >= :newPosition";
        $this->connection->executeUpdate($query,
            [
                'subtreeWidth' => $subtreeWidth,
                'newPosition' => $subtreePosition,
            ]
        );
        $query = "update `{$this->tableName}` SET rgt = rgt + :subtreeWidth WHERE rgt >= :newPosition";
        $this->connection->executeUpdate($query,
            [
                'subtreeWidth' => $subtreeWidth,
                'newPosition' => $subtreePosition,
            ]
        );
    }

    /**
     * {@inheritdoc}
     */
    public function deleteNode($nodeId)
    {
        $node = $this->getNodeData($nodeId);
        $subtreeWidth = $node->getRight() - $node->getLeft() + 1;
        $this->removeSubtreeSpace($node->getRight(), $subtreeWidth);
    }

    /**
     * @param int $subtreeRightValue
     * @param int $subtreeWidth
     *
     * @return void
     */
    private function removeSubtreeSpace($subtreeRightValue, $subtreeWidth)
    {
        $query = "UPDATE `{$this->tableName}` SET lft = lft - :subtreeWidth WHERE lft > :oldRgt";
        $this->connection->executeUpdate($query,
            [
                'subtreeWidth' => $subtreeWidth,
                'oldRgt' => $subtreeRightValue,
            ]
        );
        $query = "UPDATE `{$this->tableName}` SET rgt = rgt - :subtreeWidth WHERE rgt > :oldRgt";
        $this->connection->executeUpdate($query,
            [
                'subtreeWidth' => $subtreeWidth,
                'oldRgt' => $subtreeRightValue,
            ]
        );
    }

    /**
     * {@inheritDoc}
     */
    public function initializeTree()
    {
        $left = 0;
        $rootNodes = $this->getChildren(null);
        foreach ($rootNodes as $rootNode) {
            ++$left;
            $left = $this->initializeNode($rootNode, $left);
        }
    }

    /**
     * @param int $left
     *
     * @return int
     */
    private function initializeNode(NodeInterface $node, $left)
    {
        $this->updateNodeLeftValue($node->getId(), $left);
        $children = $this->getChildren($node->getId());
        foreach ($children as $child) {
            ++$left;
            $left = $this->initializeNode($child, $left);
        }
        ++$left;
        $this->updateNodeRightValue($node->getId(), $left);

        return $left;
    }

    /**
     * @param string $nodeId
     *
     * @return NodeInterface
     */
    private function getNodeData($nodeId)
    {
        return $this->nodeFactory->createNodeFromId($this->tableName, $nodeId);
    }

    /**
     * @param string|null $parentId
     *
     * @return NodeInterface[]
     */
    private function getChildren($parentId)
    {
        $quotedTableName = $this->connection->quoteIdentifier($this->tableName);
        $quotedParentIdField = $this->connection->quoteIdentifier($this->parentIdField);
        $quotedNodeSortField = $this->connection->quoteIdentifier($this->nodeSortField);
        $query = "SELECT * FROM $quotedTableName WHERE $quotedParentIdField = :parentId ORDER BY $quotedNodeSortField ASC";
        $children = $this->connection->fetchAllAssociative($query,
            [
                'parentId' => (null !== $parentId) ? $parentId : '',
            ]
        );

        $childrenObject = [];
        foreach ($children as $child) {
            $childrenObject[] = $this->nodeFactory->createNodeFromArray($this->tableName, $child);
        }

        return $childrenObject;
    }

    /**
     * @param string $nodeId
     * @param int $newLeft
     * @param int $newRight
     *
     * @return void
     */
    private function updateNodeLeftAndRightValues($nodeId, $newLeft, $newRight)
    {
        $query = "UPDATE {$this->tableName} SET rgt = :newRight, lft = :newLeft WHERE id = :nodeId";
        $this->connection->executeUpdate($query,
            [
                'newLeft' => $newLeft,
                'newRight' => $newRight,
                'nodeId' => $nodeId,
            ]
        );
    }

    /**
     * @param string $nodeId
     * @param int $newLeft
     *
     * @return void
     */
    private function updateNodeLeftValue($nodeId, $newLeft)
    {
        $query = "UPDATE {$this->tableName} SET lft = :newLeft WHERE id = :nodeId";
        $this->connection->executeUpdate($query,
            [
                'newLeft' => $newLeft,
                'nodeId' => $nodeId,
            ]
        );
    }

    /**
     * @param string $nodeId
     * @param int $newRight
     *
     * @return void
     */
    private function updateNodeRightValue($nodeId, $newRight)
    {
        $query = "UPDATE {$this->tableName} SET rgt = :newRight WHERE id = :nodeId";
        $this->connection->executeUpdate($query,
            [
                'newRight' => $newRight,
                'nodeId' => $nodeId,
            ]
        );
    }

    /**
     * if we are the first sibling
     *      if we have a parent, then newPos = parent.lft + 1
     *      else newPos = 1
     * else newPos = previousSibling.rgt + 1.
     *
     * @return int
     */
    private function calculateNodePosition(NodeInterface $node)
    {
        $newPosition = 0;
        $siblings = $this->getChildren($node->getParentId());
        if ($siblings[0]->getId() === $node->getId()) {
            if (null !== $node->getParentId()) {
                $parentNode = $this->getNodeData($node->getParentId());
                $newPosition = $parentNode->getLeft() + 1;

                return $newPosition;
            } else {
                $newPosition = 1;

                return $newPosition;
            }
        } else {
            foreach ($siblings as $sibling) {
                if ($sibling->getId() === $node->getId()) {
                    break;
                }
                $newPosition = $sibling->getRight() + 1;
            }

            return $newPosition;
        }
    }
}
