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
     * @return null|string
     */
    public function getParentId();

    /**
     * @return string
     */
    public function getLeft();

    /**
     * @return string
     */
    public function getRight();
}
