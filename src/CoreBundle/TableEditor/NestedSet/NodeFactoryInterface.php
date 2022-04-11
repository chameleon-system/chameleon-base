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

interface NodeFactoryInterface
{
    /**
     * @param $tableName
     * @param array $nodeData
     *
     * @return NodeInterface
     */
    public function createNodeFromArray($tableName, array $nodeData);

    /**
     * @param string $tableName
     * @param string $nodeId
     *
     * @return mixed
     */
    public function createNodeFromId($tableName, $nodeId);
}
