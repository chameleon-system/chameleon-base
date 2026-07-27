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

use ChameleonSystem\CoreBundle\Exception\InvalidPortalDomainException;
use ChameleonSystem\CoreBundle\RequestType\RequestTypeInterface;
use ChameleonSystem\CoreBundle\Service\DomainPathMatch;
use ChameleonSystem\CoreBundle\Service\LanguageServiceInterface;
use ChameleonSystem\CoreBundle\Service\PortalDomainServiceInterface;
use ChameleonSystem\CoreBundle\Service\RequestInfoServiceInterface;
use ChameleonSystem\CoreBundle\Util\UrlPrefixGeneratorInterface;
use esono\pkgCmsRouting\exceptions\PortalNotFoundException;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\RequestEvent;

class CheckPortalDomainListener
{
    /**
     * @var PortalDomainServiceInterface
     */
    private $portalDomainService;
    /**
     * @var RequestInfoServiceInterface
     */
    private $requestInfoService;
    /**
     * @var bool
     */
    private $forcePrimaryDomain;
    private LanguageServiceInterface $languageService;
    private UrlPrefixGeneratorInterface $urlPrefixGenerator;

    /**
     * @param bool $forcePrimaryDomain
     */
    public function __construct(
        PortalDomainServiceInterface $portalDomainService,
        RequestInfoServiceInterface $requestInfoService,
        LanguageServiceInterface $languageService,
        UrlPrefixGeneratorInterface $urlPrefixGenerator,
        $forcePrimaryDomain = CHAMELEON_FORCE_PRIMARY_DOMAIN
    ) {
        $this->portalDomainService = $portalDomainService;
        $this->requestInfoService = $requestInfoService;
        $this->languageService = $languageService;
        $this->urlPrefixGenerator = $urlPrefixGenerator;
        $this->forcePrimaryDomain = $forcePrimaryDomain;
    }

    /**
     * @return void
     *
     * @throws PortalNotFoundException
     * @throws InvalidPortalDomainException
     * @throws \LogicException
     */
    public function onKernelRequest(RequestEvent $event)
    {
        if (!$event->isMainRequest()) {
            return;
        }

        $request = $event->getRequest();
        $requestType = $this->requestInfoService->getChameleonRequestType();
        if (null === $requestType) {
            throw new \LogicException('RequestTypeListener needs to run before CheckPortalDomainListener. Please check the priorities.');
        }
        if (RequestTypeInterface::REQUEST_TYPE_FRONTEND !== $requestType || $this->requestInfoService->isCmsTemplateEngineEditMode()) {
            return;
        }

        $hostFromRequest = $request->getHost();
        $activeDomain = $this->portalDomainService->getActiveDomain();
        if (null === $activeDomain) {
            throw new InvalidPortalDomainException("Chameleon is not set up to handle the domain '$hostFromRequest'. To use this domain, configure it via backend for at least one portal.");
        }

        $this->redirectToCanonicalUrlIfRequired($event, $activeDomain, $hostFromRequest);
    }

    /**
     * @param string $hostFromRequest
     *
     * @return void
     *
     * @throws PortalNotFoundException
     * @throws InvalidPortalDomainException
     */
    private function redirectToCanonicalUrlIfRequired(RequestEvent $event, \TdbCmsPortalDomains $activeDomain, $hostFromRequest)
    {
        $portal = $this->portalDomainService->getActivePortal();
        if (null === $portal) {
            throw new PortalNotFoundException('Unable to find an active portal matching this domain/portal prefix.');
        }

        $language = $this->languageService->getActiveLanguage();
        if (null === $language) {
            return;
        }

        $request = $event->getRequest();
        $targetDomain = $activeDomain;
        if (true === $this->forcePrimaryDomain) {
            $targetDomain = $this->portalDomainService->getPrimaryDomain($portal->id, $language->id);
        }

        $targetHost = $this->getDomainNameForRequest($targetDomain, $request->isSecure());
        $targetPath = $this->buildCanonicalPath($request, $portal, $language, $targetDomain);
        $targetUrl = $this->buildAbsoluteUrl($request, $targetHost, $targetPath);

        if ($this->isCurrentRequestCanonical($request, $targetUrl, $activeDomain, $hostFromRequest, $targetHost)) {
            return;
        }

        $statusCode = \in_array($request->getMethod(), ['GET', 'HEAD'], true) ? Response::HTTP_MOVED_PERMANENTLY : Response::HTTP_TEMPORARY_REDIRECT;
        $event->setResponse(new RedirectResponse($targetUrl, $statusCode));
    }

    private function buildCanonicalPath(
        \Symfony\Component\HttpFoundation\Request $request,
        \TdbCmsPortal $portal,
        \TdbCmsLanguage $language,
        \TdbCmsPortalDomains $targetDomain
    ): string {
        $domainPathMatch = $request->attributes->get(DomainPathMatch::REQUEST_ATTRIBUTE_NAME);
        if (false === $domainPathMatch instanceof DomainPathMatch || false === $domainPathMatch->isMatched()) {
            return $request->getPathInfo();
        }

        $remainingPath = $this->requestInfoService->getPathInfoWithoutPortalAndLanguagePrefix();
        $prefix = $this->urlPrefixGenerator->generatePrefixForDomain($portal, $language, $targetDomain);

        if ('/' === $remainingPath) {
            return '' === $prefix ? '/' : $prefix.'/';
        }

        if ('' === $prefix) {
            return $remainingPath;
        }

        return $prefix.$remainingPath;
    }

    private function buildAbsoluteUrl(\Symfony\Component\HttpFoundation\Request $request, string $host, string $path): string
    {
        $url = $request->getScheme().'://'.$host.$path;
        $queryString = $request->getQueryString();
        if (null !== $queryString && '' !== $queryString) {
            $url .= '?'.$queryString;
        }

        return $url;
    }

    private function isCurrentRequestCanonical(
        \Symfony\Component\HttpFoundation\Request $request,
        string $targetUrl,
        \TdbCmsPortalDomains $activeDomain,
        string $hostFromRequest,
        string $targetHost
    ): bool {
        $currentUrl = $request->getScheme().'://'.$hostFromRequest.$request->getRequestUri();
        if ($currentUrl === $targetUrl) {
            return true;
        }

        if (false === $this->forcePrimaryDomain) {
            return false;
        }

        $domainName = $activeDomain->GetActiveDomainName();

        return $activeDomain->fieldIsMasterDomain && $domainName === $hostFromRequest && $hostFromRequest === $targetHost;
    }

    private function getDomainNameForRequest(\TdbCmsPortalDomains $domain, bool $isSecureRequest): string
    {
        return true === $isSecureRequest ? $domain->getSecureDomainName() : $domain->getInsecureDomainName();
    }
}
