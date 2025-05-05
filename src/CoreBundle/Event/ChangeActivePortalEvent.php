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

class ChangeActivePortalEvent extends Event
{
    /**
     * @var \TCMSPortal|null
     */
    private $oldActivePortal;

    /**
     * @var \TCMSPortal|null
     */
    private $newActivePortal;

    /**
     * @return \TCMSPortal|null
     */
    public function getNewActivePortal()
    {
        return $this->newActivePortal;
    }

    /**
     * @return \TCMSPortal|null
     */
    public function getOldActivePortal()
    {
        return $this->oldActivePortal;
    }

    public function __construct(?\TCMSPortal $oldActivePortal = null, ?\TCMSPortal $newActivePortal = null)
    {
        $this->oldActivePortal = $oldActivePortal;
        $this->newActivePortal = $newActivePortal;
    }
}
