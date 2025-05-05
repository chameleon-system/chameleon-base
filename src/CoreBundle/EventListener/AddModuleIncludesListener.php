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

use ChameleonSystem\CoreBundle\Event\HtmlIncludeEventInterface;
use ChameleonSystem\CoreBundle\Service\RequestInfoServiceInterface;

class AddModuleIncludesListener
{
    /**
     * @var RequestInfoServiceInterface
     */
    private $requestInfoService;
    /**
     * @var \TModuleLoader
     */
    private $moduleLoader;
    /**
     * @var \TUserModuleLoader
     */
    private $userModuleLoader;

    public function __construct(RequestInfoServiceInterface $requestInfoService, \TModuleLoader $moduleLoader, \TUserModuleLoader $userModuleLoader)
    {
        $this->requestInfoService = $requestInfoService;
        $this->moduleLoader = $moduleLoader;
        $this->userModuleLoader = $userModuleLoader;
    }

    /**
     * @return void
     */
    public function onGlobalHtmlHeaderInclude(HtmlIncludeEventInterface $event)
    {
        if ($this->requestInfoService->isBackendMode()) {
            $event->addData($this->moduleLoader->GetHtmlHeadIncludes());
        } else {
            $event->addData($this->userModuleLoader->GetHtmlHeadIncludes());
        }
    }

    /**
     * @return void
     */
    public function onGlobalHtmlFooterInclude(HtmlIncludeEventInterface $event)
    {
        if ($this->requestInfoService->isBackendMode()) {
            $event->addData($this->moduleLoader->GetHtmlFooterIncludes());
        } else {
            $event->addData($this->userModuleLoader->GetHtmlFooterIncludes());
        }
    }
}
