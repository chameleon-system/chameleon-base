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

interface NodeInterface
{
    /**
     * @return string
     */
    public function getId();

    /**
     * @return string|null
     */
    public function getParentId();

    /**
     * @return int
     */
    public function getLeft();

    /**
     * @return int
     */
    public function getRight();
}
