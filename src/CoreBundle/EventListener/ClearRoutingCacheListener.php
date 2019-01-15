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

use ChameleonSystem\CoreBundle\Routing\ChameleonBaseRouter;
use Symfony\Component\EventDispatcher\Event;

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

    public function clearRoutingCache(Event $event)
    {
        $this->router->clearCache();
    }
}
