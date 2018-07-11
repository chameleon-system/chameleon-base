<?php

namespace ChameleonSystem\CoreBundle\Security\AuthenticityToken;

use ChameleonSystem\CoreBundle\Service\RequestInfoServiceInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Csrf\CsrfTokenManager;
use Symfony\Component\Security\Csrf\TokenGenerator\TokenGeneratorInterface;
use Symfony\Component\Security\Csrf\TokenStorage\TokenStorageInterface;

/**
 * Provides an alternative namespace callback that allows to create separate CSRF tokens for frontend and backend.
 */
class CsrfTokenManagerFactory
{
    /**
     * @var TokenGeneratorInterface
     */
    private $tokenGenerator;
    /**
     * @var TokenStorageInterface
     */
    private $tokenStorage;
    /**
     * @var RequestStack
     */
    private $requestStack;
    /**
     * @var RequestInfoServiceInterface
     */
    private $requestInfoService;

    public function __construct(TokenGeneratorInterface $tokenGenerator, TokenStorageInterface $tokenStorage, RequestStack $requestStack, RequestInfoServiceInterface $requestInfoService)
    {
        $this->tokenGenerator = $tokenGenerator;
        $this->tokenStorage = $tokenStorage;
        $this->requestStack = $requestStack;
        $this->requestInfoService = $requestInfoService;
    }

    /**
     * @return CsrfTokenManager
     */
    public function createCsrfTokenManager()
    {
        /*
         * The namespace can only be constructed in the callback at runtime as the request is not yet available when
         * this factory method is executed.
         */
        $requestStack = $this->requestStack;
        $requestInfoService = $this->requestInfoService;
        $namespaceCallback = function () use ($requestStack, $requestInfoService) {
            $namespaceParts = [];

            if (true === $requestInfoService->isBackendMode() || true === $requestInfoService->isCmsTemplateEngineEditMode()) {
                $namespaceParts[] = 'backend-';
            } else {
                $namespaceParts[] = 'frontend-';
            }

            $request = $requestStack->getMasterRequest();
            if (null !== $request && true === $request->isSecure()) {
                $namespaceParts[] = 'https-';
            }

            return implode('', $namespaceParts);
        };

        return new CsrfTokenManager($this->tokenGenerator, $this->tokenStorage, $namespaceCallback);
    }
}
