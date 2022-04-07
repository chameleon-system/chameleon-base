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
use TGlobal;

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
     * @param Request $request
     */
    public function initialize(Request $request)
    {
        // removed check for request type here. If we need it for something else here besides the setting via chameleon::boot, look here again
        $this->defineVersion();
        $this->registerErrorHandler();

        $this->addStaticURLs();
        $this->addSchemeVariable($request);
        $this->sessionManager->boot();
        $this->transformParameters($request);
    }

    protected function defineVersion()
    {
        require_once PATH_CORE_CONFIG.'/version.inc.php';
    }

    /**
     * @psalm-suppress InvalidArgument TCMSErrorHandler::ShutdownHandler exists
     * @return void
     */
    protected function registerErrorHandler()
    {
        if (!_DEVELOPMENT_MODE && USE_DEFAULT_ERROR_HANDLER) {
            register_shutdown_function(array('TCMSErrorHandler', 'ShutdownHandler'));
        }
    }

    /**
     * @param ChameleonSessionManagerInterface $sessionManager
     */
    public function setSessionManager($sessionManager)
    {
        $this->sessionManager = $sessionManager;
    }

    protected function addStaticURLs()
    {
        $aStaticURLs = TGlobal::GetStaticURLPrefix();
        if (!is_array($aStaticURLs)) {
            $aStaticURLs = array($aStaticURLs);
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
     * @param Request $request
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
