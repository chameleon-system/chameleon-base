<?php

namespace ChameleonSystem\CoreBundle\EventListener;

use ChameleonSystem\CoreBundle\Event\ChangeActivePageEvent;

/**
 * Use a no-cache header for every secure page so that browser "back" does not get these pages from the cache.
 *
 * @see https://redmine.esono.de/issues/33950
 */
class NoCacheForSecurePageListener
{
    public function onChangeActivePage(ChangeActivePageEvent $event): void
    {
        $pageIsProtected = true === $event->getNewActivePage()->fieldExtranetPage;

        if ($pageIsProtected) {
            header('Cache-Control: no-cache, no-store, must-revalidate');
        }
    }
}
