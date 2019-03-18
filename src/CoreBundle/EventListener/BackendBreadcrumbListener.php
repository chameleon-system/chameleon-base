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

use ChameleonSystem\CoreBundle\Service\BackendBreadcrumbServiceInterface;
use ChameleonSystem\CoreBundle\Service\RequestInfoServiceInterface;
use ChameleonSystem\CoreBundle\Util\InputFilterUtilInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;

class BackendBreadcrumbListener
{
    /**
     * @var RequestStack
     */
    private $requestStack;

    /**
     * @var RequestInfoServiceInterface
     */
    private $requestInfoService;

    /**
     * @var BackendBreadcrumbServiceInterface
     */
    private $backendBreadcrumbService;

    /**
     * @var InputFilterUtilInterface
     */
    private $inputFilterUtil;

    public function __construct(
        RequestStack $requestStack,
        RequestInfoServiceInterface $requestInfoService,
        InputFilterUtilInterface $inputFilterUtil,
        BackendBreadcrumbServiceInterface $backendBreadcrumbService
    ) {
        $this->requestStack = $requestStack;
        $this->requestInfoService = $requestInfoService;
        $this->inputFilterUtil = $inputFilterUtil;
        $this->backendBreadcrumbService = $backendBreadcrumbService;
    }

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

    private function handleBreadcrumbHistory(): void
    {
        $breadCrumb = $this->backendBreadcrumbService->getBreadcrumb();

        if (null === $breadCrumb) {
            return;
        }

        if ('true' === $this->inputFilterUtil->getFilteredGetInput('_rmhist')) {
            $breadCrumb->reset();

            return;
        }

        $historyItemId = $this->inputFilterUtil->getFilteredGetInput('_histid');

        if (null === $historyItemId) {
            $parameters = $this->getAllParametersFromGetRequest();
            unset($parameters['_rmhist']);
            $historyItemId = $breadCrumb->getSimilarHistoryElementIndex($parameters);
        }

        if (null !== $historyItemId) {
            $breadCrumb->Clear((int) $historyItemId);
        }
    }

    private function getAllParametersFromGetRequest(): array
    {
        $parameters = [];

        $request = $this->requestStack->getCurrentRequest();

        $keys = $request->query->keys();
        foreach ($keys as $key) {
            $parameters[$key] = $this->inputFilterUtil->getFilteredInput($key);
        }

        return $parameters;
    }
}
