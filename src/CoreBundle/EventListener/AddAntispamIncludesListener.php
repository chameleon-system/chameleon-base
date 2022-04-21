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

class AddAntispamIncludesListener
{
    /**
     * @var RequestInfoServiceInterface
     */
    private $requestInfoService;

    /**
     * @param RequestInfoServiceInterface $requestInfoService
     */
    public function __construct(RequestInfoServiceInterface $requestInfoService)
    {
        $this->requestInfoService = $requestInfoService;
    }

    /**
     * @param HtmlIncludeEventInterface $event
     *
     * @return void
     */
    public function onGlobalHtmlFooterInclude(HtmlIncludeEventInterface $event)
    {
        $includes = array();

        if (!$this->requestInfoService->isCmsTemplateEngineEditMode()) {
            $oAntiSpam = new \antiSpam();
            $includes[] = $oAntiSpam->PrintJSCode();
        }
        $event->addData($includes);
    }
}
