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
use ChameleonSystem\CoreBundle\Exception\ModuleExecutionFailedException;
use ChameleonSystem\CoreBundle\ModuleService\ModuleExecutionStrategyInterface;
use ChameleonSystem\CoreBundle\ModuleService\ModuleResolverInterface;
use ChameleonSystem\CoreBundle\Service\RequestInfoServiceInterface;
use ChameleonSystem\CoreBundle\ServiceLocator;
use ChameleonSystem\SecurityBundle\Service\SecurityHelperAccess;
use esono\pkgCmsCache\CacheInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * loads CMS backend modules.
 * /**/
class TModuleLoader
{
    public const ESIMODULE_DIVIDER = '_esimodule_';

    /**
     * @var TModelBase[]
     */
    public array $modules = []; // an array of all model classes.
    protected ChameleonControllerInterface $controller; // a pointer to the controller of the framework
    private array $aModuleCacheData = [];
    protected RequestStack $requestStack;
    protected ModuleResolverInterface $moduleResolver;
    private CacheInterface $cache;
    private IViewPathManager $viewPathManager;
    private TGlobalBase $global;
    private ModuleExecutionStrategyInterface $moduleExecutionStrategy;
    protected RequestInfoServiceInterface $requestInfoService;
    protected SecurityHelperAccess $securityHelperAccess;

    public function __construct(
        RequestStack $requestStack,
        ModuleResolverInterface $moduleResolver,
        IViewPathManager $viewPathManager,
        CacheInterface $cache,
        TGlobalBase $global,
        ModuleExecutionStrategyInterface $moduleExecutionStrategy,
        RequestInfoServiceInterface $requestInfoService,
        SecurityHelperAccess $securityHelperAccess
    ) {
        $this->requestStack = $requestStack;
        $this->moduleResolver = $moduleResolver;
        $this->viewPathManager = $viewPathManager;
        $this->cache = $cache;
        $this->global = $global;
        $this->moduleExecutionStrategy = $moduleExecutionStrategy;
        $this->requestInfoService = $requestInfoService;
        $this->securityHelperAccess = $securityHelperAccess;
    }

    /**
     * @param string $sSpotName
     *
     * @return mixed|null
     */
    public function getModuleCacheKeyDetails($sSpotName)
    {
        return isset($this->aModuleCacheData[$sSpotName]['aKey']) ? $this->aModuleCacheData[$sSpotName]['aKey'] : null;
    }

    /**
     * @param string $sSpotName
     *
     * @return mixed|null
     */
    public function getModuleCacheTriggerDetails($sSpotName)
    {
        return isset($this->aModuleCacheData[$sSpotName]['aTrigger']) ? $this->aModuleCacheData[$sSpotName]['aTrigger'] : null;
    }

    /**
     * @param string $sSpotName
     *
     * @return mixed|bool
     */
    public function allowCacheForModule($sSpotName)
    {
        return isset($this->aModuleCacheData[$sSpotName]['bAllowCaching']) ? $this->aModuleCacheData[$sSpotName]['bAllowCaching'] : false;
    }

    /**
     * creates and stores all models listed by name in the moduleList parameter.
     * For each model it sets the view also passed via the moduleList parameter.
     *
     * @param array $moduleList
     * @param string|null $templateLanguage
     */
    public function LoadModules($moduleList, $templateLanguage = null)
    {
        $this->modules = [];
        foreach ($moduleList as $name => $config) {
            // @TODO: check if the class is a descendant of TModelBase
            $this->modules[$name] = $this->_SetModuleConfigData($name, $config, $templateLanguage);
        }
    }

    /**
     * create an instance of the requested Module and initialize it using the config data.
     *
     * @param string $name name of the module "spot" (name from the pagedef)
     * @param array $config
     * @param string|null $templateLanguage
     *
     * @return TModelBase
     */
    protected function _SetModuleConfigData($name, $config, $templateLanguage = null)
    {
        $moduleType = 'Core';
        if (array_key_exists('moduleType', $config)) {
            $moduleType = $config['moduleType'];
        }
        // fetch the module path depending on the type of the module
        $modulePath = $this->global->getModuleRootPath($moduleType);

        // map class via cms_config if a mapping is specified
        $sModuleClassName = $config['model'];
        $sMappedPath = $modulePath;
        $bModuleIsExtended = false;
        if (TGlobal::IsCMSMode()) {
            $oCMSConfig = TdbCmsConfig::GetInstance();
            $sMappedClassName = $oCMSConfig->GetRealModuleClassName($sModuleClassName);
            if (false !== $sMappedClassName) {
                $bModuleIsExtended = true;
                $sModuleOriginalClassName = $sModuleClassName;
                $sModuleClassName = $sMappedClassName;
                $sModuleExtensionType = $oCMSConfig->GetModuleExtensionType($sModuleOriginalClassName);
                $sMappedPath = $this->global->getModuleRootPath($sModuleExtensionType);
            }
        }
        $tmpModel = $this->CreateModuleInstance($sModuleClassName);
        if ($this->isLegacyModule($tmpModel)) {
            $tmpModel->viewTemplate = $this->viewPathManager->getWebModuleViewPath($config['view'], $sModuleClassName, 'Customer', $sMappedPath);
            // if we are extending, check if the view exists in the customer dir, if not use the one from the core
            if ($bModuleIsExtended && !file_exists($tmpModel->viewTemplate)) {
                $tmpModel->viewTemplate = $this->viewPathManager->getWebModuleViewPath($config['view'], $config['model'], 'Customer', $modulePath);
            }
        }

        $tmpModel->aModuleConfig = $config;
        $tmpModel->sModuleSpotName = $name;
        if (!is_null($templateLanguage) && property_exists($tmpModel, 'templateLanguage')) {
            $tmpModel->templateLanguage = $templateLanguage;
        }

        return $tmpModel;
    }

