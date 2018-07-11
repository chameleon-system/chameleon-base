<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ChameleonSystem\ExtranetBundle\objects;

use ChameleonSystem\ExtranetBundle\Interfaces\ExtranetUserEventInterface;
use Symfony\Component\EventDispatcher\Event;

class ExtranetUserEvent extends Event implements ExtranetUserEventInterface
{
    /**
     * @var \TdbDataExtranetUser
     */
    private $user;

    public function __construct(\TdbDataExtranetUser $user)
    {
        $this->user = $user;
    }

    /**
     * @return \TdbDataExtranetUser
     */
    public function getUser()
    {
        return $this->user;
    }
}
