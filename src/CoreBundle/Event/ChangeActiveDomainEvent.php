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
use TCMSPortalDomain;

class ChangeActiveDomainEvent extends Event
{
    /**
     * @var TCMSPortalDomain
     */
    private $oldActiveDomain;
    /**
     * @var TCMSPortalDomain
     */
    private $newActiveDomain;

    /**
     * @return TCMSPortalDomain
     */
    public function getNewActiveDomain()
    {
        return $this->newActiveDomain;
    }

    /**
     * @return TCMSPortalDomain
     */
    public function getOldActiveDomain()
    {
        return $this->oldActiveDomain;
    }

    public function __construct(TCMSPortalDomain $oldActiveDomain = null, TCMSPortalDomain $newActiveDomain)
    {
        $this->oldActiveDomain = $oldActiveDomain;
        $this->newActiveDomain = $newActiveDomain;
    }
}
