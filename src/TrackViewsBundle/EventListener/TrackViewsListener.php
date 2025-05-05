<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ChameleonSystem\TrackViewsBundle\EventListener;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\RequestEvent;

class TrackViewsListener
{
    /**
     * @return void
     */
    public function onKernelRequest(RequestEvent $event)
    {
        if (!$event->isMainRequest()) {
            return;
        }
        $request = $event->getRequest();
        if (!$request->query->has('trackviews')) {
            return;
        }

        \TPkgTrackObjectViews::GetInstance()->WriteView();

        $dummyGifData = \base64_decode('R0lGODlhAQABAJAAAP8AAAAAACH5BAUQAAAALAAAAAABAAEAAAICBAEAOw==');

        $response = new Response();
        $response->headers->set('Content-Type', 'image/gif');
        $response->headers->set('Content-Length', (string) \strlen($dummyGifData));

        $response->setContent($dummyGifData);

        $event->stopPropagation();
        $event->setResponse($response);
    }
}
