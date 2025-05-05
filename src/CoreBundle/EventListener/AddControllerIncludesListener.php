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

use ChameleonSystem\CoreBundle\Controller\ChameleonController;
use ChameleonSystem\CoreBundle\Event\HtmlIncludeEventInterface;
use ChameleonSystem\CoreBundle\Service\RequestInfoServiceInterface;

class AddControllerIncludesListener
{
    /**
     * @var RequestInfoServiceInterface
     */
    private $requestInfoService;
    /**
     * @var ChameleonController
     */
    private $backendController;
    /**
     * @var ChameleonController
     */
    private $frontendController;

    public function __construct(RequestInfoServiceInterface $requestInfoService, ChameleonController $backendController, ChameleonController $frontendController)
    {
        $this->requestInfoService = $requestInfoService;
        $this->backendController = $backendController;
        $this->frontendController = $frontendController;
    }

    /**
     * @return void
     */
    public function onGlobalHtmlHeaderInclude(HtmlIncludeEventInterface $event)
    {
        if ($this->requestInfoService->isBackendMode()) {
            $event->addData($this->backendController->getHtmlHeaderIncludes());
        } else {
            $event->addData($this->frontendController->getHtmlHeaderIncludes());
        }
    }

    /**
     * @return void
     */
    public function onGlobalHtmlFooterInclude(HtmlIncludeEventInterface $event)
    {
        if ($this->requestInfoService->isBackendMode()) {
            $event->addData($this->backendController->getHtmlFooterIncludes());
        } else {
            $event->addData($this->frontendController->getHtmlFooterIncludes());
        }
    }
}
