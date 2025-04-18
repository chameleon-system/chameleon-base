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
use ChameleonSystem\CoreBundle\DataAccess\DataAccessCmsMasterPagedefInterface;
use ChameleonSystem\CoreBundle\Event\FilterContentEvent;
use ChameleonSystem\CoreBundle\Event\HtmlIncludeEvent;
use ChameleonSystem\CoreBundle\Interfaces\ResourceCollectorInterface;
use ChameleonSystem\CoreBundle\Response\ResponseVariableReplacerInterface;
use ChameleonSystem\CoreBundle\Security\AuthenticityToken\AuthenticityTokenManagerInterface;
use ChameleonSystem\CoreBundle\Service\ActivePageServiceInterface;
use ChameleonSystem\CoreBundle\Service\RequestInfoServiceInterface;
use ChameleonSystem\CoreBundle\ServiceLocator;
use ChameleonSystem\CoreBundle\Util\InputFilterUtilInterface;
use ChameleonSystem\SecurityBundle\Service\SecurityHelperAccess;
use ChameleonSystem\SecurityBundle\Voter\CmsUserRoleConstants;
use esono\pkgCmsCache\CacheInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

abstract class ChameleonController implements ChameleonControllerInterface
{
    private AuthenticityTokenManagerInterface $authenticityTokenManager;
    protected RequestStack $requestStack;
    protected CacheInterface $cache;
    protected \TGlobal $global;
    protected \TModuleLoader $moduleLoader;
    protected array $aHeaderIncludes = [];
    protected array $aFooterIncludes = [];
    protected ?\IViewPathManager $viewPathManager;
    protected ActivePageServiceInterface $activePageService;
    private EventDispatcherInterface $eventDispatcher;
    private RequestInfoServiceInterface $requestInfoService;
    private InputFilterUtilInterface $inputFilterUtil;
    private ResourceCollectorInterface $resourceCollector;
    private DataAccessCmsMasterPagedefInterface $dataAccessCmsMasterPagedef;
    private ResponseVariableReplacerInterface $responseVariableReplacer;
    private SecurityHelperAccess $securityHelperAccess;

    public function __construct(
        RequestStack $requestStack,
        EventDispatcherInterface $eventDispatcher,
        DataAccessCmsMasterPagedefInterface $dataAccessCmsMasterPagedef,
        \TModuleLoader $moduleLoader,
        SecurityHelperAccess $securityHelperAccess,
        ?\IViewPathManager $viewPathManager = null,
    ) {
        $this->requestStack = $requestStack;
        $this->eventDispatcher = $eventDispatcher;
        $this->dataAccessCmsMasterPagedef = $dataAccessCmsMasterPagedef;
        $this->moduleLoader = $moduleLoader;
        $this->securityHelperAccess = $securityHelperAccess;
        $this->viewPathManager = $viewPathManager;
        $this->moduleLoader->setController($this);
    }

    public function getModuleLoader(): \TModuleLoader
    {
        return $this->moduleLoader;
    }

    /**
     * {@inheritdoc}
     */
    public function __invoke()
    {
        $event = new ChameleonControllerInvokeEvent($this);
        $this->eventDispatcher->dispatch($event, ChameleonControllerEvents::INVOKE);

        $pagedef = $this->getRequest()->attributes->get('pagedef');
        $this->handleRequest($pagedef);

        return $this->getResponse();
    }

    /**
     * @return Request|null
     */
    protected function getRequest()
    {
        return $this->requestStack->getCurrentRequest();
    }

    /**
     * @param \TGlobal $global
     *
     * @return void
     */
    public function setGlobal($global)
    {
        $this->global = $global;
    }

