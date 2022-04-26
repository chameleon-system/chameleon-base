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

interface NestedSetHelperFactoryInterface
{
    /**
     * @param string $tableName
     * @param string $parentIdField
     * @param string $nodeSortField
     *
     * @return NestedSetHelperInterface
     */
    public function createNestedSetHelper($tableName, $parentIdField = 'parent_id', $nodeSortField = 'position');
}
