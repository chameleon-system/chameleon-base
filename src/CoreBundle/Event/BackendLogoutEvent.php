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

class BackendLogoutEvent extends Event
{
    /**
     * @var TCMSUser|null
     */
    private $user;

    public function __construct(TCMSUser $user = null)
    {
        $this->user = $user;
    }

    /**
     * @return TCMSUser|null
     */
    public function getUser()
    {
        return $this->user;
    }
}
