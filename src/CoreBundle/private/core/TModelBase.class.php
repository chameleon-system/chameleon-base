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
use ChameleonSystem\CoreBundle\Exception\ModuleException;
use ChameleonSystem\CoreBundle\RequestType\RequestTypeInterface;
use ChameleonSystem\CoreBundle\Response\ResponseVariableReplacerInterface;
use ChameleonSystem\CoreBundle\Security\AuthenticityToken\TokenInjectionFailedException;
use ChameleonSystem\CoreBundle\Service\RequestInfoServiceInterface;
use ChameleonSystem\CoreBundle\ServiceLocator;
use esono\pkgCmsCache\CacheInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class TModelBase
{
    /**
     * the view template path to load (without .view.php ending).
     *
     * @var string
     */
    public $viewTemplate = null;

    /**
     * array of module configuration data from pagedef.
     *
     * @var array
     */
    public $aModuleConfig = array();

    /**
     * name of the spot e.g. spota.
     *
     * @var string
     */
    public $sModuleSpotName = '';

    /**
     * @var bool
     */
    public $hasError = false;

    /**
     * the data that will be available to module template views.
     *
     * @var array
     */
    public $data = array();

    /**
     * pointer to the controller.
     *
     * @var ChameleonControllerInterface
     *
     * @deprecated Don't use this controller. Retrieve it through \ChameleonSystem\CoreBundle\ServiceLocator::get('chameleon_system_core.chameleon_controller') instead
     */
    protected $controller = null;

    /**
     * @var $bool
     */
    private $allowedMethodCallsInitialized = false;

    /**
     * An array of all methods from the class which may be called via http requests.
     *
     * @var array
     */
    public $methodCallAllowed = array();

    /**
     * this is set automatically when the class is restored from session.
     *
     * @var bool
     */
    private $bIsWakingUp = false;

    /**
     * if this is true (and config enables automatically wrapping of div container for spots)
     * the module loader will generate a div with id="spotname" and class="moduleclass moduleview"
     * by default its false - you can overwrite this explicit for each module you need.
     *
     * @var bool
     */
    protected $bAllowHTMLDivWrapping = false;

    /**
     * this array is filled in TModuleLoader if the module was loaded from cache
     * if it is null, you should call $this->_GetCacheTableInfos().
     *
     * @var array - null by default
     */
    public $aCacheTrigger = null;

    /**
     * @param ChameleonControllerInterface $controller
     *
     * @deprecated Don't use this controller. Retrieve it through \ChameleonSystem\CoreBundle\ServiceLocator::get('chameleon_system_core.chameleon_controller') instead
     */
    public function setController($controller)
    {
        $this->controller = $controller;
    }

    /**
     * returns true if the class is currently waking up from the session.
     *
     * @return bool
     */
    protected function ClassIsWakingUpFromSession()
    {
        return $this->bIsWakingUp;
    }

    public function __wakeup()
    {
        $this->bIsWakingUp = true;
        $this->Init();
        $this->bIsWakingUp = false;
    }

    public function __sleep()
    {
        return array('viewTemplate', 'aModuleConfig', 'sModuleSpotName');
    }

    public function __construct()
    {
    }

    /**
     * @param string $name
     *
     * @return object
     */
    public function __get($name)
    {
        if ('global' === $name) {
            @trigger_error('The property TModelBase::$global is deprecated.', E_USER_DEPRECATED);

            return ServiceLocator::get('chameleon_system_core.global');
        }

        $trace = debug_backtrace();
        trigger_error(sprintf('Undefined property via __get(): %s in %s on line %s',
                $name,
                $trace[0]['file'],
                $trace[0]['line']),
            E_USER_NOTICE);

        return null;
    }

    /**
     * @param string $name
     * @param mixed  $value
     */
    public function __set($name, $value)
    {
        if ('global' !== $name) {
            throw new \LogicException('Invalid property: '.$name);
        }

        $this->global = $value;
    }

    public function __isset($name)
    {
        return 'global' === $name;
    }

    /**
     * Called before any external functions get called, but after the constructor.
     * @return void
     */
    public function Init()
    {
    }

    /**
     * this function should fill the data array and return a pointer to it
     * (pointer because it may contain objects).
     *
     * @deprecated - use a mapper instead (see getMapper)
     *
     * @return array<string, mixed>
     */
    public function &Execute()
    {
        $this->data['sModuleSpotName'] = $this->sModuleSpotName;
        $this->data['_oModules'] = &$this->getController()->moduleLoader;
        $pagedef = $this->global->GetUserData('pagedef');
        $this->data['pagedef'] = $pagedef;
        $this->data['_pagedefType'] = 'Core';
        if ($this->global->UserDataExists('_pagedefType')) {
            $this->data['_pagedefType'] = $this->global->GetUserData('_pagedefType');
        }

        return $this->data;
    }

    /**
     * return true if the method is white-listed for access without Authenticity token. Note: you will still need
     * to define the permitted methods via DefineInterface.
     *
     * @param string $sMethodName
     *
     * @return bool
     */
    public function AllowAccessWithoutAuthenticityToken($sMethodName)
    {
        return 'ExecuteAjaxCall' === $sMethodName;
    }

    /**
     * @return void
     */
    protected function DefineInterface()
    {
        $externalFunctions = ['ExecuteAjaxCall'];
        $this->methodCallAllowed = array_merge($this->methodCallAllowed, $externalFunctions);
        $this->allowedMethodCallsInitialized = true;
    }

    /**
     * Run a method within the module, but as an ajax call (no module will be used
     * and the function will output jason encoded data). The method assumes that
     * the name of the function that you want to execute is in the parameter _fnc.
     * Also note that the function being called needs to be included in $this->methodCallAllowed
     * You can control how the data will be encoded using the sOutputMode.
     */
    public function ExecuteAjaxCall()
    {
        $methodName = $this->global->GetUserData('_fnc');
        if (empty($methodName)) {
            if (_DEVELOPMENT_MODE) {
                trigger_error('Ajax call made, but no function passed via _fnc', E_USER_WARNING);
            }
            header('HTTP/1.0 404 Not Found');
            exit();
        } else {
            if (false === $this->AllowAccessWithoutAuthenticityToken($methodName)
                && false === $this->getAuthenticityTokenManager()->isTokenValid()) {
                return;
            }
            // call the _fnc method in the current module
            $functionResult = &$this->_CallMethod($methodName);

            $sOutputMode = 'Ajax';
            $aPermittedOutputModes = array('Ajax', 'Plain');
            if ($this->global->UserDataExists('sOutputMode') && in_array($this->global->GetUserData('sOutputMode'), $aPermittedOutputModes)) {
                $sOutputMode = $this->global->GetUserData('sOutputMode');
            }
            switch ($sOutputMode) {
                case 'Plain':
                    $this->_OutputForAjaxPlain($functionResult);
                    break;
                case 'Ajax':
                default:
                    $this->_OutputForAjax($functionResult);
                    break;
            }
        }
    }

    /**
     * returns an array holding the required style, js, and other info for the
     * module that needs to be loaded in the document head. each include should
     * be one element of the array, and should be formated exactly as it would
     * by another module that requires the same data (so it is not loaded twice).
     * the function will be called for every module on the page AUTOMATICALLY by
     * the controller (the controller will replace the tag "<!--#CMSHEADERCODE#-->" with
     * the results).
     *
     * @return string[]
     */
    public function GetHtmlHeadIncludes()
    {
        return array();
    }

    /**
     * returns an array holding the required js, html snippets, and other info for the
     * module that needs to be loaded in the document footer (before the ending </body> Tag).
     * Each include should be one element of the array, and should be formated exactly as it
     * would by another module that requires the same data (so it is not loaded twice).
     * the function will be called for every module on the page AUTOMATICALLY by
     * the controller (the controller will replace the tag "<!--#CMSFOOTERCODE#-->" with
     * the results).
     *
     * @return string[]
     */
    public function GetHtmlFooterIncludes()
    {
        return array();
    }

    /**
     * sends module output as plaintext
     * it`s possible to set a callback function via GET/POST 'callback' as wrapper.
     *
     * @param string $content
     * @param bool   $bPreventPreOutputInjection - disable the pre output variable injection (messages, vars, authenticity token...)
     */
    protected function _OutputForAjaxPlain(&$content, $bPreventPreOutputInjection = false)
    {
        if (!$bPreventPreOutputInjection) {
            try {
                $content = $this->getResponseVariableReplacer()->replaceVariables($content);
            } catch (TokenInjectionFailedException $exception) {
                $this->getLogger()->error(
                    sprintf('Cannot render AJAX output plain %s', $exception->getMessage()),
                    ['exception' => $exception]
                );

                http_response_code(500);

                exit;
            }
        }

        $this->outputForAjaxAndExit($content, 'text/plain');
    }

    /**
     * converts the contents of $parameter to a form that can be read by the javascript
     * on the client side. it will clear all previously generated content, send the ajax header,
     * the encoded data, and terminate(!) no further processing will take place.
     *
     * @param object|array|string $content
     */
    protected function _OutputForAjax(&$content)
    {
        try {
            $content = $this->getResponseVariableReplacer()->replaceVariables($content);
        } catch (TokenInjectionFailedException $exception) {
            $this->getLogger()->error(
                sprintf('Cannot render AJAX output %s', $exception->getMessage()),
                ['exception' => $exception]
            );

            http_response_code(500);

            exit;
        }
        $jsonContent = \json_encode($content);

        $this->outputForAjaxAndExit($jsonContent, 'application/json');
    }

    /**
     * @param object|array|string $content
     * @param string              $contentType
     */
    private function outputForAjaxAndExit($content, string $contentType): void
    {
        // now clear the output. notice that we need the @ since the function throws a notice once the buffer is cleared
        $this->SetHTMLDivWrappingStatus(false);
        while (@ob_end_clean()) {
        }
        header(sprintf('Content-Type: %s', $contentType));
        //never index ajax responses
        header('X-Robots-Tag: noindex, nofollow', true);

        // allow using a JS callback function
        if ($this->global->UserDataExists('callback')) {
            $sParam = $this->global->GetUserData('callback');
            if (!empty($sParam)) {
                trigger_error('callback parameter no longer supported', E_USER_NOTICE);
            }
        }

        echo $content;

        exit;
    }

    /**
     * call a method of this module. validates request.
     *
     * @param string $sMethodName      - name of the function
     * @param mixed[] $aMethodParameter - parameters to pass to the method
     *
     * @return mixed
     */
    public function &_CallMethod($sMethodName, $aMethodParameter = array())
    {
        if (true === $this->isMethodCallAllowed($sMethodName)) {
            $functionResult = call_user_func_array(array($this, $sMethodName), $aMethodParameter);

            return $functionResult;
        }

        if (_DEVELOPMENT_MODE) {
            trigger_error('Ajax call made ['.TGlobal::OutHTML($sMethodName).'] in ['.get_class($this).'], but either that function may not be called or it does not exist', E_USER_ERROR);
        } else {
            trigger_error('Ajax call made ['.TGlobal::OutHTML($sMethodName).'] in ['.get_class($this).'], but either that function may not be called or it does not exist', E_USER_WARNING);
            header('HTTP/1.0 404 Not Found');
            exit();
        }
    }

    /**
     * @param string $methodName
     *
     * @return bool
     */
    private function isMethodCallAllowed($methodName)
    {
        if (false === $this->allowedMethodCallsInitialized) {
            $this->DefineInterface();
        }

        return in_array($methodName, $this->methodCallAllowed, true) && method_exists($this, $methodName);
    }

    /**
     * set the view template of the model.
     *
     * @param string $modelName - directory name of the module
     * @param string $name      - template filename in directory "views" without ".view.php"
     */
    protected function SetTemplate($modelName, $name)
    {
        $sType = $this->aModuleConfig['moduleType'] ?? 'Core';

        $this->aModuleConfig['model'] = $modelName;
        $this->aModuleConfig['view'] = $name;

        $rootPath = $this->_GetModuleRootPath($sType);

        $viewPathManager = ServiceLocator::get('chameleon_system_core.viewPathManager'); /** @var $viewPathManager IViewPathManager* */
        $sTemplatePath = $viewPathManager->getWebModuleViewPath($name, $modelName, $sType, $rootPath);
        $this->viewTemplate = $sTemplatePath;
    }

    public function _GetModuleRootPath($sType)
    {
        $rootPath = PATH_MODULES;
        switch ($sType) {
            case 'Core':
                $rootPath = PATH_MODULES;
                break;
            case 'Custom-Core':
                $rootPath = PATH_MODULES_CUSTOM;
                break;
            case 'Customer':
                $rootPath = PATH_MODULES_CUSTOMER;
                break;
            default:
                break;
        }

        return $rootPath;
    }

    /**
     * returns an array of variables that should be replaced in the rendered module. Use this method to inject not-cachable data into
     * the complete module html.
     *
     * The Variables can be used in the HTML of the Module via [{NameOfVariable}]. This also works for view of other objects used by the module.
     *
     * @return array
     */
    public function GetPostRenderVariables()
    {
        return array(
            'sModuleSpotName' => TGlobal::OutHTML($this->sModuleSpotName),
        );
    }

    /**
     * if this function returns true, then the result of the execute function will be cached.
     *
     * @return bool
     */
    public function _AllowCache()
    {
        return false;
    }

    /**
     * return an assoc array of parameters that describe the state of the module.
     *
     * @return array
     */
    public function _GetCacheParameters()
    {
        $parameters = $this->aModuleConfig;
        $parameters['_class'] = __CLASS__;

        return $parameters;
    }

    /**
     * if the content that is to be cached comes from the database (as is most often the case)
     * then this function should return an array of assoc arrays that point to the
     * tables and records that are associated with the content. one table entry has
     * two fields:
     *   - table - the name of the table
     *   - id    - the record in question. if this is empty, then any record change in that
     *             table will result in a cache clear.
     *
     * @deprecated - use a mapper instead (see getMapper)
     *
     * @return array
     */
    public function _GetCacheTableInfos()
    {
        return array();
    }

    /**
     * return true if the spot has messages.
     *
     * @return bool
     */
    protected function SpotHasMessages()
    {
        $oMessageManager = TCMSMessageManager::GetInstance();

        return $oMessageManager->ConsumerHasMessages($this->sModuleSpotName);
    }

    /**
     * return parameters send to the module using the TGlobal::GetModuleURLParameter() method.
     *
     * @param string|array $parameterNameOrNames - if nothing is passed, all parameters will be returned
     *                                           - if you pass an array of field names, all matches will be returned
     *                                           if on of the elements does not exists, it will still be included in the array
     *                                           but its value will be null
     *                                           - if you pass a string, the matching value will be returned - or null if it is not found
     * @param string|array $defaultValueOrValues - if this is set, the defined value will be returned for fields in which we did not find a match
     * @param string       $sFilter
     *
     * @return string|array
     */
    protected function GetUserInput($parameterNameOrNames = null, $defaultValueOrValues = null, $sFilter = null)
    {
        static $aInputList = array();
        if (!array_key_exists($this->sModuleSpotName, $aInputList)) {
            $aInputList[$this->sModuleSpotName] = $this->global->GetUserData($this->sModuleSpotName, array(), 'TCMSUserInputFilter;TCMSUserInput/filter;Core');
            if (!is_array($aInputList[$this->sModuleSpotName])) {
                $aInputList[$this->sModuleSpotName] = array();
            }
        }

        $aResults = array();
        if (is_null($parameterNameOrNames)) {
            $aResults = $aInputList[$this->sModuleSpotName];
            // now add all defaults
            if (is_array($defaultValueOrValues)) {
                foreach ($defaultValueOrValues as $sKey => $sDefault) {
                    if (!array_key_exists($sKey, $aResults)) {
                        $aResults[$sKey] = $sDefault;
                    }
                }
            }
        } elseif (is_array($parameterNameOrNames)) {
            foreach ($parameterNameOrNames as $sKey) {
                if (array_key_exists($sKey, $aInputList[$this->sModuleSpotName])) {
                    $aResults[$sKey] = $aInputList[$this->sModuleSpotName][$sKey];
                } elseif (!is_null($defaultValueOrValues)) {
                    if (!is_array($defaultValueOrValues)) {
                        $aResults[$sKey] = $defaultValueOrValues;
                    } elseif (array_key_exists($sKey, $defaultValueOrValues)) {
                        $aResults[$sKey] = $defaultValueOrValues[$sKey];
                    } else {
                        $aResults[$sKey] = null;
                    }
                }
            }
        } elseif (!empty($parameterNameOrNames)) {
            if (array_key_exists($parameterNameOrNames, $aInputList[$this->sModuleSpotName])) {
                $aResults[$parameterNameOrNames] = $aInputList[$this->sModuleSpotName][$parameterNameOrNames];
            } elseif (!is_null($defaultValueOrValues)) {
                $aResults[$parameterNameOrNames] = $defaultValueOrValues;
            } else {
                $aResults[$parameterNameOrNames] = null;
            }
        }

        if (!is_null($parameterNameOrNames) && !is_array($parameterNameOrNames)) {
            $vReturn = $aResults[$parameterNameOrNames];
        } else {
            $vReturn = $aResults;
        }
        if (!is_null($sFilter)) {
            $vReturn = TCMSUserInput::FilterValue($vReturn, $sFilter);
        }

        return $vReturn;
    }

    /**
     * if you need to change the state of $this->bAllowHTMLDivWrapping within
     * e.g. the execute proccess of the module you can use this function.
     *
     * @param bool $bState
     */
    protected function SetHTMLDivWrappingStatus($bState)
    {
        $this->bAllowHTMLDivWrapping = $bState;
    }

    /**
     * returns the state of the module for wrapping automatically a div around by the module loader.
     *
     * @return bool
     */
    public function IsHTMLDivWrappingAllowed()
    {
        return $this->bAllowHTMLDivWrapping;
    }

    /**
     * injects virtual module spots with modules in $oModuleLoader->modules.
     *
     * @see MTPkgMultiModuleCoreEndPoint::InjectVirtualModuleSpots
     *
     * @param TUserModuleLoader
     */
    public function InjectVirtualModuleSpots(&$oModuleLoader)
    {
    }

    /**
     * @param string $identifier
     * @param string $field
     *
     * @return TdbCmsTplModule
     */
    protected function getModuleObject($identifier, $field = 'id')
    {
        $dbAccessLayer = ServiceLocator::get('chameleon_system_core.database_access_layer_cms_tpl_module');
        if ('id' === $field) {
            return $dbAccessLayer->loadFromId($identifier);
        } else {
            return $dbAccessLayer->loadFromField($field, $identifier);
        }
    }

    /**
     * @param Request $request
     * @param bool    $isLegacyModule
     *
     * @return Response
     *
     * @throws ModuleException
     */
    final public function __invoke(Request $request, $isLegacyModule)
    {
        $response = new Response();
        if (true === $this->_AllowCache()) {
            $cache = $this->getCache();
            $cacheKey = $cache->getKey($this->_GetCacheParameters());
            $cachedContent = $cache->get($cacheKey);
            if (null !== $cachedContent) {
                $response->setContent($cachedContent);

                return $this->injectPostRenderVariables($response);
            }
        }

        $oLegacySnippetRenderer = null;
        $oRenderer = $this->getViewRenderer();
        if (true === $isLegacyModule) {
            $oLegacySnippetRenderer = TPkgSnippetRendererLegacy::GetNewInstance($this, IPkgSnippetRenderer::SOURCE_TYPE_CMSMODULE);
            $oRenderer->AddMapper(new TPkgViewRendererModuleLegacyMapper());
            $oRenderer->AddSourceObject('oModuleInstance', $this);
            $sViewPathReference = &$this->viewTemplate;
        } else {
            $oModule = $this->getModuleObject($this->aModuleConfig['model'], 'classname');
            if (null === $oModule) {
                $errorMessage = sprintf('Error: Module "%s" not found', $this->aModuleConfig['model']);
                if (true === _DEVELOPMENT_MODE) {
                    $response->setContent($errorMessage);
                }
                $this->getLogger()->error($errorMessage);

                return $response;
            }

            $oRenderer->AddMapper($this);
            $oRenderer->AddSourceObject('instanceID', $this->instanceID);
            $oRenderer->AddSourceObject('aModuleConfig', $this->aModuleConfig);
            $oRenderer->AddSourceObject('sModuleSpotName', $this->sModuleSpotName);
            $sViewName = $this->aModuleConfig['view'];
            $viewMapperConfig = $oModule->getViewMapperConfig();
            $mappers = $viewMapperConfig->getMappersForConfig($sViewName);
            foreach ($mappers as $sMapperName) {
                $oRenderer->addMapperFromIdentifier(
                    $sMapperName,
                    $viewMapperConfig->getTransformationsForMapper($sViewName, $sMapperName),
                    $viewMapperConfig->getArrayMappingForMapper($sViewName, $sMapperName)
                );
            }

            $sViewPathReference = $viewMapperConfig->getSnippetForConfig($sViewName);

            $mapperChains = $oModule->getMapperChains();
            foreach ($mapperChains as $mapperChainName => $mapperChainClasses) {
                $oRenderer->addMapperChain($mapperChainName, $mapperChainClasses);
            }
        }
        $oRenderer->AddSourceObject('_moduleID', $this->sModuleSpotName);
        if ($this->_AllowCache()) {
            $response = $this->setResponseDefaultHeader($request, $response);
        }
        $sContent = '';
        try {
            $sContent = $oRenderer->Render($sViewPathReference, $oLegacySnippetRenderer, true);
            if (true === $this->_AllowCache()) {
                $cache = $this->getCache();
                $cacheKey = $cache->getKey($this->_GetCacheParameters());
                $cache->set($cacheKey, $sContent, $oRenderer->getPostRenderMapperCacheTrigger(), 0);
            }
        } catch (\TPkgSnippetRenderer_SnippetRenderingException $e) {
            if (_DEVELOPMENT_MODE) {
                $sContent = 'ERROR: '.$e->getMessage().' in '.$e->getFile().' LINE '.$e->getLine();
            }
            TTools::WriteLogEntrySimple($e->getMessage(), 4, $e->getFile(), $e->getLine());
        }

        $response->setContent($sContent);

        return $this->injectPostRenderVariables($response);
    }

    /**
     * @param Response $response
     *
     * @return Response
     */
    private function injectPostRenderVariables(Response $response)
    {
        $postRenderVariables = $this->GetPostRenderVariables();
        if (null === $postRenderVariables || 0 === count($postRenderVariables)) {
            return $response;
        }

        $injection = new TPkgCmsStringUtilities_VariableInjection();
        $content = $response->getContent();
        $response->setContent($injection->replace($content, $postRenderVariables));

        return $response;
    }

    /**
     * @param Request  $request
     * @param Response $response
     *
     * @return Response
     */
    protected function setResponseCacheHeader(Request $request, Response $response)
    {
        // no module caching for backend
        /** @var RequestInfoServiceInterface $requestInfoService */
        $requestInfoService = ServiceLocator::get('chameleon_system_core.request_info_service');
        if ($requestInfoService->isChameleonRequestType(RequestTypeInterface::REQUEST_TYPE_BACKEND)) {
            return $response;
        }

        $response->setMaxAge(ServiceLocator::getParameter('chameleon_system_core.cache.default_max_age_in_seconds'));
        $response->setPublic();

        $cache = $this->getCache();
        $cacheKey = $cache->getKey($this->_GetCacheParameters());
        $eTag = md5($cacheKey.'-NODE:'.gethostname()); //add hostname to handle multi-server setup
        $response->setEtag($eTag);

        return $response;
    }

    /**
     * called after the response is generated - will set the default headers.
     *
     * @param Request  $request
     * @param Response $response
     *
     * @return Response
     */
    protected function setResponseDefaultHeader(Request $request, Response $response)
    {
        return $response;
    }

    /**
     * hook allowing post render modification of the response. will only be called if the response is not returned from cache.
     *
     * @param Request  $request
     * @param Response $response
     */
    protected function writeETagValidationKey(Request $request, Response $response, array $cacheTrigger)
    {
        if (0 === count($cacheTrigger)) {
            return;
        }
        $this->getCache()->set(md5($response->getEtag()), true, $cacheTrigger, 0); // we do not pass a time argument, as we want the key to expire only via call or cms backend
    }

    public function __toString()
    {
        return get_class($this);
    }

    /**
     * @return \ChameleonSystem\CoreBundle\Security\AuthenticityToken\AuthenticityTokenManagerInterface
     */
    private function getAuthenticityTokenManager()
    {
        return ServiceLocator::get('chameleon_system_core.security.authenticity_token.authenticity_token_manager');
    }

    /**
     * @return CacheInterface
     */
    private function getCache()
    {
        return ServiceLocator::get('chameleon_system_core.cache');
    }

    /**
     * Returns the current controller. For modules that are defined as services the controller is NOT set as property of
     * each module, so we need to get it from the service container.
     *
     * @return ChameleonControllerInterface
     */
    private function getController(): ChameleonControllerInterface
    {
        if (null !== $this->controller) {
            return $this->controller;
        }

        return ServiceLocator::get('chameleon_system_core.chameleon_controller');
    }

    /**
     * @return ViewRenderer
     */
    private function getViewRenderer()
    {
        return ServiceLocator::get('chameleon_system_view_renderer.view_renderer');
    }

    private function getResponseVariableReplacer(): ResponseVariableReplacerInterface
    {
        return ServiceLocator::get('chameleon_system_core.response.response_variable_replacer');
    }

    private function getLogger(): LoggerInterface
    {
        return ServiceLocator::get('logger');
    }
}
