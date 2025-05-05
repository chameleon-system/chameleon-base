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

class ChangeUseSlashInSeoUrlsForPortalEvent extends Event
{
    /**
     * @var \TCMSPortal
     */
    private $portal;
    /**
     * @var bool
     */
    private $oldValue;
    /**
     * @var bool
     */
    private $newValue;

    /**
     * @param bool $oldValue
     * @param bool $newValue
     */
    public function __construct(\TCMSPortal $portal, $oldValue, $newValue)
    {
        $this->portal = $portal;
        $this->oldValue = $oldValue;
        $this->newValue = $newValue;
    }

    /**
     * @return \TCMSPortal
     */
    public function getPortal()
    {
        return $this->portal;
    }

    /**
     * @return bool
     */
    public function isOldValue()
    {
        return $this->oldValue;
    }

    /**
     * @return bool
     */
    public function isNewValue()
    {
        return $this->newValue;
    }
}
