<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ChameleonSystem\CoreBundle\Controller;

use Symfony\Contracts\EventDispatcher\Event;

class ChameleonControllerInvokeEvent extends Event
{
    /**
     * @var ChameleonControllerInterface
     */
    private $controller;

    public function __construct(ChameleonControllerInterface $controller)
    {
        $this->controller = $controller;
    }

    /**
     * @return ChameleonControllerInterface
     */
    public function getController()
    {
        return $this->controller;
    }
}
