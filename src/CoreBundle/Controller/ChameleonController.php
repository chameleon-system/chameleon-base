<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ChameleonSystem\CoreBundle\Controller;

use ChameleonSystem\CoreBundle\CoreEvents;
use ChameleonSystem\CoreBundle\Event\HtmlIncludeEvent;
use ChameleonSystem\CoreBundle\Interfaces\ResourceCollectorInterface;
use ChameleonSystem\CoreBundle\Response\ResponseVariableReplacerInterface;
use ChameleonSystem\CoreBundle\Security\AuthenticityToken\AuthenticityTokenManagerInterface;
use ChameleonSystem\CoreBundle\Security\AuthenticityToken\TokenInjectionFailedException;
use ChameleonSystem\CoreBundle\Service\ActivePageServiceInterface;
use ChameleonSystem\CoreBundle\Service\RequestInfoServiceInterface;
use ChameleonSystem\CoreBundle\Util\InputFilterUtilInterface;
use ErrorException;
use esono\pkgCmsCache\CacheInterface;
use ICmsCoreRedirect;
use IViewPathManager;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use TCMSPageDefinitionFile;
use TGlobal;
use TModelBase;
use TModuleLoader;
use TPkgCmsActionPluginManager;
use TPkgCmsCoreLayoutPluginManager;
use TPkgCmsEvent;
use TPkgCmsEventManager;
use TTools;

abstract class ChameleonController implements ChameleonControllerInterface
{
    /**
     * @var AuthenticityTokenManagerInterface
     */
    private $authenticityTokenManager;
    /**
     * @var RequestStack
     */
    protected $requestStack;
    /**
     * @var CacheInterface
     */
    protected $cache;
    /**
     * @var TGlobal
     */
    protected $global;
    /**
     * @var TModuleLoader
     */
    public $moduleLoader;
    /**
     * @var array
     *
     * @deprecated since 6.3.0 - not used anymore
     */
    protected $postRenderVariables;
    /**
     * @var array
     */
    protected $aHeaderIncludes = array();
    /**
     * @var array
     */
    protected $aFooterIncludes = array();
    /**
     * @var string
     *
     * @deprecated since 6.3.0 - not used anymore
     */
    protected $sGeneratedPage;
    /**
     * @var bool
     */
    private $bBlockAutoFlushToBrowser = false;
    /**
     * @var IViewPathManager
     */
    protected $viewPathManager;

    /** @var ActivePageServiceInterface */
    protected $activePageService;
    /**
     * @var EventDispatcherInterface
     */
    private $eventDispatcher;
    /**
     * @var RequestInfoServiceInterface
     */
    private $requestInfoService;
    /**
     * @var ICmsCoreRedirect
     *
     * @deprecated since 6.1.9 - no longer used in this class.
     */
    protected $redirect;
    /**
     * @var InputFilterUtilInterface
     */
    private $inputFilterUtil;
    /**
     * @var ResourceCollectorInterface
     */
    private $resourceCollector;
    /**
     * @var ResponseVariableReplacerInterface
     */
    private $responseVariableReplacer;

