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
     * @var string
     */
    private $routingId;

    public function __construct(string $routingId)
    {
        $this->routingId = $routingId;
    }

    public function getRoutingId(): string
    {
        return $this->routingId;
    }
}
