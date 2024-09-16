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

use Symfony\Contracts\EventDispatcher\Event;

class TreeIdMapCompletedEvent extends Event
{
    private array $treeIdMap;

    public function __construct(array $treeIdMap)
    {
        $this->treeIdMap = $treeIdMap;
    }

    public function getNewIdForSourceTreeId(string $sourceTreeId): ?string
    {
        if (array_key_exists($sourceTreeId, $this->treeIdMap)) {
            return $this->treeIdMap[$sourceTreeId];
        }

        return null;
    }
}
