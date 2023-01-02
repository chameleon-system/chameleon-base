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
use TCMSUser;

/**
 * @deprecated 7.2 no longer used - use the standard symfony login event
 */
class BackendLoginEvent extends Event
{
    /**
     * @var TCMSUser
     */
    private $user;

    public function __construct(TCMSUser $user)
    {
        $this->user = $user;
    }

    /**
     * @return TCMSUser
     */
    public function getUser()
    {
        return $this->user;
    }
}
