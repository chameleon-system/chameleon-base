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
use Symfony\Component\HttpFoundation\Request;
use TGlobal;

class RequestInitializer
{
    /**
     * @var ResponseVariableReplacerInterface
     */
    private $responseVariableReplacer;

    public function __construct(ResponseVariableReplacerInterface $responseVariableReplacer)
    {
        $this->responseVariableReplacer = $responseVariableReplacer;
    }

    public function initialize(Request $request)
    {
        // removed check for request type here. If we need it for something else here besides the setting via chameleon::boot, look here again
        $this->defineVersion();
        $this->registerErrorHandler();

        $this->addStaticURLs();
        $this->addSchemeVariable($request);
        $this->transformParameters($request);
    }

    /**
     * @param Request $request
     *
     * @deprecated since 6.1.6 - not used anymore.
     */
    protected function handleUnitTestCase(Request $request)
    {
    }

    protected function defineVersion()
    {
        require_once PATH_CORE_CONFIG.'/version.inc.php';
    }

    protected function registerErrorHandler()
    {
        if (!_DEVELOPMENT_MODE && USE_DEFAULT_ERROR_HANDLER) {
            register_shutdown_function(array('TCMSErrorHandler', 'ShutdownHandler'));
        }
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
