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
use ChameleonSystem\CoreBundle\Service\RequestInfoServiceInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\RequestEvent;

/**
 * Allow the calling domain to see this page (e.g. in an iframe).
 */
class AllowEmbeddingForDifferentDomainListener
{
    public function __construct(
        private readonly CmsPortalDomainsDataAccessInterface $domainsDataAccess,
        private readonly RequestInfoServiceInterface $requestInfoService
    ) {
    }

    /**
     * @return void
     */
    public function onKernelRequest(RequestEvent $event)
    {
        $request = $event->getRequest();

        if (false === $this->requestInfoService->isPreviewMode()) {
            return;
        }

        $refererHost = $this->getRefererHost($request);
        if (null === $refererHost || $request->getHost() === $refererHost) {
            return;
        }

        if (false === $this->isConfiguredDomain($refererHost)) {
            return;
        }

        header("Content-Security-Policy: frame-ancestors $refererHost;");
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
