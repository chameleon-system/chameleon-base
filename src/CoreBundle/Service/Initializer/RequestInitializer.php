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

use ChameleonSystem\CoreBundle\Service\RequestInfoServiceInterface;
use ChameleonSystem\CoreBundle\Session\ChameleonSessionManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use TGlobal;
use TTools;

/**
 * Class RequestInitializer.
 */
class RequestInitializer
{
    /**
     * @var ChameleonSessionManagerInterface
     */
    private $sessionManager;
    /**
     * @var RequestInfoServiceInterface $requestInfoService
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
     * @param Request $request
     */
    public function initialize(Request $request)
    {
        // removed check for request type here. If we need it for something else here besides the setting via chameleon::boot, look here again
        $this->defineVersion();
        $this->registerErrorHandler();

        $this->addStaticURLs();
        $this->sessionManager->boot();
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
        $aStaticURLMapping = array();
        $iCount = 0;
        foreach ($aStaticURLs as $sStaticURL) {
            $aStaticURLMapping['CMSSTATICURL_'.$iCount] = trim($sStaticURL);
            ++$iCount;
        }
        TTools::AddStaticPageVariables($aStaticURLMapping);
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
