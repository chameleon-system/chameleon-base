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

class CmsTreeTableObject extends \ChameleonSystemCoreBundleTableEditorNestedSetCmsTreeTableObjectAutoParent implements NodeInterface
{
    /**
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string|null
     */
    public function getParentId()
    {
        if ('' === $this->fieldParentId) {
            return null;
        }

        return $this->fieldParentId;
    }

    /**
     * @return int
     *
     * @psalm-suppress InvalidReturnStatement, InvalidReturnType - Technically returning an `int` here
     */
    public function getLeft()
    {
        return $this->fieldLft;
    }

    /**
     * @return int
     *
     * @psalm-suppress InvalidReturnStatement, InvalidReturnType - Technically returning an `int` here
     */
    public function getRight()
    {
        return $this->fieldRgt;
    }
}
