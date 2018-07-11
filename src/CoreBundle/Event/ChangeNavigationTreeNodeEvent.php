<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ChameleonSystem\CoreBundle\Event;

use Symfony\Component\EventDispatcher\Event;

class ChangeNavigationTreeNodeEvent extends Event
{
    /**
     * @var \TdbCmsTree[]
     */
    private $changedTreeNodes;

    /**
     * @param \TdbCmsTree[] $changedTreeNodes
     */
    public function __construct(array $changedTreeNodes)
    {
        $this->changedTreeNodes = $changedTreeNodes;
    }

    /**
     * @return \TdbCmsTree[]
     */
    public function getChangedTreeNodes()
    {
        return $this->changedTreeNodes;
    }
}
