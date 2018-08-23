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
use ChameleonSystem\CoreBundle\Service\LanguageServiceInterface;
use ChameleonSystem\CoreBundle\Service\PortalDomainServiceInterface;
use ChameleonSystem\CoreBundle\Util\InputFilterUtilInterface;
use ChameleonSystem\CoreBundle\Util\UrlPrefixGeneratorInterface;
use Doctrine\DBAL\Connection;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpKernel\KernelInterface;

/**
 * all global classes used in the framework (ie. for both the cms and the user
 * side) should inherit from this. this class provides the basic functionality
 * such as class factory, get/post filtering, etc.
 *
/**/
class TGlobalBase
{
    /**
     * holds the state of portal based class transformation.
     *
     * @var bool
     */
    protected static $bPortalBasedClassTransformState = true;

    /**
     * URL path to backend http resources.
     *
     * @var string
     */
    public static $PATH_TO_WEB_LIBRARY = 'chameleon/blackbox';

    /**
     * a copy of all rewrite parameter - these parameters will be excluded from the GetRealURL request (since they are part of the url anyway).
     *
     * @var array
     */
    protected $aRewriteParameter = array();

    /**
     * used to cache any data that may be needed globally
     * (like a list of portals, etc).
     *
     * @var array
     */
    public $_dataCache = array();

    public $aLangaugeIds = null;

    /**
     * holds the current executing module object.
     *
     * @var TModelBase
     */
    protected $oExecutingModuleObject = null;

    protected $aFileList = array();

    /**
     * config class of the HTMLPurifier XSS filter.
     *
     * @var HTMLPurifier_Config
     */
    public $oHTMLPurifyConfig = null;

    /**
     * an array holding mocked objects for unit testing.
     *
     * @var array
     *
     * @deprecated since 6.2.0 - no longer supported.
     */
    protected $aUnitTestMockedObjects = array();

    /** @var RequestStack */
    private $requestStack;
    /**
     * @var InputFilterUtilInterface
     */
    private $inputFilterUtil;
    /**
     * @var KernelInterface
     */
    private $kernel;

    public function __construct(RequestStack $requestStack, InputFilterUtilInterface $inputFilterUtil, KernelInterface $kernel)
    {
        $this->requestStack = $requestStack;
        $this->inputFilterUtil = $inputFilterUtil;
        $this->kernel = $kernel;
    }

    protected function getRequest()
    {
        return $this->requestStack->getCurrentRequest();
    }

    public function __get($sParameterName)
    {
        if ('userData' === $sParameterName) {
            $trace = debug_backtrace();
            trigger_error('userData is no longer available - use \ChameleonSystem\CoreBundle\ServiceLocator::get("request_stack")->getCurrentRequest() instead in '.$trace[1]['file'].' on line '.$trace[1]['line'], E_USER_ERROR);

            return null;
        } else {
            $trace = debug_backtrace();
            trigger_error('Undefined property via __get(): '.$sParameterName.' in '.$trace[0]['file'].' on line '.$trace[0]['line'], E_USER_NOTICE);

            return null;
        }
    }

    /**
     * needs to be overwritten in the child class. should return a pointer to
     * an instance of the child global class.
     *
     * @return TGlobalBase
     *
     * @deprecated Use \ChameleonSystem\CoreBundle\ServiceLocator::get('chameleon_system_core.global') instead
     */
    public static function instance()
    {
        return \ChameleonSystem\CoreBundle\ServiceLocator::get('chameleon_system_core.global');
    }

    /**
     * called by the controller to set the pointer to the currently executing module instance.
     *
     * @param TModelBase $oExecutingModuleObject
     */
    public function SetExecutingModulePointer(&$oExecutingModuleObject)
    {
        $this->oExecutingModuleObject = &$oExecutingModuleObject;
    }

    /**
     * return pointer to the currently executing module object.
     *
     * @return TModelBase
     */
    public function &GetExecutingModulePointer()
    {
        return $this->oExecutingModuleObject;
    }

    /**
     * return a pointer to the controller running the show.
     *
     * @return ChameleonControllerInterface
     *
     * @deprecated Use \ChameleonSystem\CoreBundle\ServiceLocator::get('chameleon_system_core.chameleon_controller') instead
     */
    public static function GetController()
    {
        return \ChameleonSystem\CoreBundle\ServiceLocator::get('chameleon_system_core.chameleon_controller');
    }

    /**
     * returns the path to the blackbox directory with mod_rewrite build number.
     *
     * @deprecated use GetStaticURLToWebLib instead of this function
     *
     * @return string
     */
    public static function GetPathWebLibrary()
    {
        return TGlobal::GetStaticURLToWebLib();
    }

    /**
     * returns the base URL of the current backend theme
     * uses GetStaticURL to return optional static domain.
     *
     * @return string
     */
    public static function GetPathTheme()
    {
        static $sThemePath;
        if (!$sThemePath) {
            $oConfig = TdbCmsConfig::GetInstance();
            $sThemePath = $oConfig->GetThemeURL(); // .'build'
            $sThemePath = self::GetStaticURL($sThemePath);
        }

        return $sThemePath;
    }

