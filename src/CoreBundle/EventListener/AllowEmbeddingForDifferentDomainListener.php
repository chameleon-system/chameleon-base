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

use ChameleonSystem\CoreBundle\DataAccess\CmsPortalDomainsDataAccessInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\RequestEvent;

/**
 * Allow the calling domain to see this page (e.g. in an iframe).
 */
class AllowEmbeddingForDifferentDomainListener
{
    /**
     * @var CmsPortalDomainsDataAccessInterface
     */
    private $domainsDataAccess;

    public function __construct(CmsPortalDomainsDataAccessInterface $domainsDataAccess)
    {
        $this->domainsDataAccess = $domainsDataAccess;
    }

    /**
     * @return void
     */
    public function onKernelRequest(RequestEvent $event)
    {
        $request = $event->getRequest();

        if (false === $this->isPreviewMode($request)) {
            return;
        }

        $refererHost = $this->getRefererHost($request);
        if (null === $refererHost || $request->getHost() === $refererHost) {
            return;
        }

        if (false === $this->isConfiguredDomain($refererHost)) {
            return;
        }

        header("X-Frame-Options: ALLOW-FROM $refererHost");
    }

    private function isPreviewMode(Request $request): bool
    {
        return 'true' === $request->get('__previewmode');
    }

    private function getRefererHost(Request $request): ?string
    {
        $referer = $request->server->get('HTTP_REFERER');

        if (null === $referer) {
            return null;
        }

        $urlParts = parse_url($referer);

        if (false === \array_key_exists('host', $urlParts)) {
            return null;
        }

        return $urlParts['host'];
    }

    private function isConfiguredDomain(string $refererHost): bool
    {
        $domainNames = $this->domainsDataAccess->getAllDomainNames();

        return true === \in_array($refererHost, $domainNames, true);
    }
}
