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

class ChangeActiveDomainEvent extends Event
{
    /**
     * @var \TCMSPortalDomain|null
     */
    private $oldActiveDomain;
    /**
     * @var \TCMSPortalDomain|null
     */
    private $newActiveDomain;

    /**
     * @return \TCMSPortalDomain|null
     */
    public function getNewActiveDomain()
    {
        return $this->newActiveDomain;
    }

    /**
     * @return \TCMSPortalDomain|null
     */
    public function getOldActiveDomain()
    {
        return $this->oldActiveDomain;
    }

    public function __construct(?\TCMSPortalDomain $oldActiveDomain = null, ?\TCMSPortalDomain $newActiveDomain = null)
    {
        $this->oldActiveDomain = $oldActiveDomain;
        $this->newActiveDomain = $newActiveDomain;
    }
}