    /**
     * returns the static path to the given file in the blackbox directory.
     *
     * @param string $sFilePath    - the relative url of the file in relation to the blackbox directory
     * @param bool   $bForceNonSSL - used to force urls to non SSL
     *
     * @return string
     */
    public static function GetStaticURLToWebLib($sFilePath = '', $bForceNonSSL = false)
    {
        return self::GetStaticURL(self::$PATH_TO_WEB_LIBRARY.$sFilePath, $bForceNonSSL);
    }

    /**
     * @return string[]|string
     */
    public static function GetStaticURLPrefix()
    {
        static $aStaticURLPrefix = null;
        if (null === $aStaticURLPrefix) {
            if (strpos(URL_STATIC, ',')) {
                $aStaticURLPrefix = array();
                $aStaticURLs = explode(',', URL_STATIC);
                foreach ($aStaticURLs as $sKey => $sURL) {
                    $sURL = trim($sURL);
                    if (!empty($sURL)) {
                        $aStaticURLPrefix[] = $sURL;
                    }
                }
            } else {
                $aStaticURLPrefix = URL_STATIC;
            }
        }

        return $aStaticURLPrefix;
    }

    /**
     * @param string $sURL
     *
     * @return string
     */
    public static function ResolveStaticURL($sURL)
    {
        if ('[{CMSSTATICURL' != substr($sURL, 0, 14)) {
            return $sURL;
        }

        $aStaticURLPrefix = TGlobal::GetStaticURLPrefix();
        if (!is_array($aStaticURLPrefix)) {
            $aStaticURLPrefix = array($aStaticURLPrefix);
        }
        $sPrefix = substr($sURL, 0, strpos($sURL, '}]', 2) + 2);
        $sURL = substr($sURL, strlen($sPrefix));
        $sIndex = substr($sPrefix, 15, -2);
        if (isset($aStaticURLPrefix[$sIndex])) {
            $sURL = $aStaticURLPrefix[$sIndex].$sURL;
        } else {
            $sURL = $aStaticURLPrefix[0].$sURL;
        }

        return $sURL;
    }

    /**
     * returns the config constant URL_STATIC or "/" if SSL is active
     * overwrite this method if you use a Content Delivery Network and need different
     * URLs for different content types (images from CDN, JS library from code.google.com, CSS from local)
     * you can check the path or file type to solve this.
     *
     * @param string $sFilePath    - the relative url to get a static server
     * @param bool   $bForceNonSSL - used to force urls to non SSL
     *
     * @return string
     */
    public static function GetStaticURL($sFilePath = '', $bForceNonSSL = false)
    {
        static $iNumberOfStaticURLs = null;
        static $iCurrentStaticURL = 0;
        if (null === $iNumberOfStaticURLs) {
            $iNumberOfStaticURLs = 1;
            $aStaticURLs = TGlobal::GetStaticURLPrefix();
            if (is_array($aStaticURLs)) {
                $iNumberOfStaticURLs = count($aStaticURLs);
            }
        }

        $sStaticURLToUse = URL_STATIC;
        if ($iNumberOfStaticURLs > 1) {
            // map requested file path to one of the urls
            $sMap = substr(md5($sFilePath), 0, 4);
            // now map to a position in the array -> for this to work, convert to decimal
            $iMap = hexdec($sMap);
            $iCurrentStaticURL = $iMap % $iNumberOfStaticURLs;

            $sStaticURLToUse = '[{CMSSTATICURL_'.$iCurrentStaticURL.'}]';
        }

        if ('http' === substr($sFilePath, 0, 4)) {
            $aURLParts = parse_url($sFilePath);
            $sFilePath = $aURLParts['path'];
            if (isset($aURLParts['query'])) {
                $sFilePath .= '?'.$aURLParts['query'];
            }
            if (isset($aURLParts['fragment'])) {
                $sFilePath .= '#'.$aURLParts['fragment'];
            }
        }

        if ('/' === substr($sFilePath, 0, 1)) {
            $sFilePath = substr($sFilePath, 1);
        }

        if (true === $bForceNonSSL) {
            if (0 === strpos($sStaticURLToUse, 'https://')) {
                $sStaticURLToUse = 'http://'.substr($sStaticURLToUse, 8);
            }
        } elseif (0 === strpos($sStaticURLToUse, 'http://')) {
            $request = self::getCurrentRequest();

            if ($request->isSecure()) {
                $sStaticURLToUse = 'https://'.substr($sStaticURLToUse, 7);
            }
        }

        if ('/' !== substr($sStaticURLToUse, -1)) {
            $sStaticURLToUse .= '/';
        }
        $sStaticURLToUse .= $sFilePath;

        return $sStaticURLToUse;
    }

