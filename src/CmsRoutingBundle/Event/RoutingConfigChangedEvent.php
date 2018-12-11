<?php

namespace ChameleonSystem\CmsRoutingBundle\Event;

use Symfony\Component\EventDispatcher\Event;

class RoutingConfigChangedEvent extends Event
{
    /**
     * @var string
     */
    private $routingName;

    public function __construct(string $routingName)
    {
        $this->routingName = $routingName;
    }

    /**
     * @return string
     */
    public function getRoutingName(): string
    {
        return $this->routingName;
    }
}
