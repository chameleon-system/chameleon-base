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
use TdbCmsTreeNode;

class ChangeNavigationTreeConnectionEvent extends Event
{
    /**
     * @var TdbCmsTreeNode
     */
    private $changedTreeConnection;

    /**
     * @param TdbCmsTreeNode $changedTreeConnection
     */
    public function __construct(TdbCmsTreeNode $changedTreeConnection)
    {
        $this->changedTreeConnection = $changedTreeConnection;
    }

    /**
     * @return TdbCmsTreeNode
     */
    public function getChangedTreeConnection()
    {
        return $this->changedTreeConnection;
    }
}