    public function __construct(
        RequestStack $requestStack,
        EventDispatcherInterface $eventDispatcher,
        TModuleLoader $moduleLoader,
        IViewPathManager $viewPathManager = null
    ) {
        $this->requestStack = $requestStack;
        $this->moduleLoader = $moduleLoader;
        $this->moduleLoader->setController($this);
        $this->viewPathManager = $viewPathManager;
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * {@inheritdoc}
     */
    public function __invoke()
    {
        $event = new ChameleonControllerInvokeEvent($this);
        $this->eventDispatcher->dispatch(ChameleonControllerEvents::INVOKE, $event);

        $pagedef = $this->getRequest()->attributes->get('pagedef');
        $this->handleRequest($pagedef);

        return $this->getResponse();
    }

    /**
     * @return Request
     */
    protected function getRequest()
    {
        return $this->requestStack->getCurrentRequest();
    }

    /**
     * @param TGlobal $global
     */
    public function setGlobal($global)
    {
        $this->global = $global;
    }

    /**
     * @param bool $bBlockAutoFlushToBrowser
     */
    public function SetBlockAutoFlushToBrowser($bBlockAutoFlushToBrowser)
    {
        $this->bBlockAutoFlushToBrowser = $bBlockAutoFlushToBrowser;
    }

    /**
     * @return bool
     */
    public function getBlockAutoFlushToBrowser()
    {
        return $this->bBlockAutoFlushToBrowser;
    }

    protected function sendDefaultHeaders()
    {
        if (true === headers_sent()) {
            return;
        }

        if (CMS_AUTO_SEND_UTF8_HEADER) {
            header('Content-type: text/html; charset=UTF-8');
        }
    }

    /**
     * This method is the core of the controller. It will create the modules,
     * execute them, and render them using the layout. In detail:
     * Loads the page definition file. Then it Creates the modules defined in the page
     * definition file, and executes them. Then it will load the layout template which will run
     * the created modules to generate the page. iIf any of the modules set the $redirectPageDef
     * property of the class during the loadlayout cycle, then the function will call HandleRequest
     * with the new pagedef, otherwise it will output the page generated using the layout.
     *
     * @param string $pagedef - name of the pagedef that is to be generated
     *
     * @return Response
     *
     * @throws ErrorException
     * @throws NotFoundHttpException
     */
    protected function GeneratePage($pagedef)
    {
        $pagedefData = $this->getPagedefData($pagedef);
        if (false === $pagedefData) {
            return new Response('<div style="background-color: #ffcccc; color: #900; border: 2px solid #c00; padding-left: 10px; padding-right: 10px; padding-top: 5px; padding-bottom: 5px; font-weight: bold; font-size: 11px; min-height: 40px; display: block;">Error: invalid page definition: '.TGlobal::OutHTML($pagedef).'</div>', Response::HTTP_NOT_FOUND);
        }

        $this->moduleLoader->LoadModules($pagedefData['moduleList']);

        foreach ($this->moduleLoader->modules as $sSpotName => $module) {
            $this->moduleLoader->modules[$sSpotName]->InjectVirtualModuleSpots($this->moduleLoader);
        }
        reset($this->moduleLoader->modules);

        $this->InitializeModules();
        $this->ExecuteModuleMethod($this->moduleLoader);

        $templatePath = $this->LoadLayoutTemplate($pagedefData['sLayoutFile']);
        if (false === file_exists($templatePath)) {
            $sErrorMessage = '<div style="background-color: #ffcccc; color: #900; border: 2px solid #c00; padding-left: 10px; padding-right: 10px; padding-top: 5px; padding-bottom: 5px; font-weight: bold; font-size: 11px; min-height: 40px; display: block;">Error: Invalid template: '.TGlobal::OutHTML($templatePath).' ('.TGlobal::OutHTML($pagedefData['sLayoutFile']).")</div>\n";
            /** @noinspection CallableInLoopTerminationConditionInspection */
            /** @noinspection SuspiciousLoopInspection */
            for ($i = 0; ob_get_level() > $i; ++$i) {
                ob_end_flush();
            }

            return new Response($sErrorMessage, Response::HTTP_NOT_FOUND);
        }

        // $modules and $pluginManager are needed in templates
        $modules = $this->moduleLoader;
        /** @noinspection PhpUnusedLocalVariableInspection */
        /** @noinspection OnlyWritesOnParameterInspection */
        $pluginManager = new TPkgCmsCoreLayoutPluginManager($modules);
        $layoutFile = TGlobal::ProtectedPath($templatePath, '.layout.php');

        $level = ob_get_level();

        ob_start();
        include $layoutFile;

        $sPageContent = ob_get_clean();

        if (ob_get_level() !== $level) {
            echo $sPageContent;
            throw new ErrorException("There was a problem with output buffering - someone in ({$layoutFile}) changed the buffer output level.", 0, E_USER_ERROR, __FILE__, __LINE__);
        }

        $sPageContent = $this->PreOutputCallbackFunction($sPageContent);

        return new Response($sPageContent);
    }

    /**
     * @param string $pagedef
     *
     * @return array|bool
     */
    private function getPagedefData($pagedef)
    {
        $cacheKey = array(
            'type' => 'pagedefdata',
            'pagedef' => $pagedef,
            'requestMasterPageDef' => $this->inputFilterUtil->getFilteredInput('__masterPageDef', false),
            'isTemplateEngineMode' => $this->requestInfoService->isCmsTemplateEngineEditMode(),
            'cmsuserdefined' => TGlobal::CMSUserDefined(),
        );

        if ($cacheKey['cmsuserdefined'] && $cacheKey['requestMasterPageDef']) {
            $cacheKey['get_id'] = $this->inputFilterUtil->getFilteredInput('id');
        }

        $key = $this->cache->getKey($cacheKey);
        $pagedefData = $this->cache->get($key);
        if (null !== $pagedefData) {
            return $pagedefData;
        }
        $oPageDefinitionFile = $this->GetPagedefObject($pagedef);
        if (false !== $oPageDefinitionFile) {
            // GetLayoutFile will reload the module list so MAKE SURE TO CALL GetModuleList first (and yes, i know this is terrible)
            $pagedefData = array(
                'moduleList' => $oPageDefinitionFile->GetModuleList(),
                'sLayoutFile' => $oPageDefinitionFile->GetLayoutFile(),
            );

            $aTrigger = array(
                array('table' => 'cms_tpl_page', 'id' => $pagedef),
                array('table' => 'cms_tree', 'id' => null),
                array('table' => 'cms_tree_node', 'id' => null),
                array('table' => 'cms_master_pagedef', 'id' => null),
            );
            $this->cache->set($key, $pagedefData, $aTrigger);
        } else {
            $pagedefData = false;
        }

        return $pagedefData;
    }

    /**
     * {@inheritdoc}
     */
    public function setCache(CacheInterface $cache)
    {
        $this->cache = $cache;
    }

    /**
     * return the page definition object. by default, this is file based, but may be page based (template engine).
     *
     * @var string $pagedef
     *
     * @return TCMSPageDefinitionFile|bool
     */
    public function &GetPagedefObject($pagedef)
    {
        /** @var $oPageDefinitionFile TCMSPageDefinitionFile */
        $oPageDefinitionFile = new TCMSPageDefinitionFile();
        $fullPageDefPath = $this->PageDefinitionFile($pagedef);
        $pagePath = substr($fullPageDefPath, 0, -strlen($pagedef.'.pagedef.php'));

        if (!$oPageDefinitionFile->Load($pagedef, $pagePath)) {
            $oPageDefinitionFile = false;
        }

        return $oPageDefinitionFile;
    }

    /**
     * returns the full path to a page definition file given the page definition name.
     *
     * @param string $pagedef - name of the pagedef
     *
     * @return string
     */
    protected function PageDefinitionFile($pagedef)
    {
        // we can select a location using a get parameter (_pagedefType). it may be one of: Core, Custom-Core, and Customer
        if (null === $pagedefType = $this->inputFilterUtil->getFilteredInput('_pagedefType')) {
            $pagedefType = 'Core';
        }
        $path = $this->global->_GetPagedefRootPath($pagedefType);

        return $path.'/'.$pagedef.'.pagedef.php';
    }

    /**
     * call the init function on all modules.
     */
    protected function InitializeModules()
    {
        reset($this->moduleLoader->modules);

        foreach ($this->moduleLoader->modules as $spotName => $module) {
            $this->global->SetExecutingModulePointer($this->moduleLoader->modules[$spotName]);
            $this->moduleLoader->modules[$spotName]->Init();
            $tmp = null;
            $this->global->SetExecutingModulePointer($tmp);
        }
        reset($this->moduleLoader->modules);
    }

    /**
     * a page can execute any number of functions in any module. which functions
     * to execute, in which module, needs to be passed via POST or GET through
     * the variable module_fnc. This function will fetch the contents of that
     * variable and loop through the resulting list of module->function sets. if the
     * function exists within the specified module it will be called. order of
     * execution is the same as the order in module_fnc array.
     *
     * @param TModuleLoader $modulesObject
     */
    protected function ExecuteModuleMethod(&$modulesObject)
    {
        $moduleFunctions = $this->getRequestedModuleFunctions();

        if (0 === \count($moduleFunctions)) {
            return;
        }

        foreach ($moduleFunctions as $spotName => $method) {
            $method = trim($method);
            if ('' === $method) {
                continue;
            }
            if (array_key_exists($spotName, $modulesObject->modules)) {
                /**
                 * @var TModelBase $module
                 */
                $module = $modulesObject->modules[$spotName];
                if ($this->isModuleMethodCallAllowed($module, $method)) {
                    $this->global->SetExecutingModulePointer($module);
                    $module->_CallMethod($method);
                    $tmp = null;
                    $this->global->SetExecutingModulePointer($tmp);
                }
            } else {
                $oActivePage = $this->activePageService->getActivePage();
                if ($oActivePage) {
                    $oActionPluginManager = new TPkgCmsActionPluginManager($oActivePage);
                    if ($oActionPluginManager->actionPluginExists($spotName)) {
                        $oActionPluginManager->callAction($spotName, $method, $this->global->GetUserData());
                    }
                }
            }
        }
    }

    /**
     * Returns all module function definitions of the request specified by either POST or GET
     * in the form 'spot name' => 'method name'. POST has precedence (first in the array).
     *
     * @return array
     */
    private function getRequestedModuleFunctions(): array
    {
        $moduleFunctions = $this->inputFilterUtil->getFilteredPostInput('module_fnc');
        if (false === \is_array($moduleFunctions)) {
            $moduleFunctions = [];
        }
        $moduleFunctionsGet = $this->inputFilterUtil->getFilteredGetInput('module_fnc');
        if (false === \is_array($moduleFunctionsGet)) {
            $moduleFunctionsGet = [];
        }

        $moduleFunctions = \array_merge($moduleFunctions, $moduleFunctionsGet);

        return $moduleFunctions;
    }

    /**
     * @param TModelBase $module
     * @param string     $method
     *
     * @return bool
     */
    private function isModuleMethodCallAllowed(TModelBase $module, $method)
    {
        return true === $this->authenticityTokenManager->isTokenValid()
            || true === $module->AllowAccessWithoutAuthenticityToken($method);
    }

    /**
     * returns the full path to a layout template given the layout template name.
     *
     * @param string $layoutTemplate - name of the layout template
     *
     * @return string
     */
    protected function LoadLayoutTemplate($layoutTemplate)
    {
        if (null === $pagedefType = $this->inputFilterUtil->getFilteredInput('_pagedefType')) {
            $pagedefType = 'Core';
        }
        $path = TGlobal::_GetLayoutRootPath($pagedefType);
        $pagedefPath = $path.'/'.$layoutTemplate.'.layout.php';
        if (!file_exists($pagedefPath)) {
            $path = TGlobal::_GetLayoutRootPath('Core');
            $pagedefPath = $path.'/'.$layoutTemplate.'.layout.php';
        }

        return $pagedefPath;
    }

    /**
     * gets called when the page output is passed from buffer to client.
     *
     * @param string $sPageContent the contents of the output buffer
     *
     * @return string
     */
    public function PreOutputCallbackFunction(&$sPageContent)
    {
        static $bHeaderParsed = false;
        TPkgCmsEventManager::GetInstance()->NotifyObservers(
            TPkgCmsEvent::GetNewInstance($this, TPkgCmsEvent::CONTEXT_CORE, TPkgCmsEvent::NAME_PRE_OUTPUT_CALLBACK_FUNCTION, array('sPageContent' => $sPageContent)));

        if (!$bHeaderParsed) {
            // parse and replace header includes, call resource collection
            $sPageContent = $this->injectHeaderIncludes($sPageContent);
        }

        if (false !== strrpos($sPageContent, '<!--#CMSFOOTERCODE#-->') || false !== strrpos($sPageContent, '</body>')) {
            $sCustomFooterData = $this->getHTMLFooterDataAsString();
            if (false !== strrpos($sPageContent, '<!--#CMSFOOTERCODE#-->')) {
                $sPageContent = str_replace('<!--#CMSFOOTERCODE#-->', $sCustomFooterData, $sPageContent);
            } else {
                $sPageContent = str_replace('</body>', $sCustomFooterData."\n</body>", $sPageContent);
            }
        }

        if (TGlobal::CMSUserDefined()) {
            if ('true' === $this->inputFilterUtil->getFilteredInput('esdisablelinks')) {
                $sPattern = "/<a([^>]+)href=[']([^']*)[']/Uusi";
                $sReplacePatter = '<a$1href="javascript:var tmp=false;"';

                $sPageContent = preg_replace($sPattern, $sReplacePatter, $sPageContent);

                $sPattern = '/<a([^>]+)href=["]([^"]*)["]/Uusi';
                $sReplacePatter = '<a$1href="javascript:var tmp=false;"';

                $sPageContent = preg_replace($sPattern, $sReplacePatter, $sPageContent);
            }

            // disable frontend javascripts (css is not replaced)
            // i´m not sure if this is a brilliant idea, but it is a workaround to edit pages where frontend and backend javascript is incompatible
            if ($this->global->isFrontendJSDisabled()) {
                $tempReplacePattern = '/chameleon//javascript/jquery/jquery.js';
                $sPageContent = str_replace($tempReplacePattern, '@@jquery@@', $sPageContent);

                $tempReplacePattern = '/chameleon/javascript/jquery/jquery.js';
                $sPageContent = str_replace($tempReplacePattern, '@@jquery@@', $sPageContent);

                $tempReplacePattern = '/static/javascript/jquery/jquery.js';
                $sPageContent = str_replace($tempReplacePattern, '@@jquery@@', $sPageContent);

                $tempReplacePattern = '/static/jquery/jquery.js';
                $sPageContent = str_replace($tempReplacePattern, '@@jquery@@', $sPageContent);

                $tempReplacePattern = '/static/js/jquery.js';
                $sPageContent = str_replace($tempReplacePattern, '@@jquery@@', $sPageContent);

                if (defined('CHAMELEON_URL_GOOGLE_JQUERY') && false !== CHAMELEON_URL_GOOGLE_JQUERY) {
                    $tempReplacePattern = CHAMELEON_URL_GOOGLE_JQUERY;
                    $sPageContent = str_replace($tempReplacePattern, '@@jquery@@', $sPageContent);
                }

                $sPattern = "/<script.*\/chameleon\/+javascript.*<\/script>/i";
                $sPageContent = preg_replace($sPattern, '', $sPageContent);

                $sPattern = "/<script (?!.*blackbox.*).*\/javascript\/.*<\/script>/i";
                $sPageContent = preg_replace($sPattern, '', $sPageContent);

                $sPageContent = str_replace('@@jquery@@', '/chameleon/blackbox/javascript/jquery/jquery-3.3.1.min.js', $sPageContent);

                $sPattern = "/<script>.*?<\/script>/si";
                $sPageContent = preg_replace($sPattern, '', $sPageContent);
            }
        }

        if (!$bHeaderParsed) {
            $sPageContent = $this->runExternalResourceCollectorOnPageContent($sPageContent);

            if (stripos($sPageContent, '</head>')) {
                $bHeaderParsed = true;
            }
        }
        $sPageContent = $this->responseVariableReplacer->replaceVariables($sPageContent);
        $this->sGeneratedPage .= $sPageContent;

        return $sPageContent;
    }

    /**
     * injects module specific head includes into header part placeholder tags (like: <!--#CMSHEADERCODE#-->).
     *
     * @param string $sPageContent
     *
     * @return string
     */
    protected function injectHeaderIncludes($sPageContent)
    {
        static $aCustomHeaderData = null;

        if (
            (false === stripos($sPageContent, '<!--#CMSHEADERCODE#-->'))
            && (false === stripos($sPageContent, '<!--#CMSHEADERCODE-CSS#-->'))
            && (false === stripos($sPageContent, '<!--#CMSHEADERCODE-JS#-->'))
        ) {
            return $sPageContent; // no replace hooks - so skip process
        }

        if (null === $aCustomHeaderData) {
            $aCustomHeaderData = $this->_GetCustomHeaderData(true);
            $aCustomHeaderData = $this->splitHeaderDataIntoJSandOther($aCustomHeaderData);
        }

        $sCustomHeaderDataOTHER = implode("\n", $aCustomHeaderData['other']);
        $sCustomHeaderDataJS = implode("\n", $aCustomHeaderData['js']);

        $sCustomHeaderData = $sCustomHeaderDataOTHER."\n".$sCustomHeaderDataJS;

        $sPageContent = str_replace('<!--#CMSHEADERCODE#-->', $sCustomHeaderData, $sPageContent);

        if ($this->resourceCollector->IsAllowed()) {
            // need to keep everything in the header since the resource collection looks for it there
            $sPageContent = str_replace('<!--#CMSHEADERCODE-CSS#-->', $sCustomHeaderData, $sPageContent);
        } else {
            $sPageContent = str_replace('<!--#CMSHEADERCODE-CSS#-->', $sCustomHeaderDataOTHER, $sPageContent);
            $sPageContent = str_replace('<!--#CMSHEADERCODE-JS#-->', $sCustomHeaderDataJS, $sPageContent);
        }

        return $sPageContent;
    }

    /**
     * returns everything for the current page that should be part of the
     * page <head> tag. It will be generated from the controller, and from
     * the modules within the page.
     *
     * @param bool $bAsArray
     *
     * @return string
     */
    protected function _GetCustomHeaderData($bAsArray = false)
    {
        TPkgCmsEventManager::GetInstance()->NotifyObservers(
            TPkgCmsEvent::GetNewInstance($this, TPkgCmsEvent::CONTEXT_CORE, TPkgCmsEvent::NAME_GET_CUSTOM_HEADER_DATA));

        $event = new HtmlIncludeEvent();
        $event = $this->eventDispatcher->dispatch(CoreEvents::GLOBAL_HTML_HEADER_INCLUDE, $event);

        if ($bAsArray) {
            return $event->getData();
        } else {
            return implode("\n", $event->getData());
        }
    }

    /**
     * @param array $aResourceArray
     *
     * @return array
     */
    protected function splitHeaderDataIntoJSandOther($aResourceArray)
    {
        $aData = array('js' => array(), 'other' => array());
        foreach ($aResourceArray as $sLine) {
            // is .js file? true if it contains <script
            if (false !== stripos($sLine, '<script ') || false !== stripos($sLine, '<script>')) {
                $aData['js'][] = $sLine;
            } else {
                $aData['other'][] = $sLine;
            }
        }

        return $aData;
    }

    /**
     * wrapper for _GetCustomFooterData - the method caches the result of _GetCustomFooterData
     * we moved the cache to this method since children of the controller overwrite the method GetCustomFooterData
     * and would each have to implement caching if we had kept caching there.
     *
     * @return string
     */
    private function getHTMLFooterDataAsString()
    {
        static $footerData = null;
        if (null === $footerData) {
            $footerData = $this->_GetCustomFooterData();
        }

        return $footerData;
    }

    /**
     * returns everything for the current page that should be placed before the
     * </body> tag. It will be generated from the controller, and from
     * the modules within the page.
     *
     * @return string
     */
    protected function _GetCustomFooterData()
    {
        TPkgCmsEventManager::GetInstance()->NotifyObservers(
            TPkgCmsEvent::GetNewInstance($this, TPkgCmsEvent::CONTEXT_CORE, TPkgCmsEvent::NAME_GET_CUSTOM_FOOTER_DATA));

        $event = new HtmlIncludeEvent();

        $event = $this->eventDispatcher->dispatch(CoreEvents::GLOBAL_HTML_FOOTER_INCLUDE, $event);

        $aModuleFooterData = $event->getData();

        return implode("\n", $aModuleFooterData);
    }

    /**
     * @param string $sPageContent
     *
     * @return string
     */
    protected function runExternalResourceCollectorOnPageContent($sPageContent)
    {
        return $this->resourceCollector->CollectExternalResources($sPageContent);
    }

    /**
     * extra hook that replaces messages and custom vars in the string passed.
     *
     * @param object|array|string $sPageContent
     *
     * @return string
     *
     * @deprecated since 6.3.0 - use ResponseVariableReplacerInterface::replaceVariables() instead.
     *
     * @throws TokenInjectionFailedException
     */
    public function PreOutputCallbackFunctionReplaceCustomVars($sPageContent)
    {
        return $this->responseVariableReplacer->replaceVariables($sPageContent);
    }

    /**
     * return an array of variables to search/replace in the rendered page
     * use this hook to add vars that should never be cached.
     *
     * @return array
     *
     * @deprecated since 6.3.0 - no longer used. To add custom variables use ResponseVariableReplacerInterface::addVariable() instead of
     *             overwriting this method.
     */
    protected function GetPostRenderVariables()
    {
        if (null === $this->postRenderVariables) {
            $this->postRenderVariables = TTools::AddStaticPageVariables(null);
            $this->postRenderVariables[AuthenticityTokenManagerInterface::TOKEN_ID] = $this->authenticityTokenManager->getStoredToken();

            $this->postRenderVariables['CMS-PROTOCOL'] = $this->getRequest()->getScheme();
        }

        return $this->postRenderVariables;
    }

    /**
     * {@inheritdoc}
     *
     * This method will do nothing if CHAMELEON_ENABLE_FLUSHING is set to false in the config file.
     */
    public function FlushContentToBrowser($bEnableAutoFlush = false)
    {
        if (false === CHAMELEON_ENABLE_FLUSHING && !TGlobal::IsCMSMode()) {
            return;
        }
        if (true === $this->bBlockAutoFlushToBrowser) {
            return;
        }
        $sPageContent = ob_get_clean();
        if (!empty($sPageContent)) {
            $sPageContent = $this->PreOutputCallbackFunction($sPageContent);
            echo $sPageContent;
        }
        flush();
        ob_start();
        if ($bEnableAutoFlush) {
            $this->moduleLoader->SetEnableAutoFlush(true);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function AddHTMLFooterLine($sLine)
    {
        if (!in_array($sLine, $this->aFooterIncludes)) {
            $this->aFooterIncludes[] = $sLine;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function AddHTMLHeaderLine($sLine)
    {
        if (!in_array($sLine, $this->aHeaderIncludes)) {
            $this->aHeaderIncludes[] = $sLine;
        }
    }

    /**
     * outputs the final generated webpage.
     *
     * @param string $sContent
     * @param bool   $bContentLoadedFromCache
     *
     * @return mixed|string
     */
    protected function augmentContent($sContent, $bContentLoadedFromCache = false)
    {
        return str_replace('</html>', "\n<!-- chameleon-status: complete --></html>", $sContent);
    }

    /**
     * performs a header redirect. the content of aParameters will be added as
     * get parameters to the redirect.
     *
     * @param array $aParameters - assoc array of the get parameters
     *
     * @deprecated use \ChameleonSystem\CoreBundle\ServiceLocator::get('chameleon_system_core.redirect')->redirectToActivePage($aParameters) instead
     */
    public function HeaderRedirect($aParameters)
    {
        $this->redirect->redirectToActivePage($aParameters);
    }

    /**
     * performs a header redirect to a specified URL.
     *
     * @param string $url                    - relative URL or full URL with http:// to which we want to redirect
     * @param bool   $bAllowOnlyRelativeURLs - strips scheme from URL and adds current HOST - default false
     *
     * @deprecated use \ChameleonSystem\CoreBundle\ServiceLocator::get('chameleon_system_core.redirect') instead
     */
    public function HeaderURLRedirect($url = '', $bAllowOnlyRelativeURLs = false)
    {
        $this->redirect->redirect($url, Response::HTTP_FOUND, $bAllowOnlyRelativeURLs);
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return get_class($this);
    }

    /**
     * @param ActivePageServiceInterface $activePageService
     */
    public function setActivePageService(ActivePageServiceInterface $activePageService)
    {
        $this->activePageService = $activePageService;
    }

    /**
     * use method to check access of current user to the page. use it to redirect to an access denied page if the user does
     * not have the correct permissions.
     */
    protected function accessCheckHook()
    {
    }

    /**
     * @param string $pagedef
     */
    protected function handleRequest($pagedef)
    {
    }

    /**
     * @return array
     */
    public function getHtmlHeaderIncludes()
    {
        return $this->aHeaderIncludes;
    }

    /**
     * @return array
     */
    public function getHtmlFooterIncludes()
    {
        return $this->aFooterIncludes;
    }

    /**
     * @param AuthenticityTokenManagerInterface $authenticityTokenManager
     */
    public function setAuthenticityTokenManager($authenticityTokenManager)
    {
        $this->authenticityTokenManager = $authenticityTokenManager;
    }

    /**
     * @param RequestInfoServiceInterface $requestInfoService
     */
    public function setRequestInfoService($requestInfoService)
    {
        $this->requestInfoService = $requestInfoService;
    }

    /**
     * @param ICmsCoreRedirect $redirect
     *
     * @deprecated since 6.1.9 - no longer used in this class.
     */
    public function setRedirect($redirect)
    {
        $this->redirect = $redirect;
    }

    /**
     * @return InputFilterUtilInterface
     */
    protected function getInputFilterUtil()
    {
        return $this->inputFilterUtil;
    }

    /**
     * @param InputFilterUtilInterface $inputFilterUtil
     */
    public function setInputFilterUtil(InputFilterUtilInterface $inputFilterUtil)
    {
        $this->inputFilterUtil = $inputFilterUtil;
    }

    public function setResourceCollector(ResourceCollectorInterface $resourceCollector): void
    {
        $this->resourceCollector = $resourceCollector;
    }

    public function setResponseVariableReplacer(ResponseVariableReplacerInterface $responseVariableReplacer): void
    {
        $this->responseVariableReplacer = $responseVariableReplacer;
    }
}
