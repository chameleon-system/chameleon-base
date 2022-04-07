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

interface NestedSetHelperInterface
{
    /**
     * call after creating a new node to update all siblings.
     *
     * @param string $nodeId
     * @param string $parentId
     *
     * @return void
     */
    public function newNode($nodeId, $parentId);

    /**
     * @param NodeInterface $node - the after being moved before the lft and rgt fields have been updated
     *
     * @return void
     */
    public function updateNode(NodeInterface $node);

    /**
     * call before removing a node - will update all siblings.
     *
     * @param string $nodeId
     *
     * @return void
     */
    public function deleteNode($nodeId);

    /**
     * @return void
     */
    public function initializeTree();
}
