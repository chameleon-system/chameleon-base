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
use Symfony\Component\Debug\Exception\FlattenException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Log\DebugLoggerInterface;

/**
 * {@inheritdoc}
 */
class ExceptionController extends \Symfony\Bundle\TwigBundle\Controller\ExceptionController
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
     * @param ChameleonControllerInterface $mainController
     */
    public function setMainController(ChameleonControllerInterface $mainController)
    {
        $this->mainController = $mainController;
    }

    /**
     * @param Request                   $request
     * @param FlattenException          $exception
     * @param DebugLoggerInterface|null $logger
     *
     * @return Response
     */
    public function showAction(Request $request, FlattenException $exception, DebugLoggerInterface $logger = null)
    {
        $currentContent = $this->getAndCleanOutputBuffering($request->headers->get('X-Php-Ob-Level', -1));

        $code = $exception->getStatusCode();

        $request->attributes->set('currentContent', $currentContent);

        $exceptionPageDef = $this->getExceptionPageDef($code);
        if (null === $exceptionPageDef) {
            return parent::showAction($request, $exception, $logger);
        }
        $request->attributes->set('pagedef', $exceptionPageDef);

        // Do not execute (original) module_fnc for error pages
        $request->request->remove('module_fnc');
        $request->query->remove('module_fnc');

        return $this->mainController->__invoke();
    }

    /**
     * @param ExtranetConfigurationInterface $extranetConfiguration
     */
    public function setExtranetConfiguration(ExtranetConfigurationInterface $extranetConfiguration)
    {
        $this->extranetConfiguration = $extranetConfiguration;
    }

    /**
     * @param ExtranetUserProviderInterface $extranetUserProvider
     */
    public function setExtranetUserProvider(ExtranetUserProviderInterface $extranetUserProvider)
    {
        $this->extranetUserProvider = $extranetUserProvider;
    }

    /**
     * @param Request $request
     * @param string  $format
     * @param int     $code
     * @param bool    $debug
     *
     * @return string
     */
    protected function findTemplate(Request $request, $format, $code, $debug)
    {
        if ($debug) {
            return 'exception-debug.html.twig';
        }

        return 'exception.html.twig';
    }

    /**
     * @param PortalDomainServiceInterface $portalDomainService
     */
    public function setPortalDomainService(PortalDomainServiceInterface $portalDomainService)
    {
        $this->portalDomainService = $portalDomainService;
    }

    /**
     * @param RequestInfoServiceInterface $requestInfoService
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
        $nodeId = null;
        if (404 === $code) {
            $nodeId = $portal->fieldPageNotFoundNode;
        } elseif (403 === $code) {
            $nodeId = $this->getAccessDeniedPageNodeId();
        }

        if (null === $nodeId) {
            if ($this->debug) {
                return;
            }

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
     * @param PageServiceInterface $pageService
     */
    public function setPageService(PageServiceInterface $pageService)
    {
        $this->pageService = $pageService;
    }
}
