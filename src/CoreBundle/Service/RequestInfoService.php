<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ChameleonSystem\CoreBundle\Service;

use ChameleonSystem\CoreBundle\RequestType\RequestTypeInterface;
use ChameleonSystem\CoreBundle\ServiceLocator;
use ChameleonSystem\CoreBundle\Util\UrlPrefixGeneratorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

class RequestInfoService implements RequestInfoServiceInterface
{
    private ?string $pathInfoWithoutPortalAndLanguagePrefix = null;
    private ?string $requestId = null;

    /**
     * Cache of request type.
     */
    private ?int $chameleonRequestType = null;
    private ?bool $isCmsTemplateEngineEditModeCache = null;
    private ?bool $isPreviewModeCache = null;

    public function __construct(
        private readonly RequestStack $requestStack,
        private readonly PortalDomainServiceInterface $portalDomainService,
        private readonly LanguageServiceInterface $languageService,
        private readonly UrlPrefixGeneratorInterface $urlPrefixGenerator,
        private readonly PreviewModeServiceInterface $previewModeService
    ) {
    }

    /**
     * {@inheritdoc}
     */
    public function getChameleonRequestType(): int
    {
        if (null !== $this->chameleonRequestType) {
            return $this->chameleonRequestType;
        }

        $request = $this->getRequest();
        if (null === $request) {
            return RequestTypeInterface::REQUEST_TYPE_BACKEND; // no request means this is a console command - then we
            // behave as in the backend
        }

        $this->chameleonRequestType = (int) $request->attributes->get('chameleon.request_type');

        return $this->chameleonRequestType;
    }

    /**
     * {@inheritdoc}
     */
    public function setChameleonRequestType(int $requestType): void
    {
        $this->chameleonRequestType = $requestType;
    }

    /**
     * {@inheritdoc}
     *
     * @param int $requestType
     */
    public function isChameleonRequestType(int $requestType): bool
    {
        return $this->getChameleonRequestType() === $requestType;
    }

    protected function getRequest(): ?Request
    {
        return $this->requestStack->getCurrentRequest();
    }

    /**
     * {@inheritdoc}
     */
    public function isCmsTemplateEngineEditMode(): bool
    {
        if (null !== $this->isCmsTemplateEngineEditModeCache) {
            return $this->isCmsTemplateEngineEditModeCache;
        }

        $request = $this->requestStack->getCurrentRequest();
        if (null === $request) {
            return false;
        }
        $this->isCmsTemplateEngineEditModeCache = false === \TGlobal::IsCMSMode() && 'true' === $request->get('__modulechooser');

        return $this->isCmsTemplateEngineEditModeCache;
    }

    /**
     * {@inheritdoc}
     */
    public function isPreviewMode(): bool
    {
        if (null !== $this->isPreviewModeCache) {
            return $this->isPreviewModeCache;
        }

        $request = $this->requestStack->getCurrentRequest();
        if (null === $request) {
            return false;
        }

        $this->isPreviewModeCache = false === \TGlobal::IsCMSMode()
            && (
                true === $this->previewModeService->currentSessionHasPreviewAccess()
                || $this->checkTokenFromQueryParam($request)
            )
            && (
                'true' === $request->query->get('__previewmode')
                || 'true' === $request->query->get('preview')
            );

        return $this->isPreviewModeCache;
    }

    public function isFrontendJsDisabled(): bool
    {
        $request = $this->requestStack->getCurrentRequest();
        if (null === $request) {
            return false;
        }

        return 'true' === $request->query->get('disableFrontendJs') || 'true' === $request->get('disableFrontendJS');
    }

    /**
     * {@inheritdoc}
     */
    public function isBackendMode(): bool
    {
        return $this->isChameleonRequestType(RequestTypeInterface::REQUEST_TYPE_BACKEND);
    }

    /**
     * {@inheritdoc}
     */
    public function getPathInfoWithoutPortalAndLanguagePrefix(): string
    {
        if (null !== $this->pathInfoWithoutPortalAndLanguagePrefix) {
            return $this->pathInfoWithoutPortalAndLanguagePrefix;
        }

        $request = $this->getRequest();

        if (null === $request) {
            return '';
        }

        $fullPath = $request->getPathInfo();

        $activePortal = $this->portalDomainService->getActivePortal();
        if (null === $activePortal || $this->isBackendMode()) {
            return $fullPath;
        }

        $activeLanguage = $this->languageService->getActiveLanguage();

        $prefixToCut = $this->urlPrefixGenerator->generatePrefix($activePortal, $activeLanguage);

        if (empty($prefixToCut)) {
            return $fullPath;
        }

        if (str_starts_with($fullPath, $prefixToCut)) {
            $this->pathInfoWithoutPortalAndLanguagePrefix = substr($fullPath, strlen($prefixToCut));
        } else {
            $this->pathInfoWithoutPortalAndLanguagePrefix = $fullPath;
        }

        return $this->pathInfoWithoutPortalAndLanguagePrefix;
    }

    /**
     * {@inheritdoc}
     */
    public function getRequestId(): string
    {
        if (null === $this->requestId) {
            $this->requestId = \TTools::GetUUID();
        }

        return $this->requestId;
    }

    private function checkTokenFromQueryParam(Request $request): bool
    {
        $previewToken = $request->query->get('previewToken');
        if (null === $previewToken) {
            return false;
        }

        return $this->previewModeService->previewTokenExists($previewToken);
    }
}
