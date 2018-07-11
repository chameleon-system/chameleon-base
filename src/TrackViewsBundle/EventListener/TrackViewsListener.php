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
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use TPkgTrackObjectViews;

class TrackViewsListener
{
    public function onKernelRequest(GetResponseEvent $event)
    {
        if (!$event->isMasterRequest()) {
            return;
        }
        $request = $event->getRequest();
        if (!$request->query->has('trackviews')) {
            return;
        }

        TPkgTrackObjectViews::GetInstance()->WriteView();

        $name = PATH_USER_CMS_PUBLIC.'/blackbox/images/spacer.gif';

        $response = new Response();
        $response->headers->set('Content-Type', 'image/gif');
        $response->headers->set('Content-Length', ''.filesize($name));

        $response->setContent(file_get_contents($name));

        $event->stopPropagation();
        $event->setResponse($response);
    }
}
