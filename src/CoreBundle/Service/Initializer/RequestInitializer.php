<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ChameleonSystem\CoreBundle\Service\Initializer;

use ChameleonSystem\CoreBundle\Response\ResponseVariableReplacerInterface;
use ChameleonSystem\CoreBundle\Session\ChameleonSessionManagerInterface;
use Symfony\Component\HttpFoundation\Request;

class RequestInitializer
{
    /**
     * @var ChameleonSessionManagerInterface
     */
    private $sessionManager;
    /**
     * @var ResponseVariableReplacerInterface
     */
    private $responseVariableReplacer;

    public function __construct(ResponseVariableReplacerInterface $responseVariableReplacer)
    {
        $this->responseVariableReplacer = $responseVariableReplacer;
    }

    /**
     * @return void
     */
    public function initialize(Request $request)
    {
        // removed check for request type here. If we need it for something else here besides the setting via chameleon::boot, look here again
        $this->registerErrorHandler();

        $this->addStaticURLs();
        $this->addSchemeVariable($request);
        $this->sessionManager->boot();
        $this->transformParameters($request);
    }

    /**
     * @psalm-suppress InvalidArgument TCMSErrorHandler::ShutdownHandler exists
     *
     * @return void
     */
    protected function registerErrorHandler()
    {
        if (!_DEVELOPMENT_MODE && USE_DEFAULT_ERROR_HANDLER) {
            register_shutdown_function(['TCMSErrorHandler', 'ShutdownHandler']);
        }
    }

    /**
     * @param ChameleonSessionManagerInterface $sessionManager
     *
     * @return void
     */
    public function setSessionManager($sessionManager)
    {
        $this->sessionManager = $sessionManager;
    }

    /**
     * @return void
     */
    protected function addStaticURLs()
    {
        $aStaticURLs = \TGlobal::GetStaticURLPrefix();
        if (!is_array($aStaticURLs)) {
            $aStaticURLs = [$aStaticURLs];
        }
        $iCount = 0;
        foreach ($aStaticURLs as $sStaticURL) {
            $this->responseVariableReplacer->addVariable('CMSSTATICURL_'.$iCount, trim($sStaticURL));
            ++$iCount;
        }
    }

    private function addSchemeVariable(Request $request): void
    {
        $this->responseVariableReplacer->addVariable('CMS-PROTOCOL', $request->getScheme());
    }

    /**
     * @return void
     */
    protected function transformParameters(Request $request)
    {
        if ($request->query->count() > 0) {
            $oParameterMapper = new \TCMSParameterMapper();
            $getParameter = $oParameterMapper->transformParameter($request->query->all());
            $request->query->replace($getParameter);
        }
    }
}
