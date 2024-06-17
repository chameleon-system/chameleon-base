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
    /**
     * @var RequestStack
     */
    private $requestStack;
    /**
     * @var LanguageServiceInterface
     */
    private $languageService;
    /**
     * @var PortalDomainServiceInterface
     */
    private $portalDomainService;

    /**
     * @var null|string
     */
    private $pathInfoWithoutPortalAndLanguagePrefix;
    /**
     * @var UrlPrefixGeneratorInterface
     */
    private $urlPrefixGenerator;
    /**
     * @var string|null
     */
    private $requestId;

    /**
     * Cache of request type.
     *
     * @var int|null
     */
    private $chameleonRequestType;
    /**
     * @var bool|null
     */
    private $isCmsTemplateEngineEditModeCache;
    /**
     * @var bool|null
     */
    private $isPreviewModeCache;
    /**
     * @param RequestStack                 $requestStack
     * @param PortalDomainServiceInterface $portalDomainService
     * @param LanguageServiceInterface     $languageService
     * @param UrlPrefixGeneratorInterface  $urlPrefixGenerator
     */
    public function __construct(
        RequestStack $requestStack,
        PortalDomainServiceInterface $portalDomainService,
        LanguageServiceInterface $languageService,
        UrlPrefixGeneratorInterface $urlPrefixGenerator,
    ) {
        $this->requestStack = $requestStack;
        $this->languageService = $languageService;
        $this->portalDomainService = $portalDomainService;
        $this->urlPrefixGenerator = $urlPrefixGenerator;
    }

    /**
     * {@inheritdoc}
     */
    public function getChameleonRequestType()
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
    public function isChameleonRequestType($requestType)
    {
        return $this->getChameleonRequestType() === $requestType;
    }

    /**
     * @return Request|null
     */
    protected function getRequest()
    {
        return $this->requestStack->getCurrentRequest();
    }

    /**
     * {@inheritdoc}
     */
    public function isCmsTemplateEngineEditMode()
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
    public function isPreviewMode()
    {
        if (null !== $this->isPreviewModeCache) {
            return $this->isPreviewModeCache;
        }

        $request = $this->requestStack->getCurrentRequest();
        if (null === $request) {
            return false;
        }

        // todo: `__previewmode` should be the only way to enable this. Refactor all places where the preview attribute is set as `preview` instead of `__previewmode'
        // @note: preview mode laoding only via cookie is not possible, because this will kill all frontend logins
        $this->isPreviewModeCache = false === \TGlobal::IsCMSMode()
            && true === $this->getPreviewModeService()->currentSessionHasPreviewAccess()
            && (
                'true' === $request->query->get('__previewmode')
                 || 'true' === $request->query->get('preview')
                );

        return $this->isPreviewModeCache;
    }

    /**
     * {@inheritdoc}
     */
    public function isBackendMode()
    {
        return $this->isChameleonRequestType(RequestTypeInterface::REQUEST_TYPE_BACKEND);
    }

    /**
     * {@inheritdoc}
     */
    public function getPathInfoWithoutPortalAndLanguagePrefix()
    {
        if (null !== $this->pathInfoWithoutPortalAndLanguagePrefix) {
            return $this->pathInfoWithoutPortalAndLanguagePrefix;
        }

        $request = $this->getRequest();
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

        if (0 === strpos($fullPath, $prefixToCut)) {
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

    protected function getPreviewModeService(): PreviewModeServiceInterface
    {
        return ServiceLocator::get('chameleon_system_core.preview_mode_service');
    }
}
