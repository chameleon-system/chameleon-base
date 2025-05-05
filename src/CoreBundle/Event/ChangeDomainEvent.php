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

class ChangeDomainEvent extends Event
{
    /**
     * @var \TdbCmsPortalDomains[]
     */
    private $changedDomains;

    /**
     * @param \TdbCmsPortalDomains[] $changedDomains
     */
    public function __construct(array $changedDomains)
    {
        $this->changedDomains = $changedDomains;
    }

    /**
     * @return \TdbCmsPortalDomains[]
     */
    public function getChangedDomains()
    {
        return $this->changedDomains;
    }
}
