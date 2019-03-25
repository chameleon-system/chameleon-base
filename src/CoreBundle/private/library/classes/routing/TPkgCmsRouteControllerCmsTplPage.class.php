<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use ChameleonSystem\CoreBundle\Controller\ChameleonControllerInterface;
use ChameleonSystem\CoreBundle\Service\PageServiceInterface;
use ChameleonSystem\CoreBundle\Service\TreeNodeServiceInterface;
use ChameleonSystem\CoreBundle\Service\TreeServiceInterface;
use esono\pkgCmsRouting\AbstractRouteController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class TPkgCmsRouteControllerCmsTplPage extends AbstractRouteController
{
    /**
     * @var ChameleonControllerInterface
     */
    private $controller;

    /**
     * @var TreeNodeServiceInterface
     */
    private $treeNodeService;

    /**
     * @var TreeServiceInterface
     */
    private $treeService;
    /**
     * @var ICmsCoreRedirect
     */
    private $redirect;
    /**
     * @var RequestStack
     */
    private $requestStack;
    /**
     * @var PageServiceInterface
     */
    private $pageService;

    /**
     * @param Request $request
     * @param string  $pagePath
     *
     * @return Response
     *
     * @throws BadMethodCallException
     * @throws NotFoundHttpException
     */
    public function getPage(Request $request, $pagePath)
    {
        if (null === $this->controller) {
            throw new BadMethodCallException('No main controller has been set before calling getPage()');
        }

        $pagedef = $this->getPagedef($request, $pagePath);

        $request->query->set('pagedef', $pagedef);
        $request->attributes->set('pagedef', $pagedef);

        return call_user_func($this->controller);
    }

    /**
     * @param Request $request
     * @param string  $pagePath
     *
     * @return string|null
     *
     * @throws NotFoundHttpException
     */
    private function getPagedef(Request $request, $pagePath)
    {
        $pagedef = $this->getPagedefForPath($pagePath);

        if (null === $pagedef) {
            $pagedef = TCMSSmartURL::run($request);
        } else {
            if (in_array($request->getMethod(), array('GET', 'HEAD'), true)) {
                $canonicalUrl = $this->getCanonicalUrl($pagedef);
                if ($request->getPathInfo() !== $canonicalUrl) {
                    $this->redirectToCanonicalUrl($request, $canonicalUrl);
                }
            }
        }

        return $pagedef;
    }

    /**
     * @param string $pagePath
     *
     * @return string|null
     *
     * @throws NotFoundHttpException
     */
    private function getPagedefForPath($pagePath)
    {
        $activePortal = $this->portalDomainService->getActivePortal();
        if (null === $activePortal) {
            throw new NotFoundHttpException('No active portal is available.');
        }
        $activeLanguage = $this->languageService->getActiveLanguage();
        if (null === $activeLanguage) {
            throw new NotFoundHttpException('No active language is available.');
        }

        $normalizedPagePath = $this->routingUtil->normalizeRoutePath($pagePath, $activePortal);
        $lowerCasedPagePath = mb_strtolower($normalizedPagePath);

        $routes = $this->routingUtil->getAllPageRoutes($activePortal, $activeLanguage);
        foreach ($routes as $pagedef => $routesToPage) {
            foreach ($routesToPage->getPathList() as $comparePath) {
                $lowerCasedComparePath = mb_strtolower($comparePath);
                if ($lowerCasedComparePath === $lowerCasedPagePath) {
                    return $pagedef;
                }
            }
        }

        return null;
    }

    /**
     * @param string $pagedef
     *
     * @return string
     */
    private function getCanonicalUrl($pagedef)
    {
        return $this->urlUtil->getRelativeUrl($this->pageService->getLinkToPageRelative($pagedef));
    }

    /**
     * @param Request $request
     * @param string  $canonicalUrl
     */
    private function redirectToCanonicalUrl(Request $request, $canonicalUrl)
    {
        $pathInfo = $request->getPathInfo();
        $requestUri = $request->getRequestUri();

        $redirectUrl = str_replace($pathInfo, $canonicalUrl, $requestUri);

        $this->redirect->redirect($redirectUrl, Response::HTTP_MOVED_PERMANENTLY);
    }

    /**
     * @param ChameleonControllerInterface $controller
     */
    public function setMainController(ChameleonControllerInterface $controller)
    {
        $this->controller = $controller;
    }

    /**
     * @param TreeNodeServiceInterface $treeNodeService
     */
    public function setTreeNodeService($treeNodeService)
    {
        $this->treeNodeService = $treeNodeService;
    }

    /**
     * @param TreeServiceInterface $treeService
     */
    public function setTreeService($treeService)
    {
        $this->treeService = $treeService;
    }

    /**
     * @param ICmsCoreRedirect $redirect
     */
    public function setRedirect(ICmsCoreRedirect $redirect)
    {
        $this->redirect = $redirect;
    }

    /**
     * @param RequestStack $requestStack
     */
    public function setRequestStack(RequestStack $requestStack)
    {
        $this->requestStack = $requestStack;
    }

    /**
     * @param PageServiceInterface $pageService
     */
    public function setPageService($pageService)
    {
        $this->pageService = $pageService;
    }
}