    /**
     * call it to find out if we are in the cms or on the webpage.
     *
     * @return bool
     */
    public static function IsCMSMode()
    {
        // if this function gets called in TGlobalBase, then we are viewing the webpage
        // however we MAY be viewing it through the cms template engine. so in that
        // case we still need to return false.
        // grab an instance of TGlobal to find out :)
        $oGlobal = TGlobal::instance();
        if ('true' == $oGlobal->GetUserData('__modulechooser') && self::CMSUserDefined()) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * returns true if the webpage is loaded in template engine edit mode
     * use this to disable frontend javascript for example.
     *
     * @deprecated - use RequestInfoServiceInterface::isCmsTemplateEngineEditMode() instead
     *
     * @return bool
     */
    public static function IsCMSTemplateEngineEditMode()
    {
        static $isCmsTemplateEngineEditMode = null;
        if (null !== $isCmsTemplateEngineEditMode) {
            return $isCmsTemplateEngineEditMode;
        }

        $oGlobal = TGlobal::instance();
        if (!TGlobal::IsCMSMode() && 'true' == $oGlobal->GetUserData('__modulechooser', array(), TCMSUserInput::FILTER_NONE)) {
            $isCmsTemplateEngineEditMode = true;
        } else {
            $isCmsTemplateEngineEditMode = false;
        }

        return $isCmsTemplateEngineEditMode;
    }

    public function GetLanguageIdList()
    {
        if (is_null($this->aLangaugeIds)) {
            $this->aLangaugeIds = array();
            $oCMSConfig = TdbCmsConfig::GetInstance();
            $this->aLangaugeIds[] = $oCMSConfig->fieldTranslationBaseLanguageId;
        }

        return $this->aLangaugeIds;
    }

    /**
     * return the current active language (language is loaded from page or user -depending on mode).
     *
     * @return string
     *
     * @deprecated since 6.0.0 - use \ChameleonSystem\CoreBundle\ServiceLocator::get('chameleon_system_core.language_service')->getActiveLanguageId() instead
     */
    public static function GetActiveLanguageId()
    {
        // the method must be called through TGlobal
        trigger_error('error: call this method through TGlobal::GetActiveLanguageId()');
    }

    /**
     * checks if active CMS user session is available.
     *
     * @return bool
     */
    public static function CMSUserDefined()
    {
        return TCMSUser::CMSUserDefined();
    }

    /**
     * escapes a string (htmlentities/ENT_QUOTES, "=", "\").
     *
     * @param string $nonEscapedString
     * @param bool   $bDoubleEncode
     *
     * @return string
     */
    public static function OutHTML($nonEscapedString, $bDoubleEncode = true)
    {
        if (null === $nonEscapedString) {
            return '';
        }
        $sEscapedHTML = htmlentities($nonEscapedString, ENT_QUOTES, 'UTF-8', $bDoubleEncode);

        if ('' === $sEscapedHTML && '' !== $nonEscapedString) {
            //there is an error converting an "non-utf8" string!
            $trace = debug_backtrace();
            trigger_error('OutHTML() failed to convert the text: '.$nonEscapedString.' in '.$trace[0]['file'].' on line '.$trace[0]['line'], E_USER_NOTICE);
            //$sEscapedHTML = self::TryConvertTextToUtf8($nonEscapedString);
        }

        $sEscapedHTML = str_replace('=', '&#61;', $sEscapedHTML);
        $sEscapedHTML = str_replace('\\', '&#92;', $sEscapedHTML);

        return $sEscapedHTML;
    }

    /**
     * try to convert string to utf8.
     *
     * @param string $sContent
     *
     * @return string
     */
    public static function TryConvertTextToUtf8($sContent)
    {
        if (!mb_check_encoding($sContent, 'UTF-8') || !($sContent === mb_convert_encoding(mb_convert_encoding($sContent, 'UTF-32', 'UTF-8'), 'UTF-8', 'UTF-32'))) {
            $sContent = mb_convert_encoding($sContent, 'UTF-8');
            if (mb_check_encoding($sContent, 'UTF-8')) {
                // Converted to UTF-8
            } else {
                // Could not converted to UTF-8
            }
        }

        return $sContent;
    }

    /**
     * escapes a string for javascript usage.
     *
     * @param string $nonEscapedString
     *
     * @return string
     */
    public static function OutJS($nonEscapedString)
    {
        return self::OutHTML(addslashes($nonEscapedString));
    }

    /**
     * returns true if a variable was found in joined $_GET/$_POST data.
     *
     * @param string $name
     *
     * @return bool
     *
     * @deprecated - use \ChameleonSystem\CoreBundle\ServiceLocator::get('request_stack')->getCurrentRequest() instead
     */
    public function UserDataExists($name = null)
    {
        $request = $this->getRequest();

        return null !== $request && null !== $request->get($name, null);
    }

    /**
     * returns the value of variable $name or if missing the whole array filtered by $excludeArray.
     *
     * @param string $name
     * @param array  $excludeArray
     * @param string $sFilterClass - form: classname;path;type|classname;path;type
     *
     * @return mixed - string or array
     *
     * @deprecated - use InputFilterUtilInterface::getFiltered*Input() instead
     */
    public function GetUserData($name = null, $excludeArray = array(), $sFilterClass = TCMSUSERINPUT_DEFAULTFILTER)
    {
        $outputData = '';
        $request = $this->getRequest();
        if (null !== $request) {
            if (null !== $name) { // get value for key
                $outputData = $this->inputFilterUtil->getFilteredInput($name, '', false, $sFilterClass);
            } else {
                $outputData = array();

                $aSource = $request->query->keys();
                foreach ($aSource as $key) {
                    if (false === is_array($excludeArray) || !in_array($key, $excludeArray)) {
                        $outputData[$key] = $this->inputFilterUtil->getFilteredInput($key, null, false, $sFilterClass);
                    }
                }

                $aSource = $request->request->keys();
                foreach ($aSource as $key) {
                    if (false === is_array($excludeArray) || !in_array($key, $excludeArray)) {
                        $outputData[$key] = $this->inputFilterUtil->getFilteredInput($key, null, false, $sFilterClass);
                    }
                }
            }
        }

        return $outputData;
    }

    /**
     * returns the raw (unfiltered) value of variable $name or if missing the whole array filtered by $excludeArray.
     *
     * @param string $name
     * @param array  $excludeArray
     *
     * @return mixed - string or array
     *
     * @deprecated - use \ChameleonSystem\CoreBundle\ServiceLocator::get('request_stack')->getCurrentRequest() instead
     */
    public function GetRawUserData($name = null, $excludeArray = array())
    {
        return $this->GetUserData($name, $excludeArray, TCMSUserInput::FILTER_NONE);
    }

    /**
     * Save variable to userData.
     *
     * @param string $sArrayKeyName
     * @param mixed  $Value
     *
     * @deprecated - use \ChameleonSystem\CoreBundle\ServiceLocator::get('request_stack')->getCurrentRequest() instead
     */
    public function SetUserData($sArrayKeyName, $Value)
    {
        $request = $this->getRequest();
        $request->query->set($sArrayKeyName, $Value);
    }

    /**
     * Sets purifier config if not already set.
     *
     * @return HTMLPurifier_Config
     */
    public function SetPurifierConfig()
    {
        if (is_null($this->oHTMLPurifyConfig)) {
            /** @var $config HTMLPurifier_Config */
            $config = HTMLPurifier_Config::createDefault();
            $config->set('HTML.TidyLevel', 'none');
            $config->set('Core.RemoveInvalidImg', false);
            $config->set('Core.AggressivelyFixLt', false);
            $config->set('Core.ConvertDocumentToFragment', false);
            $config->set('Cache.SerializerPath', PATH_CMS_CUSTOMER_DATA);
            $this->oHTMLPurifyConfig = $config;
        }

        return $this->oHTMLPurifyConfig;
    }

    /**
     * helper method to remove slashes from multidimensional arrays send via GET/POST.
     *
     * @param array $arrayObj
     */
    protected function _StripSlashesArray(&$arrayObj)
    {
        // strips slashes from multidimensional array
        if (is_array($arrayObj)) {
            foreach ($arrayObj as $key => $value) {
                if (is_array($value)) {
                    $this->_StripSlashesArray($arrayObj[$key]);
                } else {
                    $arrayObj[$key] = $arrayObj[$key] = stripslashes($arrayObj[$key]);
                }
            }
        }
    }

    /**
     * returns processed data for Form-Fields using htmlspecialchars.
     *
     * @param string $name - get/post key name
     *
     * @return string
     */
    public function OutputUserData($name = null)
    {
        return htmlspecialchars($this->GetUserData($name), ENT_QUOTES);
    }

    /**
     * returns the userdata (GET/POST) as hidden html input fields.
     *
     * @param array $excludeArray
     *
     * @return string
     *
     * @deprecated - use \ChameleonSystem\CoreBundle\ServiceLocator::get('request_stack')->getCurrentRequest() instead
     */
    public function OutputDataAsFormFields($excludeArray = array())
    {
        $returnValue = '';
        $aData = $this->GetUserData(null, $excludeArray);
        foreach ($aData as $key => $value) {
            if (is_array($value)) {
                foreach ($value as $subKey => $subValue) { // only 1-dimensional arrays are supported yet
                    $returnValue .= '<input type="hidden" name="'.$key."[{$subKey}]\" value=\"".$subValue."\">\n";
                }
            } else {
                $returnValue .= '<input type="hidden" name="'.$key.'" value="'.$value."\">\n";
            }
        }

        return $returnValue;
    }

    /**
     * returns all POST and GET parameters as url.
     *
     * @param array $excludeArray
     *
     * @return string
     *
     * @deprecated - use \ChameleonSystem\CoreBundle\ServiceLocator::get('request_stack')->getCurrentRequest() instead
     */
    public function OutputDataAsURL($excludeArray = array())
    {
        $aData = $this->GetUserData();
        if (is_array($aData)) {
            foreach ($excludeArray as $key) {
                if ('' === $key) {
                    continue;
                }
                if (isset($aData[$key])) {
                    unset($aData[$key]);
                    continue;
                }
                // if the key contains [ and ], then we need to regex
                $iOpen = strpos($key, '[');
                if (false !== $iOpen) {
                    $iClose = strpos($key, ']', $iOpen);
                    if (false !== $iClose) {
                        if (preg_match("/^(.*?)(\[.*\])+/", $key, $aMatch)) {
                            if (3 == count($aMatch)) {
                                $aArrayKeys = explode('][', substr($aMatch[2], 1, -1));
                                $sPathString = $aMatch[1].'-';
                                foreach ($aArrayKeys as $Value) {
                                    $sPathString .= $Value.'-';
                                }
                                $aData = TTools::DeleteArrayKeyByPath($aData, $sPathString);
                            }
                        }
                    }
                }
            }

            return TTools::GetArrayAsURL($aData);
        } else {
            return '';
        }
    }

    /**
     * create an instance of a db object.
     *
     * @param string $sClassName - name of the class
     * @deprecated: use GetNewInstance of the Tdb Object (or new $sClass() for lists)
     *
     * @return TCMSRecord
     */
    public static function NewDBObject($sClassName)
    {
        return new $sClassName();
    }

    /**
     * Load class definition.
     *
     * @param string $sClassName
     * @param bool   $bIsAutoloadCall       - autoload
     * @param bool   $bTriggerErrorOnNoLoad - triggers a user error if loading failed
     *
     * @deprecated
     *
     * @return bool
     */
    public static function LoadDBObjectClassDefinition($sClassName, $bIsAutoloadCall = false, $bTriggerErrorOnNoLoad = true)
    {
        return true;
    }

    /**
     * loads a class.
     *
     * @param string $sClass   - the class name
     * @param string $sSubType - the subdirectory below /classes/... in engine/blackbox or extensions/
     * @param string $sType    - Core, Custom or Customer
     * @param bool   $force    - deprecated, handled by autoloader
     * @deprecated: just use new
     *
     * @return object
     */
    public static function ClassFactory($sClass, $sSubType = '', $sType = 'Core', $force = false)
    {
        return false === $sClass ? null : new $sClass();
    }

    /**
     * loads a callback function where gcf_ means "global" (CoreBundle/private/library/functions...)
     * and ccf_ = custom (/extentions/library/functions...).
     *
     * @param string $name
     */
    public static function LoadCallbackFunction($name)
    {
        $prefix = substr($name, 0, 4);
        $callbackPath = null;
        $oldCallbackPath = _CMS_CORE.'/callback_functions';
        switch ($prefix) {
            case 'gcf_':
                $oldCallbackPath = _CMS_CORE.'/callback_functions';
                $callbackPath = PATH_LIBRARY.'/functions/callback_functions';

                break;
            case 'ccf_':
                $oldCallbackPath = PATH_CUSTOMER_FRAMEWORK.'/../callback_functions';
                $callbackPath = PATH_LIBRARY_CUSTOMER.'/functions/callback_functions';
                break;

            default:
                trigger_error('Error: Unknown callback function type ['.$name.'] - rename your function to ccf_yourFunction or gcf_yourFunction', E_USER_ERROR);
                break;
        }

        $fncPath = $callbackPath.'/'.$name.'.fun.php';
        if (!file_exists($fncPath)) {
            require_once TGlobal::ProtectedPath($oldCallbackPath.'/'.$name.'.fun.php', '.fun.php');
        } else {
            require_once TGlobal::ProtectedPath($fncPath, '.fun.php');
        }

        if (!function_exists($name)) {
            trigger_error('Error: Could not find callback function ['.$fncPath.']', E_USER_ERROR);
        }
    }

    /**
     * loads a class and transforms the class if it was overwritten via cms configuration.
     *
     * @param string $sClass         - the class name
     * @param string $sSubType       - the subdirectory below /classes/... in engine/blackbox or extensions/
     * @param string $sType          - Core, Custom or Customer
     * @param bool   $force          - overrides class transformation if true
     * @param bool   $bSuppressError - set to true if you want to suppress a load error - if this is set, we return false on error
     *
     * @deprecated - classes will be loaded by the auto loader. if you need to load a class by hand, use TGlobal::LoadClass
     *
     * @return object
     */
    public static function LoadClassDefinition($sClass, $sSubType = '', $sType = 'Core', $force = false, $bSuppressError = false)
    {
    }

    /**
     * @param bool $bReset
     * @param bool $bAdd
     *
     * @return int
     */
    public static function CountCalls($bReset = false, $bAdd = false)
    {
        static $iCount = 0;
        if ($bReset) {
            $iCount = 0;
        }
        if ($bAdd) {
            ++$iCount;
        }

        return $iCount;
    }

    /**
     * returns the root path to the classes directory.
     *
     * @param string $sSubType
     * @param string $sType
     *
     * @return string
     */
    public static function _GetClassRootPath($sSubType, $sType)
    {
        $rootPath = PATH_LIBRARY.'/classes';
        switch ($sType) {
            case 'Custom-Core':
                if ('core' == $sSubType) {
                    $rootPath = PATH_CORE_CUSTOM;
                } else {
                    $rootPath = PATH_LIBRARY_CUSTOM.'/classes';
                }
                break;
            case 'Customer':
                if ('core' == $sSubType) {
                    $rootPath = PATH_CORE_CUSTOMER;
                } else {
                    $rootPath = PATH_LIBRARY_CUSTOMER.'/classes';
                }
                break;
            case 'Framework':
                $rootPath = PATH_CUSTOMER_FRAMEWORK_MODULES;
                break;
            case 'CoreEngine':
                $rootPath = PATH_CORE;
                break;
            case 'CoreCmsModules':
                $rootPath = PATH_CORE_MODULES;
                break;
            case 'CustomCoreCmsModules':
                $rootPath = PATH_MODULES_CUSTOM;
                break;
            case 'CustomerCmsModules':
                $rootPath = PATH_MODULES_CUSTOMER;
                break;
            case 'WebModules':
                $rootPath = realpath(PATH_CORE.'/../web_modules/');
                break;
            case 'autoclasses':
                $rootPath = \ChameleonSystem\CoreBundle\ServiceLocator::getParameter('chameleon_system_autoclasses.cache_warmer.autoclasses_dir');
                break;
            default:
            case 'Core':
                if ('core' == $sSubType) {
                    $rootPath = PATH_CORE;
                } else {
                    if (file_exists(PATH_LIBRARY.'/classes/'.$sSubType)) {
                        $rootPath = PATH_LIBRARY.'/classes';
                    } else {
                        $rootPath = ESONO_PACKAGES;
                    }
                }
                break;
        }

        return $rootPath;
    }

    /**
     * @param string $sType - core, custom-core, customer or package name
     *
     * @return string
     *
     * @deprecated since 6.2.0 - use getModuleRootPath() instead.
     */
    public static function _GetModuleRootPath($sType)
    {
        return self::instance()->getModuleRootPath($sType);
    }

    /**
     * @param string $type "core", "custom-core", "customer", a Chameleon package name or a bundle name
     *
     * @return string
     */
    public function getModuleRootPath($type)
    {
        $bundlePath = self::instance()->resolveBundlePath($type);
        if (null !== $bundlePath) {
            return $bundlePath.'/objects/BackendModules/';
        }

        $type = strtolower($type);

        switch ($type) {
            case 'core':
                $rootPath = PATH_MODULES;
                break;
            case 'custom-core':
                $rootPath = PATH_MODULES_CUSTOM;
                break;
            case 'customer':
                $rootPath = PATH_MODULES_CUSTOMER;
                break;
            default:
                $rootPath = ESONO_PACKAGES.'/'.$type.'/objects/BackendModules/';
                break;
        }

        return $rootPath;
    }

    /**
     * returns the path to page layout definition files based on "_pagedefType" URL parameter.
     *
     *
     * @param string $sType - Core, Custom-Core, Customer
     *
     * @return string
     */
    public function _GetPagedefRootPath($sType)
    {
        $bundlePath = $this->resolveBundlePath($sType);
        if (null !== $bundlePath) {
            return $bundlePath.'/Resources/BackendPageDefs/';
        }

        $sType = strtolower($sType);

        switch ($sType) {
            case 'core':
            case '':
                $rootPath = PATH_PAGE_DEFINITIONS;
                break;
            case 'custom-core':
                $rootPath = PATH_PAGE_DEFINITIONS_CUSTOM;
                break;
            case 'customer':
                $rootPath = PATH_PAGE_DEFINITIONS_CUSTOMER;
                break;
            default:
                $rootPath = ESONO_PACKAGES.'/'.$sType.'/Resources/BackendPageDefs/';
                break;
        }

        return $rootPath;
    }

    /**
     * @param string $path
     *
     * @return null|string
     */
    private function resolveBundlePath($path)
    {
        if ('' === $path || '@' !== $path[0]) {
            return null;
        }

        try {
            return $this->kernel->locateResource($path);
        } catch (InvalidArgumentException $e) {
            return null;
        } catch (RuntimeException $e) {
            return null;
        }
    }

    /**
     * returns the path to the layouts.
     *
     * @param string $sType
     *
     * @return string
     */
    public static function _GetLayoutRootPath($sType)
    {
        $rootPath = PATH_LAYOUTTEMPLATES;
        switch ($sType) {
            case 'Core':
                $rootPath = PATH_LAYOUTTEMPLATES;
                break;
            case 'Custom-Core':
                $rootPath = PATH_LAYOUTTEMPLATES_CUSTOM;
                break;
            case 'Customer':
                $rootPath = PATH_LAYOUTTEMPLATES_CUSTOMER;
                break;
            default:
                break;
        }

        if (!TGlobal::IsCMSMode()) {
            // overwrite path with layout path if we find a portal for the current page and a theme is set
            $activePortal = self::getPortalDomainService()->getActivePortal();
            if (null !== $activePortal) {
                $sThemePath = $activePortal->GetThemeLayoutPath();
                if (!empty($sThemePath)) {
                    $rootPath = $sThemePath;
                }
            }
        }

        return $rootPath;
    }

    /**
     * checks if a field exists in a table.
     *
     * @deprecated - please use TTools::FieldExists($sTableName,$sFieldName);
     *
     * @param string $sTableName
     * @param string $sFieldName
     *
     * @return bool
     */
    public static function FieldExists($sTableName, $sFieldName)
    {
        return TTools::FieldExists($sTableName, $sFieldName);
    }

    /**
     * checks if a table exists.
     *
     * @param string $sTableName
     *
     * @return bool
     */
    public static function TableExists($sTableName)
    {
        $databaseConnection = self::getDatabaseConnection();
        $databaseName = \ChameleonSystem\CoreBundle\ServiceLocator::getParameter('database_name');
        $quotedDatabaseName = $databaseConnection->quoteIdentifier($databaseName);
        $query = "SHOW TABLES FROM $quotedDatabaseName LIKE :tableName";
        $tRes = $databaseConnection->executeQuery($query, array('tableName' => $sTableName));

        return $tRes->rowCount() > 0;
    }

    /**
     * returns a RecordList of portals.
     *
     * @return TCMSPortalList
     */
    public function &GetPortals()
    {
        if (!array_key_exists('oPortals', $this->_dataCache)) {
            $this->_dataCache['oPortals'] = new TCMSPortalList();
        }

        return $this->_dataCache['oPortals'];
    }

    /**
     * write a debug log.
     *
     * @deprecated use TTools::WriteLogEntrySimple() instead!
     *
     * @param string $text
     */
    public static function WriteLog($text)
    {
        TTools::WriteLogEntrySimple($text, 2, __FILE__, __LINE__);
    }

    /**
     * returns the language prefix for the current page
     * returns empty string if current language is base language.
     *
     * @param string $sLanguageId - if null, will return prefix for active language
     *
     * @return string - empty string if current language is base language
     */
    public static function GetLanguagePrefix($sLanguageId = null)
    {
        static $sBaseLanguageId = null;
        if (null === $sBaseLanguageId) {
            $oCmsConfig = &TdbCmsConfig::GetInstance();
            $sBaseLanguageId = $oCmsConfig->fieldTranslationBaseLanguageId;
        }
        $languageService = self::getLanguageService();
        if (null === $sLanguageId) {
            $sLanguageId = $languageService->getActiveLanguageId();
        }

        if ($sLanguageId === $sBaseLanguageId) {
            return '';
        }

        return $languageService->getLanguageIsoCode($sLanguageId);
    }

    /**
     * add a file to the php file cache. returns the cache if no file is passed.
     *
     * @param string $sClassName
     * @param string $sFile
     * @param bool
     *
     * @return array
     *
     * @deprecated file cache isn't supported anymore - rely on the PHP OpCode cache
     */
    public static function AddFileToPHPFileCache($sClassName = null, $sFile = null, $isAutoLoadBlock = false)
    {
    }

    /**
     * set the rewrite parameter array - note: this will be called by the rewrite manager - so you never need
     * to call this directly yourself.
     *
     * @param array $aParameter
     */
    public function SetRewriteParameter($aParameter)
    {
        $this->aRewriteParameter = $aParameter;
    }

    // -----------------------------------------------------------------------

    /**
     * return the rewrite parameter.
     *
     * @return array
     */
    public function GetRewriteParameter()
    {
        return $this->aRewriteParameter;
    }

    /**
     * set record class load bit - used for file caching.
     *
     * @param bool $bState
     *
     * @return bool
     */
    public static function RecordFileLoads($bState = null)
    {
        static $bRecord = false;
        if (!is_null($bState)) {
            $bRecord = $bState;
        }

        return $bRecord;
    }

    public static function SortClassList($a, $b)
    {
        if ('IDNConvert' == $a) {
            $a = 'idna_convert';
        } elseif ('IDNConvert' == $b) {
            $b = 'idna_convert';
        }
        if (is_subclass_of($a, $b)) {
            return 1;
        } elseif (is_subclass_of($b, $a)) {
            return -1;
        } else {
            return strcasecmp($a, $b);
        }
    }

    /**
     * replaces custom var or cms text blocks in the text
     * These variables in the text must have the following format: [{name:format}]
     * "format" ist either string, date, or number. It is possible to specify the number of decimals
     * used when formating a number: [{variable:number:decimalplaces}]
     * example [{costs:number:2}].
     *
     * @param string $sString
     * @param array  $aCustomVariables
     * @param bool   $bPassVarsThroughOutHTML - set to true, if you want to pass the vars through TGlobal::OutHTML
     * @param $iWidth bool|int - max image width, default = false, used in pkgCmsTextBlock package
     *
     * @return string
     *
     * @deprecated - use TPkgCmsStringUtilities_VariableInjection instead
     */
    public function ReplaceCustomVariablesInString($sString, $aCustomVariables, $bPassVarsThroughOutHTML = false, $iWidth = false)
    {
        $oStringReplace = new TPkgCmsStringUtilities_VariableInjection();

        return $oStringReplace->replace($sString, $aCustomVariables, $bPassVarsThroughOutHTML, $iWidth);
    }

    /**
     * return instance of TCMSMemcache object that holds the memcache object internal.
     *
     * @return TCMSMemcache
     *
     * @deprecated inject chameleon_system_cms_cache.memcache_cache instead
     */
    public static function &GetMemcacheInstance()
    {
        $instance = \ChameleonSystem\CoreBundle\ServiceLocator::get('chameleon_system_cms_cache.memcache_cache');

        return $instance;
    }

    /**
     * returns the parameter wrapped in the executing module pointer spot name
     * - note: to retrive a parameter within the module, use the method GetUserInput(...).
     *
     * @param string $sParameterName
     *
     * @return string
     */
    public static function GetModuleURLParameter($sParameterName)
    {
        $sWrapper = 'UnknownSpot';
        $oGlobal = TGlobal::instance();
        $oModulePointer = $oGlobal->GetExecutingModulePointer();
        if ($oModulePointer) {
            $sWrapper = $oModulePointer->sModuleSpotName;
        }

        return TGlobal::OutHTML($sWrapper).'['.TGlobal::OutHTML($sParameterName).']';
    }

    /**
     * fallback for renamed/deprecated methods.
     *
     * @param string $name      - name of the method case sensitive
     * @param array  $arguments
     */
    public function __call($name, $arguments)
    {
        $aBackwardsCompatMethods = array();
        $aBackwardsCompatMethods['GetuserData'] = 'GetUserData';
        $aBackwardsCompatMethods['userDataExists'] = 'UserDataExists';

        if (array_key_exists($name, $aBackwardsCompatMethods)) {
            $sNewMethodName = $aBackwardsCompatMethods[$name];
            $oGlobal = TGlobal::instance();
            $oGlobal->$sNewMethodName(implode(', ', $arguments));
            // we should trigger notices if in development mode
        }
    }

    /** get the Prefix for the active language. If active language is default language function returns empty string
     *
     * @param string $sAddString (added before language prefix if not default language)
     *
     * @return string $sActLanguagPrefix
     *
     * @deprecated use chameleon_system_core.util.url_prefix_generator::getLanguagePrefix() instead
     */
    public function GetActiveLanguagePrefix($sAddString = '')
    {
        $activePortal = self::getPortalDomainService()->getActivePortal();
        $activeLanguage = self::getLanguageService()->getActiveLanguage();
        $prefix = $this->getUrlPrefixGenerator()->getLanguagePrefix($activePortal, $activeLanguage);
        if (!empty($prefix)) {
            return $sAddString.$prefix;
        }

        return $prefix;
    }

    /**
     * protect a path (makes sure, that the path is NOT above the safe path
     * this is much like the open_basedir restriction - except that this is intended only for files
     * within the project.
     *
     * @param string $sPath
     * @param string $sAllowedExtension
     *
     * @return string
     */
    public static function ProtectedPath($sPath, $sAllowedExtension = '.php')
    {
        static $aDirList = null;
        if (is_null($aDirList)) {
            $aDirList = explode(';', _CMS_BASE_DIR_RESTRICTION);
            foreach ($aDirList as $sDirKey => $sDir) {
                $aDirList[$sDirKey] = realpath($sDir).DIRECTORY_SEPARATOR;
            }
        }
        $sErrorMessage = false;
        // first make sure the path contains null byte or other
        $sTmpPath = filter_var($sPath, FILTER_SANITIZE_STRING, FILTER_FLAG_NO_ENCODE_QUOTES | FILTER_FLAG_STRIP_LOW | FILTER_FLAG_STRIP_HIGH);

        $sTmpPath = realpath($sTmpPath);
        // the file should now be safe..
        if (empty($sTmpPath)) {
            $sErrorMessage = 'File not found ['.$sPath.']';
        } else {
            if (0 != strcmp(substr($sTmpPath, -strlen($sAllowedExtension)), $sAllowedExtension)) {
                // invalid extension...
                $sErrorMessage = 'File unsafe ['.$sPath.'] expecting extension: '.$sAllowedExtension;
                $sTmpPath = '';
            }
        }
        if (!empty($sTmpPath)) {
            // make sure the path is below the dirs specified in aDirList
            $bIsBelow = false;
            reset($aDirList);
            foreach ($aDirList as $sDir) {
                if (0 == strcmp(substr($sTmpPath, 0, strlen($sDir)), $sDir)) {
                    $bIsBelow = true;
                }
            }
            if (!$bIsBelow) {
                // unsafe... log
                $sErrorMessage = 'File unsafe ['.$sPath.'] allowed: '.implode(';', $aDirList).' - defined via _CMS_BASE_DIR_RESTRICTION';
                $sTmpPath = '';
            }
        }
        if (false !== $sErrorMessage) {
            if (!class_exists('TTools', false)) {
                // error occurred BEFORE the tools class was loaded... display the error
                trigger_error($sErrorMessage, E_USER_WARNING);
            } else {
                TTools::WriteLogEntry($sErrorMessage, 1, __FILE__, __LINE__);
            }
        }

        return $sTmpPath;
    }

    /**
     * get an instance of an object in unit test mode - so you can get specially
     * prepared instances for singletons like the user object
     * you have to register the object via $oGlobal->RegisterUnitTestMockedObject().
     *
     * @param string $sClassName - name of the class
     *
     * @return TCMSRecord
     *
     * @deprecated since 6.2.0 - no longer supported.
     */
    public function GetUnitTestMockedObject($sClassName)
    {
        return new $sClassName();
    }

    /**
     * register a mocked object in unit test mode - so you can get specially
     * prepared instances for singletons like the user object
     * via $oGlobal->GetUnitTestMockedObject().
     *
     * @param string $sClassName - name of the class
     * @param object $oObject    - the mocked object to use
     *
     * @deprecated since 6.2.0 - no longer supported.
     */
    public function RegisterUnitTestMockedObject($sClassName, $oObject)
    {
    }

    /**
     * delete a mocked object in unit test mode that has been registered
     * via $oGlobal->RegisterUnitTestMockedObject().
     *
     * @param string $sClassName - name of the class
     *
     * @deprecated since 6.2.0 - no longer supported.
     */
    public function DeleteUnitTestMockedObject($sClassName)
    {
    }

    public function isFrontendJSDisabled()
    {
        $bExclude = ($this->UserDataExists('esdisablefrontendjs') && 'true' == $this->GetUserData('esdisablefrontendjs'));

        return $bExclude;
    }

    public function __sleep()
    {
        // avoid $requestStack being serialized
        return array();
    }

    /**
     * @return PortalDomainServiceInterface
     */
    private static function getPortalDomainService()
    {
        return \ChameleonSystem\CoreBundle\ServiceLocator::get('chameleon_system_core.portal_domain_service');
    }

    /**
     * @return LanguageServiceInterface
     */
    private static function getLanguageService()
    {
        return \ChameleonSystem\CoreBundle\ServiceLocator::get('chameleon_system_core.language_service');
    }

    /**
     * @return UrlPrefixGeneratorInterface
     */
    private function getUrlPrefixGenerator()
    {
        return \ChameleonSystem\CoreBundle\ServiceLocator::get('chameleon_system_core.util.url_prefix_generator');
    }

    /**
     * @return null|Request
     */
    private static function getCurrentRequest()
    {
        return \ChameleonSystem\CoreBundle\ServiceLocator::get('request_stack')->getCurrentRequest();
    }

    /**
     * @return Connection
     */
    private static function getDatabaseConnection()
    {
        return \ChameleonSystem\CoreBundle\ServiceLocator::get('database_connection');
    }
}
