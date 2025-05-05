<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use ChameleonSystem\CoreBundle\Response\ResponseVariableReplacerInterface;
use ChameleonSystem\CoreBundle\Service\ActivePageServiceInterface;
use ChameleonSystem\CoreBundle\Util\UrlUtil;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class cmsCoreRedirect implements ICmsCoreRedirect
{
    /**
     * @var RequestStack
     */
    private $requestStack;
    /**
     * @var ChameleonRedirectStrategyInterface
     */
    private $redirectStrategy;
    /**
     * @var ContainerInterface
     */
    private $container;
    /**
     * @var UrlUtil
     */
    private $urlUtil;
    /**
     * @var ResponseVariableReplacerInterface
     */
    private $responseVariableReplacer;

    public function __construct(RequestStack $requestStack, ChameleonRedirectStrategyInterface $redirectStrategy, UrlUtil $urlUtil, ContainerInterface $container, ResponseVariableReplacerInterface $responseVariableReplacer)
    {
        $this->requestStack = $requestStack;
        $this->redirectStrategy = $redirectStrategy;
        $this->urlUtil = $urlUtil;
        $this->container = $container; // avoid circular references
        $this->responseVariableReplacer = $responseVariableReplacer;
    }

    /**
     * {@inheritdoc}
     */
    public function redirect($url, $status = 302, $allowOnlyInternalUrls = false)
    {
        if (true === $this->isInternalURL($url)) {
            $url = $this->responseVariableReplacer->replaceVariables($url);
        } elseif ($allowOnlyInternalUrls) {
            throw new NotFoundHttpException('Only internal URLs allowed here, but got external URL: '.$url);
        }

        $this->redirectStrategy->redirect($url, $status);
    }

    /**
     * {@inheritdoc}
     */
    public function isInternalURL($url)
    {
        if (false === $this->urlUtil->isUrlAbsolute($url)) {
            return true;
        }

        $request = $this->getRequest();
        // remove protocol to compare
        $urlData = parse_url($url);
        if (isset($urlData['host']) && $urlData['host'] === $request->getHost()) {
            return true;
        }

        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function redirectToActivePage($queryStringParameters = null)
    {
        if (is_string($queryStringParameters)) {
            $parameters = $this->urlUtil->getUrlParametersAsArray($queryStringParameters);
        } elseif (is_array($queryStringParameters)) {
            $parameters = $queryStringParameters;
        } else {
            $parameters = [];
        }

        $excludeParameters = $this->getRequest()->query->keys();
        $this->redirect($this->getActivePageService()->getLinkToActivePageRelative($parameters, $excludeParameters));
    }

    /**
     * @return void
     */
    public function onKernelException(ExceptionEvent $event)
    {
        $exception = $event->getThrowable();
        if (!($exception instanceof ChameleonRedirectException)) {
            return;
        }

        $redirectResponse = new Symfony\Component\HttpFoundation\RedirectResponse($exception->getUrl());
        $redirectResponse->setStatusCode($exception->getStatus());
        $event->setResponse($redirectResponse);
    }

    /**
     * @return Request|null
     */
    protected function getRequest()
    {
        return $this->requestStack->getCurrentRequest();
    }

    /**
     * @return ActivePageServiceInterface
     */
    protected function getActivePageService()
    {
        return $this->container->get('chameleon_system_core.active_page_service');
    }
}
