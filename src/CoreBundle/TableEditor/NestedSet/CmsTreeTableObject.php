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
    public function getId()
    {
        return $this->id;
    }

    public function getParentId()
    {
        if ('' === $this->fieldParentId) {
            return null;
        }

        return $this->fieldParentId;
    }

    public function getLeft()
    {
        return $this->fieldLft;
    }

    public function getRight()
    {
        return $this->fieldRgt;
    }
}
