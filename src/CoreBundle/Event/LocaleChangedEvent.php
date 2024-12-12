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

class LocaleChangedEvent extends Event
{
    private ?string $originalLocal = null;
    private ?string $newLocal = null;

    public function __construct(?string $newLocal, ?string $originalLocal = null)
    {
        $this->newLocal = $newLocal;
        $this->originalLocal = $originalLocal;
    }

    /**
     * @deprecated use getNewLocale instead
     */
    public function getNewLocal(): ?string
    {
        return $this->newLocal;
    }

    public function getNewLocale(): ?string
    {
        return $this->newLocal;
    }

    /**
     * @deprecated use getOriginalLocale instead
     */
    public function getOriginalLocal(): ?string
    {
        return $this->originalLocal;
    }

    public function getOriginalLocale(): ?string
    {
        return $this->originalLocal;
    }
}