    /**
     * @return void
     */
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
     * @throws \ErrorException
     * @throws NotFoundHttpException
     */
    protected function GeneratePage($pagedef)
    {
        $pagedefData = $this->getPagedefData($pagedef);
        if (null === $pagedefData) {
            return new Response('<div style="background-color: #ffcccc; color: #900; border: 2px solid #c00; padding-left: 10px; padding-right: 10px; padding-top: 5px; padding-bottom: 5px; font-weight: bold; font-size: 11px; min-height: 40px; display: block;">Error: invalid page definition: '.\TGlobal::OutHTML($pagedef).'</div>', Response::HTTP_NOT_FOUND);
        }

        $this->moduleLoader->LoadModules($pagedefData['moduleList']);

        foreach ($this->moduleLoader->modules as $sSpotName => $module) {
            $this->moduleLoader->modules[$sSpotName]->InjectVirtualModuleSpots($this->moduleLoader);
        }
        reset($this->moduleLoader->modules);

        $this->moduleLoader->InitModules();
        $this->ExecuteModuleMethod($this->moduleLoader);

        $templatePath = $this->LoadLayoutTemplate($pagedefData['sLayoutFile']);
        if (false === file_exists($templatePath)) {
            $sErrorMessage = '<div style="background-color: #ffcccc; color: #900; border: 2px solid #c00; padding-left: 10px; padding-right: 10px; padding-top: 5px; padding-bottom: 5px; font-weight: bold; font-size: 11px; min-height: 40px; display: block;">Error: Invalid template: '.\TGlobal::OutHTML($templatePath).' ('.\TGlobal::OutHTML($pagedefData['sLayoutFile']).")</div>\n";
            /* @noinspection CallableInLoopTerminationConditionInspection */
            /* @noinspection SuspiciousLoopInspection */
            for ($i = 0; ob_get_level() > $i; ++$i) {
                ob_end_flush();
            }

            return new Response($sErrorMessage, Response::HTTP_NOT_FOUND);
        }

        // $modules and $pluginManager are needed in templates
        $modules = $this->moduleLoader;
        /** @noinspection PhpUnusedLocalVariableInspection */
        /** @noinspection OnlyWritesOnParameterInspection */
        $pluginManager = new \TPkgCmsCoreLayoutPluginManager($modules);
        $layoutFile = \TGlobal::ProtectedPath($templatePath, '.layout.php');

        $level = ob_get_level();

        ob_start();
        include $layoutFile;

        $sPageContent = ob_get_clean();

        if (ob_get_level() !== $level) {
            echo $sPageContent;
            throw new \ErrorException("There was a problem with output buffering - someone in ({$layoutFile}) changed the buffer output level.", 0, E_USER_ERROR, __FILE__, __LINE__);
        }

        $sPageContent = $this->PreOutputCallbackFunction($sPageContent);

        return new Response($sPageContent);
    }

