<?php

namespace ChameleonSystem\CoreBundle\EventListener;

use ChameleonSystem\CoreBundle\CoreEvents;
use ChameleonSystem\CoreBundle\Event\LocaleChangedEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Contracts\Translation\LocaleAwareInterface;

class CmsLocaleSyncListener implements EventSubscriberInterface
{
    public function __construct(private readonly LocaleAwareInterface $localSwitcher)
    {
    }

    public function onLocaleChanged(LocaleChangedEvent $event): void
    {
        $newLocale = $event->getNewLocale();

        $this->localSwitcher->setLocale($newLocale);
    }

    public static function getSubscribedEvents(): array
    {
        return [
            CoreEvents::LOCALE_CHANGED => 'onLocaleChanged',
        ];
    }
}
