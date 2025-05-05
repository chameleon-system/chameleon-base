<?php

namespace ChameleonSystem\CoreBundle\EventListener;

use ChameleonSystem\CoreBundle\Event\ChangeActivePageEvent;
use ChameleonSystem\CoreBundle\Service\RequestInfoServiceInterface;
use ChameleonSystem\CoreBundle\Util\UrlUtil;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;

class RedirectToSecurePageListener
{
    /**
     * @var RequestStack
     */
    private $requestStack;
    /**
     * @var UrlUtil
     */
    private $urlUtil;
    /**
     * @var \ICmsCoreRedirect
     */
    private $redirect;
    /**
     * @var RequestInfoServiceInterface
     */
    private $requestInfoService;

    public function __construct(
        RequestStack $requestStack,
        UrlUtil $urlUtil,
        \ICmsCoreRedirect $redirect,
        RequestInfoServiceInterface $requestInfoService
    ) {
        $this->requestStack = $requestStack;
        $this->urlUtil = $urlUtil;
        $this->redirect = $redirect;
        $this->requestInfoService = $requestInfoService;
    }

    public function onChangeActivePage(ChangeActivePageEvent $event): void
    {
        $request = $this->requestStack->getCurrentRequest();

        if (null === $request) {
            throw new \RuntimeException('No request present during page change');
        }

        if ($this->needsRedirectToSecurePage($request, $event->getNewActivePage())) {
            $url = $this->urlUtil->getModifiedUrlFromRequest($request, 'https', ['pagedef']);

            $this->redirect->redirect($url, Response::HTTP_MOVED_PERMANENTLY);
        }
    }

    private function needsRedirectToSecurePage(Request $request, \TCMSActivePage $activePage): bool
    {
        if (true === $request->isSecure()) {
            return false;
        }
        if (false === $activePage->fieldUsessl) {
            return false;
        }
        if (true === $this->requestInfoService->isCmsTemplateEngineEditMode()) {
            return false;
        }

        return true;
    }
}