    private function getPagedefData(string $pagedef): ?array
    {
        $pagedefData = $this->dataAccessCmsMasterPagedef->get($pagedef);
        if (null === $pagedefData) {
            return null;
        }

        return [
            'moduleList' => $pagedefData->getModuleList(),
            'sLayoutFile' => $pagedefData->getLayoutFile(),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function setCache(CacheInterface $cache)
    {
        $this->cache = $cache;
    }

    /**
     * a page can execute any number of functions in any module. which functions
     * to execute, in which module, needs to be passed via POST or GET through
     * the variable module_fnc. This function will fetch the contents of that
     * variable and loop through the resulting list of module->function sets. if the
     * function exists within the specified module it will be called. order of
     * execution is the same as the order in module_fnc array.
     *
     * @param \TModuleLoader $modulesObject
     *
     * @return void
     */
    protected function ExecuteModuleMethod($modulesObject)
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
                 * @var \TModelBase $module
                 */
                $module = $modulesObject->modules[$spotName];
                if ($this->isModuleMethodCallAllowed($module, $method)) {
                    $this->global->SetExecutingModulePointer($module);
                    $module->_CallMethod($method);
                    $tmp = null;

                    /* @psalm-suppress NullArgument */
                    $this->global->SetExecutingModulePointer($tmp);
                }
            } else {
                $oActivePage = $this->activePageService->getActivePage();
                if ($oActivePage) {
                    $oActionPluginManager = new \TPkgCmsActionPluginManager($oActivePage);
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
     */
    private function getRequestedModuleFunctions(): array
    {
        $moduleFunctions = $this->inputFilterUtil->getFilteredPostInputArray('module_fnc');
        if (false === \is_array($moduleFunctions)) {
            $moduleFunctions = [];
        }
        $moduleFunctionsGet = $this->inputFilterUtil->getFilteredGetInputArray('module_fnc');
        if (false === \is_array($moduleFunctionsGet)) {
            $moduleFunctionsGet = [];
        }

        return \array_merge($moduleFunctions, $moduleFunctionsGet);
    }

    /**
     * @param string $method
     *
     * @return bool
     */
    private function isModuleMethodCallAllowed(\TModelBase $module, $method)
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
        $path = \TGlobal::_GetLayoutRootPath($pagedefType);
        $pagedefPath = $path.'/'.$layoutTemplate.'.layout.php';
        if (!file_exists($pagedefPath)) {
            $path = \TGlobal::_GetLayoutRootPath('Core');
            $pagedefPath = $path.'/'.$layoutTemplate.'.layout.php';
        }

        return $pagedefPath;
    }

    /**
     * gets called when the page output is passed from buffer to client.
     *
     * @param string $sPageContent the contents of the output buffer
     *
     * @deprecated since 7.1.0 - you may use of symfony's "kernel.response" event
     *
     * @return string
     */
    public function PreOutputCallbackFunction($sPageContent)
    {
        static $bHeaderParsed = false;
        \TPkgCmsEventManager::GetInstance()->NotifyObservers(
            \TPkgCmsEvent::GetNewInstance($this, \TPkgCmsEvent::CONTEXT_CORE, \TPkgCmsEvent::NAME_PRE_OUTPUT_CALLBACK_FUNCTION, ['sPageContent' => $sPageContent]));

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

        if ($this->securityHelperAccess->isGranted(CmsUserRoleConstants::CMS_USER)) {
            if ('true' === $this->inputFilterUtil->getFilteredInput('esdisablelinks')) {
                $pattern = "/<a([^>]+)href=[']([^']*)[']/Uusi";
                $replacePattern = '<a$1href="javascript:var tmp=false;"';

                $sPageContent = preg_replace($pattern, $replacePattern, $sPageContent);

                $pattern = '/<a([^>]+)href=["]([^"]*)["]/Uusi';

                $sPageContent = preg_replace($pattern, $replacePattern, $sPageContent);
            }

            // disable frontend javascripts (css is not replaced)
            if (true === $this->requestInfoService->isFrontendJsDisabled()) {
                $pattern = "/<script.*\/chameleon\/+javascript.*<\/script>/i";
                $sPageContent = preg_replace($pattern, '', $sPageContent);

                $pattern = "/<script (?!.*blackbox.*).*\/javascript\/.*<\/script>/i";
                $sPageContent = preg_replace($pattern, '', $sPageContent);

                $pattern = "/<script>.*?<\/script>/si";
                $sPageContent = preg_replace($pattern, '', $sPageContent);
            }
        }

        if (!$bHeaderParsed) {
            $sPageContent = $this->runExternalResourceCollectorOnPageContent($sPageContent);

            if (stripos($sPageContent, '</head>')) {
                $bHeaderParsed = true;
            }
        }

        $event = new FilterContentEvent($sPageContent);
        $this->eventDispatcher->dispatch($event, CoreEvents::FILTER_CONTENT);

        return $event->getContent();
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
        /** @var array{js: string[], other: string[]} $aCustomHeaderData */
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
            $sPageContent = str_replace(['<!--#CMSHEADERCODE-CSS#-->', '<!--#CMSHEADERCODE-JS#-->'],
                [$sCustomHeaderDataOTHER, $sCustomHeaderDataJS],
                $sPageContent);
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
     * @return string|string[]
     *
     * @psalm-return ($bAsArray is true ? string[] : string)
     */
    protected function _GetCustomHeaderData($bAsArray = false)
    {
        \TPkgCmsEventManager::GetInstance()->NotifyObservers(
            \TPkgCmsEvent::GetNewInstance($this, \TPkgCmsEvent::CONTEXT_CORE, \TPkgCmsEvent::NAME_GET_CUSTOM_HEADER_DATA));

        $event = new HtmlIncludeEvent();
        /** @var HtmlIncludeEvent $event */
        $event = $this->eventDispatcher->dispatch($event, CoreEvents::GLOBAL_HTML_HEADER_INCLUDE);

        if ($bAsArray) {
            return $event->getData();
        } else {
            return implode("\n", $event->getData());
        }
    }

    /**
     * @param string[] $aResourceArray
     *
     * @return array{js: string[], other: string[]}
     */
    protected function splitHeaderDataIntoJSandOther($aResourceArray)
    {
        $aData = ['js' => [], 'other' => []];
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
     * Wrapper for _GetCustomFooterData - the method caches the result of _GetCustomFooterData
     * we moved the cache to this method since children of the controller overwrite the method GetCustomFooterData
     * and would each have to implement caching if we had kept caching there.
     */
    private function getHTMLFooterDataAsString(): string
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
        \TPkgCmsEventManager::GetInstance()->NotifyObservers(
            \TPkgCmsEvent::GetNewInstance($this, \TPkgCmsEvent::CONTEXT_CORE, \TPkgCmsEvent::NAME_GET_CUSTOM_FOOTER_DATA));

        $event = new HtmlIncludeEvent();

        /** @var HtmlIncludeEvent $event */
        $event = $this->eventDispatcher->dispatch($event, CoreEvents::GLOBAL_HTML_FOOTER_INCLUDE);

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
     * @param bool $bContentLoadedFromCache
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
     *
     * @return never
     */
    public function HeaderRedirect(array $aParameters)
    {
        $this->getRedirectService()->redirectToActivePage($aParameters);
    }

    /**
     * performs a header redirect to a specified URL.
     *
     * @param string $url - relative URL or full URL with http:// to which we want to redirect
     * @param bool $bAllowOnlyRelativeURLs - strips scheme from URL and adds current HOST - default false
     *
     * @deprecated use \ChameleonSystem\CoreBundle\ServiceLocator::get('chameleon_system_core.redirect') instead
     *
     * @return never
     */
    public function HeaderURLRedirect($url = '', $bAllowOnlyRelativeURLs = false)
    {
        $this->getRedirectService()->redirect($url, Response::HTTP_FOUND, $bAllowOnlyRelativeURLs);
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return get_class($this);
    }

    /**
     * @return void
     */
    public function setActivePageService(ActivePageServiceInterface $activePageService)
    {
        $this->activePageService = $activePageService;
    }

    /**
     * use method to check access of current user to the page. use it to redirect to an access denied page if the user does
     * not have the correct permissions.
     *
     * @return void
     */
    protected function accessCheckHook()
    {
    }

    /**
     * @param string $pagedef
     *
     * @return void
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
     * @return string[]
     */
    public function getHtmlFooterIncludes()
    {
        return $this->aFooterIncludes;
    }

    public function setAuthenticityTokenManager(AuthenticityTokenManagerInterface $authenticityTokenManager): void
    {
        $this->authenticityTokenManager = $authenticityTokenManager;
    }

    public function setRequestInfoService(RequestInfoServiceInterface $requestInfoService): void
    {
        $this->requestInfoService = $requestInfoService;
    }

    /**
     * @return InputFilterUtilInterface
     */
    protected function getInputFilterUtil()
    {
        return $this->inputFilterUtil;
    }

    public function setInputFilterUtil(InputFilterUtilInterface $inputFilterUtil): void
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

    private function getRedirectService(): \ICmsCoreRedirect
    {
        return ServiceLocator::get('chameleon_system_core.redirect');
    }
}
