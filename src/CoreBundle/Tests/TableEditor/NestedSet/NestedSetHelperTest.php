<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ChameleonSystem\CoreBundle\Tests\TableEditor\NestedSet;

use ChameleonSystem\CoreBundle\TableEditor\NestedSet\NestedSetHelper;
use ChameleonSystem\CoreBundle\TableEditor\NestedSet\NodeFactoryInterface;
use ChameleonSystem\CoreBundle\Tests\TableEditor\NestedSet\Mocks\NestedSetHelperTestNodeMock;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DriverManager;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;

class NestedSetHelperTest extends TestCase
{
    use ProphecyTrait;

    /**
     * @var Connection
     */
    private $db;
    private static $fixtureDir;
    /**
     * @var NestedSetHelper
     */
    private $nestedSetHelper;
    private $nodeName;
    /**
     * @var NodeFactoryInterface
     */
    private $nodeMockFactory;
    private $parentNodeName;
    private $nodePosition;

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        self::$fixtureDir = __DIR__.'/fixtures/';
    }

    protected function setUp(): void
    {
        parent::setUp();
        $this->db = null;
        $this->nestedSetHelper = null;
        $this->nodeName = null;
        $this->nodeMockFactory = null;
        $this->parentNodeName = null;
        $this->nodePosition = null;
        $config = new \Doctrine\DBAL\Configuration();
        $this->db = DriverManager::getConnection([
                'driver' => 'pdo_sqlite',
                'memory' => true,
            ],
            $config
        );

        $dbAlias = $this->db;
        /** @var NodeFactoryInterface|ObjectProphecy $mockNodeFactory */
        $mockNodeFactory = $this->prophesize('ChameleonSystem\CoreBundle\TableEditor\NestedSet\NodeFactoryInterface');
        $mockNodeFactory->createNodeFromId(Argument::any(), Argument::any())->will(
            function ($args, $mockNodeFactory) use ($dbAlias) {
                $node = new NestedSetHelperTestNodeMock();
                $node->setDatabaseConnection($dbAlias);
                $node->loadFromId($args[1]);

                return $node;
            }
        );
        $mockNodeFactory->createNodeFromArray(Argument::any(), Argument::any())->will(
            function ($args, $mockNodeFactory) use ($dbAlias) {
                $node = new NestedSetHelperTestNodeMock();
                $node->setDatabaseConnection($dbAlias);
                $node->loadFromArray($args[1]);

                return $node;
            }
        );

        $this->nodeMockFactory = $mockNodeFactory->reveal();

        $this->db->exec(file_get_contents(self::$fixtureDir.'/initial-tree.sql'));
        $this->validateTreeAgainst('initial-tree.txt');
    }

    /**
     * @test
     *
     * @dataProvider dataProviderDelete
     */
    public function itShouldRemoveANode($nodeName, $resultFile)
    {
        $this->given_a_nested_set_helper();
        $this->given_a_node_with_name($nodeName);
        $this->when_we_call_deleteNode();
        $this->then_we_should_get_a_tree_matching($resultFile);
    }

    /**
     * @test
     *
     * @dataProvider dataProviderInsert
     */
    public function itShouldInsertANode($nodeName, $parentNodeName, $resultFile)
    {
        $this->given_a_nested_set_helper();
        $this->given_a_node_with_name($nodeName);
        $this->given_a_parent_node_with_name($parentNodeName);
        $this->when_we_call_newNode();
        $this->then_we_should_get_a_tree_matching($resultFile);
    }

    /**
     * @test
     *
     * @dataProvider dataProviderMove
     */
    public function itShouldMoveARootNodeIntoAnotherRootNode($nodeName, $parentNodeName, $newNodePosition, $resultFile)
    {
        $this->given_a_nested_set_helper();
        $this->given_a_node_with_name($nodeName);
        $this->given_a_parent_node_with_name($parentNodeName);
        $this->given_a_new_node_position($newNodePosition);
        $this->when_we_call_updateNode();
        $this->then_we_should_get_a_tree_matching($resultFile);
    }

    /**
     * @test
     */
    public function itShouldInitializeATree()
    {
        $this->given_that_the_tree_has_no_lft_and_rgt_values_set();
        $this->given_a_nested_set_helper();
        $this->when_we_call_initializeTree();
        $this->then_we_should_get_a_tree_matching('initial-tree.txt');
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        $this->db->close();
    }

    private function validateTreeAgainst($file)
    {
        $query = 'SELECT COUNT(parent.name) - 1 AS depth, node.name AS name
                    FROM tree AS node,
                            tree AS parent
                    WHERE node.lft BETWEEN parent.lft AND parent.rgt
                    GROUP BY node.name
                    ORDER BY node.lft';
        $result = $this->db->fetchAllAssociative($query);
        $resultString = [];
        foreach ($result as $res) {
            $resultString[] = str_repeat('_', 2 * $res['depth']).$res['name'];
        }
        $resultString = trim(implode("\n", $resultString));
        $expected = trim(file_get_contents(self::$fixtureDir.'/'.$file));
        $this->assertEquals($expected, $resultString, "failed for node '{$this->nodeName}', parent '{$this->parentNodeName}', new position '{$this->nodePosition}' and file {$file}");
    }

    private function given_a_nested_set_helper()
    {
        $mockNodeFactory = $this->getNodeMockFactory();

        $this->nestedSetHelper = new NestedSetHelper($mockNodeFactory, 'tree', 'parent_id', 'position');
        $this->nestedSetHelper->setDatabaseConnection($this->db);
    }

    private function given_a_node_with_name($nodeName)
    {
        $this->nodeName = $nodeName;
    }

    private function given_a_parent_node_with_name($parentNodeName)
    {
        $this->parentNodeName = $parentNodeName;
    }

    private function given_a_new_node_position($newNodePosition)
    {
        $this->nodePosition = $newNodePosition;
    }

    /**
     * @param string $nodeName
     *
     * @return NestedSetHelperTestNodeMock
     */
    private function getNodeData($nodeName)
    {
        $query = 'select * from tree where name = :name';
        $data = $this->db->fetchAssociative($query, ['name' => $nodeName]);

        return $this->nodeMockFactory->createNodeFromArray('tree', $data);
    }

    private function when_we_call_deleteNode()
    {
        $node = $this->getNodeData($this->nodeName);
        $this->nestedSetHelper->deleteNode($node->getId());

        // Now we need to delete the node and all children
        $this->deleteRecursive($node->getId());
    }

    private function when_we_call_newNode()
    {
        $insertQuery = 'insert into tree (id, parent_id, lft, rgt, position, name)
          VALUES ( :newNodeId, :parentNodeId, 0, 0, :newNodePosition, :newNodeName)';

        $parameter = [
            'newNodeId' => $this->GetUUID(),
            'parentNodeId' => '',
            'newNodePosition' => 1,
            'newNodeName' => $this->nodeName,
        ];
        if (null !== $this->parentNodeName) {
            $parentNode = $this->getNodeData($this->parentNodeName);
            $parameter['parentNodeId'] = $parentNode->getId();
            $query = 'select MAX(position) from tree WHERE parent_id = :parentId';
            $max = $this->db->fetchNumeric($query, ['parentId' => $parentNode->getId()]);
            $parameter['newNodePosition'] = $max[0] + 1;
        } else {
            $query = "select MAX(position) from tree WHERE parent_id = ''";
            $max = $this->db->fetchNumeric($query);
            $parameter['newNodePosition'] = $max[0] + 1;
        }
        $this->db->executeUpdate($insertQuery, $parameter);

        $this->nestedSetHelper->newNode($parameter['newNodeId'], (null !== $this->parentNodeName) ? $parameter['parentNodeId'] : null);
    }

    private function when_we_call_updateNode()
    {
        $node = $this->getNodeData($this->nodeName);
        $newParent = null;
        if (null !== $this->parentNodeName) {
            $newParent = $this->getNodeData($this->parentNodeName);
        }

        $parentId = (null !== $newParent) ? $newParent->getId() : '';
        $query = 'UPDATE tree set position = position + 1 where parent_id = :newParentId and position >= :newPosition';
        $this->db->executeUpdate($query,
            [
                'newParentId' => $parentId,
                'newPosition' => $this->nodePosition,
            ],
            [
                'newParentId' => \PDO::PARAM_STR,
                'newPosition' => \PDO::PARAM_INT,
            ]
        );

        $query = 'update tree set parent_id = :newParentId, position = :newPosition where id = :nodeId';
        $this->db->executeUpdate($query,
            [
                'newParentId' => $parentId,
                'nodeId' => $node->getId(),
                'newPosition' => $this->nodePosition,
            ],
            [
                'newParentId' => \PDO::PARAM_STR,
                'nodeId' => \PDO::PARAM_STR,
                'newPosition' => \PDO::PARAM_INT,
            ]
        );
        $node = $this->getNodeData($this->nodeName);
        $this->nestedSetHelper->updateNode($node);
    }

    private function then_we_should_get_a_tree_matching($expectedResultFileName)
    {
        $this->validateTreeAgainst($expectedResultFileName);
    }

    /**
     * @return NodeFactoryInterface
     */
    private function getNodeMockFactory()
    {
        return $this->nodeMockFactory;
    }

    private function deleteRecursive($id)
    {
        $query = 'delete from tree where id = :id';
        $this->db->executeUpdate($query, ['id' => $id]);
        $childrenQuery = 'select * from tree where parent_id = :id';
        $children = $this->db->fetchAllAssociative($childrenQuery, ['id' => $id]);
        foreach ($children as $child) {
            $this->deleteRecursive($child['id']);
        }
    }

    public function dataProviderDelete()
    {
        return [
            [
                'Jackets',
                'removed_leaf_node.txt',
            ],
            [
                'Slacks',
                'removed_leaf_not_that_is_the_first_child.txt',
            ],
            [
                'Dresses',
                'removed_subtree.txt',
            ],
            [
                'Women\'s',
                'removed_root_node.txt',
            ],
        ];
    }

    public function dataProviderInsert()
    {
        return [
            [
                'Shoes',
                null,
                'insert_root_node.txt',
            ],
            [
                'Children',
                'Clothing',
                'insert_into_subtree.txt',
            ],
            [
                'Short',
                'Sun Dresses',
                'insert_into_leaf.txt',
            ],
        ];
    }

    public function dataProviderMove()
    {
        return [
            // move root to root
            [
                'Clothing',
                'Other',
                1,
                'move_root_to_root.txt',
            ],
            // move leaf to another parent
            [
                'Evening Gowns',
                'Suits',
                2,
                'move_leaf_node_to_subtree.txt',
            ],
            // move subtree to another subtree
            [
                'Dresses',
                'Suits',
                1,
                'move_subtree_to_another_subtree.txt',
            ],
            // call move without a changing anything
            [
                'Dresses',
                'Women\'s',
                1,
                'initial-tree.txt',
            ],
            // moving a root node to the first position
            [
                'Other',
                null,
                1,
                'move_root_node_to_first_root_position.txt',
            ],
        ];
    }

    private function GetUUID($prefix = '')
    {
        $chars = bin2hex(openssl_random_pseudo_bytes(16));
        $uuid = substr($chars, 0, 8).'-';
        $uuid .= substr($chars, 8, 4).'-';
        $uuid .= substr($chars, 12, 4).'-';
        $uuid .= substr($chars, 16, 4).'-';
        $uuid .= substr($chars, 20, 12);

        return $prefix.$uuid;
    }

    private function given_that_the_tree_has_no_lft_and_rgt_values_set()
    {
        $query = 'update tree set lft = 0, rgt = 0';
        $this->db->executeUpdate($query);
    }

    private function when_we_call_initializeTree()
    {
        $this->nestedSetHelper->initializeTree();
    }
}
