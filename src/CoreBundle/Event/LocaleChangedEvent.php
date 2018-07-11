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

use Symfony\Component\EventDispatcher\Event;

class LocaleChangedEvent extends Event
{
    /**
     * @var null|string
     */
    private $originalLocal;
    /**
     * @var null|string
     */
    private $newLocal;

    /**
     * @param null|string $newLocal
     * @param null|string $originalLocal
     */
    public function __construct($newLocal, $originalLocal = null)
    {
        $this->newLocal = $newLocal;
        $this->originalLocal = $originalLocal;
    }

    /**
     * @return null|string
     */
    public function getNewLocal()
    {
        return $this->newLocal;
    }

    /**
     * @return null|string
     */
    public function getOriginalLocal()
    {
        return $this->originalLocal;
    }
}
