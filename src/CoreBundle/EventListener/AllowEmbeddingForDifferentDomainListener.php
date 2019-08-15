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

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;

/**
 * Allow the calling domain to see this page (e.g. in an iframe).
 */
class AllowEmbeddingForDifferentDomainListener
{
    /**
     * @param GetResponseEvent $event
     */
    public function onKernelRequest(GetResponseEvent $event)
    {
        $request = $event->getRequest();

        if (false === $this->allowAdditionalDomain($request)) {
            return;
        }

        $refererHost = $this->getRefererHost();
        if (null === $refererHost || $request->getHost() === $refererHost) {
            return;
        }

        header("X-Frame-Options: ALLOW-FROM $refererHost");
    }

    private function allowAdditionalDomain(Request $request): bool
    {
        // TODO the priority in service definition as low as it is is necessary for CMSUserDefined() to work..

        return 'true' === $request->get('__previewmode') && true === \TGlobalBase::CMSUserDefined();
    }

    private function getRefererHost(): ?string
    {
        // TODO can the referer be determined more high-level than $_SERVER?

        if (false === \array_key_exists('HTTP_REFERER', $_SERVER)) {
            return null;
        }

        $urlParts = parse_url($_SERVER['HTTP_REFERER']);

        if (false === \array_key_exists('host', $urlParts)) {
            return null;
        }

        return $urlParts['host'];
    }
}
