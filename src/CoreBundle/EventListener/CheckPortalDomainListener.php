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
use ChameleonSystem\CoreBundle\Service\PortalDomainServiceInterface;
use ChameleonSystem\CoreBundle\Service\RequestInfoServiceInterface;
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

    /**
     * @param bool $forcePrimaryDomain
     */
    public function __construct(PortalDomainServiceInterface $portalDomainService, RequestInfoServiceInterface $requestInfoService, $forcePrimaryDomain = CHAMELEON_FORCE_PRIMARY_DOMAIN)
    {
        $this->portalDomainService = $portalDomainService;
        $this->requestInfoService = $requestInfoService;
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

        $this->redirectToPrimaryDomainIfRequired($event, $activeDomain, $hostFromRequest);
    }

    /**
     * @param string $hostFromRequest
     *
     * @return void
     *
     * @throws PortalNotFoundException
     * @throws InvalidPortalDomainException
     */
    private function redirectToPrimaryDomainIfRequired(RequestEvent $event, \TdbCmsPortalDomains $activeDomain, $hostFromRequest)
    {
        if (false === $this->forcePrimaryDomain) {
            return;
        }

        $domainName = $activeDomain->GetActiveDomainName();
        if ($activeDomain->fieldIsMasterDomain && $domainName === $hostFromRequest) {
            return;
        }

        $portal = $this->portalDomainService->getActivePortal();
        if (null === $portal) {
            throw new PortalNotFoundException('Unable to find an active portal matching this domain/portal prefix.');
        }

        $primaryDomain = $this->portalDomainService->getPrimaryDomain($portal->id);
        if ($hostFromRequest === $primaryDomain) {
            return;
        }

        $request = $event->getRequest();
        $newURL = $primaryDomain->GetActiveDomainName().$request->getPathInfo();

        $qs = $request->getQueryString();
        if (null !== $qs) {
            $newURL .= '?'.$qs;
        }
        $newURL = $request->getScheme().'://'.$newURL;
        $event->setResponse(new RedirectResponse($newURL, Response::HTTP_MOVED_PERMANENTLY));
    }
}