    /**
     * creates the requested class and returns it.
     *
     * @param string $name - class name
     *
     * @return TModelBase
     */
    protected function CreateModuleInstance($name)
    {
        if ($this->moduleResolver->hasModule($name)) {
            return $this->moduleResolver->getModule($name);
        }

        /** @var $module TModelBase */
        $module = new $name();
        $module->setController($this->controller);

        return $module;
    }

    /**
     * calls Init() method in all modules of the page.
     *
     * @param string|null $initSpotName
     */
    public function InitModules($initSpotName = null)
    {
        reset($this->modules);
        foreach ($this->modules as $spotName => $module) {
            if (null === $initSpotName || $initSpotName === $spotName) {
                $oldModulePointer = $this->global->GetExecutingModulePointer();
                if (false === is_object($this->modules[$spotName]) || false === method_exists($this->modules[$spotName], 'Init')) {
                    continue;
                }

                if (null === $oldModulePointer) {
                    $this->global->SetExecutingModulePointer($this->modules[$spotName]);
                }
                $this->modules[$spotName]->Init();
                if (null === $oldModulePointer) {
                    $tmp = null;
                    $this->global->SetExecutingModulePointer($tmp);
                }
            }
        }
        reset($this->modules);
    }

    /**
     * return array of all methods allowed for any of the included objects.
     *
     * @return array
     */
    public function GetPermittedFunctions()
    {
        reset($this->modules);
        $aFunctions = [];
        foreach ($this->modules as $spotName => $module) {
            $aTmpFunctions = $this->modules[$spotName]->methodCallAllowed;
            $aFunctions = array_merge($aFunctions, $aTmpFunctions);
        }
        reset($this->modules);

        return $aFunctions;
    }

    /**
     * return the head includes of all modules.
     *
     * @return array
     */
    public function GetHtmlHeadIncludes()
    {
        $aHeadData = [];
        reset($this->modules);
        foreach ($this->modules as $spotName => $module) {
            $aModulHeadData = $this->modules[$spotName]->GetHtmlHeadIncludes();
            $aHeadData = array_merge($aHeadData, $aModulHeadData);
        }
        reset($this->modules);

        return $aHeadData;
    }

    /**
     * return the footer includes of all modules.
     *
     * @return string[]
     */
    public function GetHtmlFooterIncludes()
    {
        reset($this->modules);
        $aFooterData = [];
        foreach ($this->modules as $spotName => $module) {
            $aModuleFooterData = $this->modules[$spotName]->GetHtmlFooterIncludes();
            $aFooterData = array_merge($aFooterData, $aModuleFooterData);
        }
        reset($this->modules);

        return array_unique($aFooterData);
    }

    /**
     * @param string $spotName
     *
     * @return bool
     */
    public function hasModule($spotName)
    {
        return array_key_exists($spotName, $this->modules);
    }

