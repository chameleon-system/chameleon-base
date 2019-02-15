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

use ChameleonSystem\CoreBundle\Service\BackendBreadcrumbService;
use ChameleonSystem\CoreBundle\Service\RequestInfoServiceInterface;
use ChameleonSystem\CoreBundle\Util\InputFilterUtilInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;

class BackendBreadcrumbListener implements ContainerAwareInterface
{
    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * @var RequestStack
     */
    private $requestStack;

    /**
     * @var RequestInfoServiceInterface
     */
    private $requestInfoService;

    /**
     * @var BackendBreadcrumbService
     */
    private $backendBreadcrumbService;

    /**
     * @var InputFilterUtilInterface
     */
    private $inputFilterUtil;

    public function __construct(
        ContainerInterface $container,
        RequestStack $requestStack,
        RequestInfoServiceInterface $requestInfoService,
        InputFilterUtilInterface $inputFilterUtil,
        BackendBreadcrumbService $backendBreadcrumbService
    )
    {
        $this->container = $container;
        $this->requestStack = $requestStack;
        $this->requestInfoService = $requestInfoService;
        $this->inputFilterUtil = $inputFilterUtil;
        $this->backendBreadcrumbService = $backendBreadcrumbService;
    }

    /**
     * @param GetResponseEvent $event
     */
    public function onKernelRequest(GetResponseEvent $event)
    {
        if (false === $event->isMasterRequest()) {
            return;
        }

        if (false === $this->requestInfoService->isBackendMode()) {
            return;
        }

        $this->handleBreadcrumbHistory();
    }

    private function handleBreadcrumbHistory()
    {
        $breadCrumb = $this->backendBreadcrumbService->getBreadcrumb();

        if (null === $breadCrumb) {
            return;
        }

        if ('true' === $this->inputFilterUtil->getFilteredInput('_rmhist')) {
            $breadCrumb->reset();

            return;
        }

        $historyItemId = $this->inputFilterUtil->getFilteredInput('_histid');

        if (null === $historyItemId) {
            $parameters = $this->getAllParametersFromRequest();
            unset($parameters['_rmhist']);
            $historyItemId = $breadCrumb->getSimilarHistoryElementIndex($parameters);
        }

        if (null !== $historyItemId) {
            $breadCrumb->Clear((int) $historyItemId);
        }
    }

    private function getAllParametersFromRequest(): array
    {
        $parameter = [];

        $request = $this->requestStack->getCurrentRequest();

        $keys = $request->query->keys();
        foreach ($keys as $key) {
            $parameter[$key] = $this->inputFilterUtil->getFilteredInput($key);
        }

        $keys = $request->request->keys();
        foreach ($keys as $key) {
            $parameter[$key] = $this->inputFilterUtil->getFilteredInput($key);
        }

        return $parameter;
    }

    /**
     * {@inheritdoc}
     */
    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }
}
