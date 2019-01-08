<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ChameleonSystem\CmsRoutingBundle\Event;

use Symfony\Component\EventDispatcher\Event;

class RoutingConfigChangedEvent extends Event
{
    /**
     * @var \TdbPkgCmsRouting
     */
    private $routing;

    public function __construct(\TdbPkgCmsRouting $routing)
    {
        $this->routing = $routing;
    }

    public function getRouting(): \TdbPkgCmsRouting
    {
        return $this->routing;
    }
}