    /**
     * executes and renders the requested module.
     *
     * @param string $spotName - spot name to fetch
     * @param bool $bReturnString - normally the result is echoed. if set to true, the result will be returned as a string instead
     * @param string|null $sCustomWrapping - specify custom wrapping. the string [{content}] will be replaced by the module content
     *                                     note: the wrapping is only added if the module is not empty
     * @param bool $bAllowAutoWrap - set to false if RENDER_DIV_WITH_MODULE_AND_VIEW_NAME_ON_MODULE_LOAD is active and you want to suppress the auto div
     *
     * @return string|null
     *
     * @throws ModuleException
     */
    public function GetModule($spotName, $bReturnString = false, $sCustomWrapping = null, $bAllowAutoWrap = true)
    {
        if (!isset($this->modules[$spotName])) {
            $sContent = _DEVELOPMENT_MODE ? "<!-- ERROR: unable to find module [{$spotName}] -->" : '';
            if (true === $bReturnString) {
                return $sContent;
            }
            echo $sContent;

            return null;
        }

        $module = $this->modules[$spotName];
        $oOldModulePoiner = $this->global->GetExecutingModulePointer();
        $this->global->SetExecutingModulePointer($module);

        $request = $this->requestStack->getCurrentRequest();
        if (null === $request) {
            throw new ModuleExecutionFailedException('No current request available, but expected.');
        }
        $sContent = '';
        try {
            $response = $this->moduleExecutionStrategy->execute($request, $module, $spotName, $this->isLegacyModule($module));
            $sContent = $response->getContent();
        } catch (ModuleException $e) {
            if (_DEVELOPMENT_MODE) {
                throw $e;
            }

            $this->logModuleException($e, $spotName);
        } catch (Exception $e) {
            if (_DEVELOPMENT_MODE) {
                throw new ModuleExecutionFailedException(sprintf('Error in module execution: %s in file: %s on line: %d', $e->getMessage(), $e->getFile(), $e->getLine()), 0, $e);
            }

            $this->logModuleException($e, $spotName);
        }

        if (false === empty($sContent)) {
            if ($bAllowAutoWrap && (RENDER_DIV_WITH_MODULE_AND_VIEW_NAME_ON_MODULE_LOAD && (!TGlobal::IsCMSMode() || $this->requestInfoService->isCmsTemplateEngineEditMode()))) {
                if ('MTEmpty' !== get_class($module) && $module->IsHTMLDivWrappingAllowed()) {
                    $sViewName = substr($module->viewTemplate, strrpos($module->viewTemplate, '/') + 1);
                    $sViewName = substr($sViewName, 0, strpos($sViewName, '.'));
                    $sViewName = ucfirst($sViewName);
                    $sContent = '<div id="spot'.$spotName.'" class="templatespot Chameleon'.get_class($module).' Chameleon'.$sViewName.'">'.$sContent.'</div>';
                }
            }
            if (null !== $sCustomWrapping) {
                $sContent = str_replace('[{content}]', $sContent, $sCustomWrapping);
            }
        }

        $this->global->SetExecutingModulePointer($oOldModulePoiner);

        if (true === $bReturnString) {
            return $sContent;
        }

        echo $sContent;

        return null;
    }

    /**
     * @param string $spotName
     */
    private function logModuleException(Exception $e, $spotName)
    {
        $subject = 'Error-Notification '.$_SERVER['HTTP_HOST'].': '.$e->getMessage().' '.md5($e->getFile().$e->getLine().$_SERVER['REQUEST_URI']);
        $date = date('Y-m-d H:i:s');
        $backtrace = TTools::GetFormattedDebug($e->getTrace());

        $body = 'Request-URI: '.$_SERVER['REQUEST_URI']."\n";
        $body .= $date."\n";
        $body .= 'Code: '.$e->getCode()."\n";
        $body .= 'Message: '.$e->getMessage()."\n";
        $body .= 'Spot: '.$spotName."\n";
        $body .= 'Backtrace: '.$backtrace."\n";

        $logMessage = "@@\n".$subject."\n".$body.'END@@';

        /**
         * @var $logger LoggerInterface
         */
        $logger = ServiceLocator::get('logger');
        $logger->error($logMessage);
    }

    /**
     * Call a module function directly from the layout.
     *
     * @param string $sSpotName - spot (module) on which to call the function
     * @param string $sFunctionName - function to call
     */
    public function CallModuleFunction($sSpotName, $sFunctionName)
    {
        if (array_key_exists($sSpotName, $this->modules) && method_exists($this->modules[$sSpotName], $sFunctionName)) {
            echo $this->modules[$sSpotName]->$sFunctionName();
        } else {
            echo "<!-- ERROR: unable to find module [{$sSpotName}], or unable to call function [{$sFunctionName}] on it -->";
        }
    }

    /**
     * Call a public (ie from web-callable) function.
     *
     * @param string $sModuleSpotName - the spot name
     * @param string $sMethod - function to call
     */
    public function CallPublicModuleFunction($sModuleSpotName, $sMethod)
    {
        if (array_key_exists($sModuleSpotName, $this->modules)) { // module exists
            if (method_exists($this->modules[$sModuleSpotName], $sMethod)) { // method exists
                if (in_array($sMethod, $this->modules[$sModuleSpotName]->methodCallAllowed)) { // method is in array so it´s allowed to execute it
                    $this->modules[$sModuleSpotName]->$sMethod();
                }
            }
        }
    }

    /**
     * returns pointer to the module with name $modulename, or false
     * if the module does not exist.
     *
     * @param string $sModuleSpotName
     *
     * @return TModelBase|bool
     */
    public function GetPointerToModule($sModuleSpotName)
    {
        if (array_key_exists($sModuleSpotName, $this->modules)) {
            return $this->modules[$sModuleSpotName];
        }

        return false;
    }

    /**
     * return an array of all instances of modules of a given type
     * $type is the class name of the instances which to return (or parent of the class)
     * returns an empty array if no matching instance is found.
     */
    public function GetModulesOfType($type)
    {
        $pointerArray = [];
        foreach ($this->modules as $moduleKey => $module) {
            if (is_subclass_of($this->modules[$moduleKey], $type) || 0 == strcasecmp(get_class($this->modules[$moduleKey]), $type)) {
                $pointerArray[] = $this->modules[$moduleKey];
            }
        }

        return $pointerArray;
    }

    /**
     * @param ChameleonControllerInterface $controller
     */
    public function setController($controller)
    {
        $this->controller = $controller;
    }

    /**
     * @param TModelBase $module
     *
     * @return bool
     */
    protected function isLegacyModule($module)
    {
        return false === ($module instanceof MTPkgViewRendererAbstractModuleMapper);
    }
}
