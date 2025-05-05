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

class NodeFactory implements NodeFactoryInterface
{
    /**
     * @var array<string, class-string>
     */
    private $tableToClassCache = [];

    /**
     * {@inheritdoc}
     */
    public function createNodeFromArray($tableName, array $nodeData)
    {
        $className = $this->getClassNameForTable($tableName);

        return call_user_func([$className, 'GetNewInstance'], $nodeData);
    }

    /**
     * {@inheritdoc}
     */
    public function createNodeFromId($tableName, $nodeId)
    {
        $className = $this->getClassNameForTable($tableName);

        return call_user_func([$className, 'GetNewInstance'], $nodeId);
    }

    /**
     * @param string $tableName
     *
     * @return class-string
     */
    private function getClassNameForTable($tableName)
    {
        if (isset($this->tableToClassCache[$tableName])) {
            return $this->tableToClassCache[$tableName];
        }
        $this->tableToClassCache[$tableName] = \TCMSTableToClass::GetClassName(\TCMSTableToClass::PREFIX_CLASS, $tableName);

        return $this->tableToClassCache[$tableName];
    }
}
