<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ChameleonSystem\CoreBundle\EventListener;

use ChameleonSystem\CmsRoutingBundle\Event\RoutingConfigChangedEvent;
use ChameleonSystem\CoreBundle\Routing\ChameleonBaseRouter;

class ClearRoutingCacheListener
{
    /**
     * @var ChameleonBaseRouter
     */
    private $router;

    /**
     * @param ChameleonBaseRouter $router
     */
    public function __construct(ChameleonBaseRouter $router)
    {
        $this->router = $router;
    }

    public function clearRoutingCache(RoutingConfigChangedEvent $event)
    {
        $this->router->clearCache();
    }
}
