<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ChameleonSystem\CoreBundle\Controller;

use ChameleonSystem\CoreBundle\Service\PageServiceInterface;
use ChameleonSystem\CoreBundle\Service\PortalDomainServiceInterface;
use ChameleonSystem\CoreBundle\Service\RequestInfoServiceInterface;
use ChameleonSystem\ExtranetBundle\Interfaces\ExtranetConfigurationInterface;
use ChameleonSystem\ExtranetBundle\Interfaces\ExtranetUserProviderInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Controller\ErrorController;

/**
 * {@inheritdoc}
 */
class ExceptionController extends ErrorController
{
    /**
     * @var ChameleonControllerInterface
     */
    private $mainController;

    /**
     * @var PortalDomainServiceInterface
     */
    private $portalDomainService;

    /**
     * @var ExtranetConfigurationInterface
     */
    private $extranetConfiguration;

    /**
     * @var RequestInfoServiceInterface
     */
    private $requestInfoService;

    /**
     * @var ExtranetUserProviderInterface
     */
    private $extranetUserProvider;

    /**
     * @var PageServiceInterface
     */
    private $pageService;

    /**
     * @return void
     */
    public function setMainController(ChameleonControllerInterface $mainController)
    {
        $this->mainController = $mainController;
    }

    public function showAction(Request $request, \Throwable $exception): Response
    {
        $code = null;
        if (method_exists($exception, 'getStatusCode')) {
            $code = $exception->getStatusCode();
        }
        if (null === $code) {
            $code = 500;
        }

        $exceptionPageDef = $this->getExceptionPageDef($code);
        if (null === $exceptionPageDef) {
            return $this->__invoke($exception);
        }

        $request->attributes->set('pagedef', $exceptionPageDef);

        // Do not execute (original) module_fnc for error pages
        $request->request->remove('module_fnc');
        $request->query->remove('module_fnc');

        return $this->mainController->__invoke();
    }

    /**
     * @return void
     */
    public function setExtranetConfiguration(ExtranetConfigurationInterface $extranetConfiguration)
    {
        $this->extranetConfiguration = $extranetConfiguration;
    }

    /**
     * @return void
     */
    public function setExtranetUserProvider(ExtranetUserProviderInterface $extranetUserProvider)
    {
        $this->extranetUserProvider = $extranetUserProvider;
    }

    /**
     * @return void
     */
    public function setPortalDomainService(PortalDomainServiceInterface $portalDomainService)
    {
        $this->portalDomainService = $portalDomainService;
    }

    /**
     * @return void
     */
    public function setRequestInfoService(RequestInfoServiceInterface $requestInfoService)
    {
        $this->requestInfoService = $requestInfoService;
    }

    /**
     * @param int $code
     *
     * @return string|null
     */
    private function getExceptionPageDef($code)
    {
        if (true === $this->requestInfoService->isBackendMode()) {
            return;
        }
        $portal = $this->portalDomainService->getActivePortal();
        if (null === $portal) {
            return null;
        }
        $nodeId = null;
        if (404 === $code) {
            $nodeId = $portal->fieldPageNotFoundNode;
        } elseif (403 === $code) {
            $nodeId = $this->getAccessDeniedPageNodeId();
        }

        if (null === $nodeId) {
            if (is_object($portal)) {
                $nodeId = $portal->GetSystemPageNodeId('error-'.$code);
            } else {
                return;
            }
        }

        if (null === $nodeId) {
            return;
        }

        $page = $this->pageService->getByTreeId($nodeId);
        if (null === $page) {
            return;
        }

        return $page->id;
    }

    /**
     * @return string|null
     */
    private function getAccessDeniedPageNodeId()
    {
        $user = $this->extranetUserProvider->getActiveUser();
        $extranetConfig = $this->extranetConfiguration->getExtranetConfigObject();

        if (null === $user || false === $user->IsLoggedIn()) {
            return $extranetConfig->fieldAccessRefusedNodeId;
        }

        return $extranetConfig->fieldGroupRightDeniedNodeId;
    }

    /**
     * @return void
     */
    public function setPageService(PageServiceInterface $pageService)
    {
        $this->pageService = $pageService;
    }
}
