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

use ChameleonSystem\CoreBundle\Event\LocaleChangedEvent;
use Symfony\Contracts\Translation\TranslatorInterface;

class ChangeTranslationLocaleListener
{
    /**
     * @var TranslatorInterface
     */
    private $translator;

    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    /**
     * @return void
     */
    public function onLocaleChangedEvent(LocaleChangedEvent $event)
    {
        $this->translator->setLocale($event->getNewLocal());
    }
}
