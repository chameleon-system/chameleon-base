<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use ChameleonSystem\CoreBundle\ServiceLocator;
use esono\pkgCmsCache\CacheInterface;

class TCMSConfig extends TCMSRecord
{
    /**
     * array of module extensions.
     *
     * @var \TdbCmsConfigCmsmoduleExtensions[]
     */
    private $aCMSModuleExtensions = array();

    /**
     * initialized imageMagick object.
     *
     * @var imageMagick
     */
    protected $oImageMagick = null;

    private $aConfigValues = null;

    public function __construct()
    {
        parent::__construct('cms_config');
    }

    /**
     * @deprecated Named constructors are deprecated and will be removed with PHP8. When calling from a parent, please use `parent::__construct` instead.
     * @see self::__construct
     */
    public function TCMSConfig()
    {
        $this->callConstructorAndLogDeprecation(func_get_args());
    }

    /**
     * @param string $moduleName
     *
     * @return TdbCmsConfigCmsmoduleExtensions
     */
    protected function getCmsModuleExtension($moduleName)
    {
        if (null === $this->aCMSModuleExtensions) {
            $this->aCMSModuleExtensions = array();
            $moduleExtensions = TdbCmsConfigCmsmoduleExtensionsList::GetListForCmsConfigId($this->id);
            while ($moduleExtension = $moduleExtensions->Next()) {
                $this->aCMSModuleExtensions[$moduleExtension->fieldName] = $moduleExtension;
            }
        }

        if (false === isset($this->aCMSModuleExtensions[$moduleName])) {
            return null;
        }

        return $this->aCMSModuleExtensions[$moduleName];
    }

    /**
     * {@inheritdoc}
     */
    protected function GetCacheRelatedTables($id)
    {
        $aCacheRelatedTables = parent::GetCacheRelatedTables($id);
        $aCacheRelatedTables[] = array('table' => 'cms_config_cmsmodule_extensions', 'id' => '');

        return $aCacheRelatedTables;
    }

    /**
     * return true if delete requests should be logged.
     *
     * @return bool
     */
    public function LogDeletes()
    {
        $bLogDeletes = ('1' == $this->sqlData['log_deletes']);
        if ($bLogDeletes && '0' == $this->sqlData['log_www_user_delete_calls']) {
            /** @var $oCMSUser TdbCmsUser */
            $oCMSUser = &TdbCmsUser::GetActiveUser();
            $bLogDeletes = ('www' != $oCMSUser->sqlData['login']);
        }

        return $bLogDeletes;
    }

    /**
     * if the requested module was overwritten (entry in cms_config property table),
     * then return the new class, else return false.
     *
     * @param string $sModuleClassName
     *
     * @return string|bool
     */
    public function GetRealModuleClassName($sModuleClassName)
    {
        $extension = $this->getCmsModuleExtension($sModuleClassName);
        if (null === $extension) {
            return false;
        }

        return $extension->fieldNewclass;
    }

    /**
     * if the requested module was overwritten return the type (Core/Customer/Custom-Core),
     * else return "Customer".
     *
     * @param string $sModuleClassName
     *
     * @return string
     */
    public function GetModuleExtensionType($sModuleClassName)
    {
        $extension = $this->getCmsModuleExtension($sModuleClassName);
        if (null === $extension) {
            return 'Customer';
        }

        return $extension->fieldType;
    }

    /**
     * returns URL to the active theme dir.
     *
     * @return string
     */
    public function GetThemeURL()
    {
        static $sURL;
        if (!$sURL) {
            if (array_key_exists('cms_config_themes_id', $this->sqlData)) {
                $oTheme = $this->GetLookup('cms_config_themes_id');
                if ('Core' == $oTheme->sqlData['type']) {
                    $sURL = URL_CMS.'/themes/';
                } else {
                    $sURL = URL_USER_CMS_PUBLIC.'/themes/';
                }
                $sURL .= $oTheme->sqlData['directory'];
            } else {
                $sURL = TGlobal::GetStaticURLToWebLib();
            }
        }

        return $sURL;
    }

    /**
     * fetch instance of object.
     *
     * @param bool $bReload - set to true if you want to force a reload
     *
     * @return TdbCmsConfig
     */
    public static function &GetInstance($bReload = false)
    {
        static $instance = null;
        if ('TCMSConfig' === get_called_class()) {
            return TdbCmsConfig::GetInstance($bReload);
        }
        if (null !== $instance && false === $bReload) {
            return $instance;
        }

        $cache = self::getCache();
        $cacheKey = $cache->getKey([
            'method' => __METHOD__,
        ]);
        if (false === $bReload) {
            $instance = $cache->get($cacheKey);

            if (null !== $instance) {
                return $instance;
            }
        }

        $instance = TdbCmsConfig::GetNewInstance('1');
        $cache->set($cacheKey, $instance, [
            ['table' => 'cms_config', 'id' => '1'],
        ]);

        return $instance;
    }

    /**
     * returns the customer server url. this is either the defined set in the config
     * or the portal connected to the current page. the portal page url is only returned
     * if a flag in the config allows using the portal url.
     *
     * @return string
     */
    public function GetCustomerServerURL($pageId = null)
    {
        $customerServerURL = _CUSTOMER_SERVER_URL;

        if (!is_null($pageId) && defined('_SET_CUSTOMER_SERVER_URL_USING_PORTAL') && _SET_CUSTOMER_SERVER_URL_USING_PORTAL == true) {
            $oPage = TCMSPage::GetPageObject($pageId);
            if (!empty($oPage->pageData['cms_portal_id'])) {
                $field = 'productive_url';
                if (_DEVELOPMENT_MODE) {
                    $field = 'development_url';
                }
                $query = "SELECT `{$field}` AS url FROM `cms_portal` WHERE `id` = '".MySqlLegacySupport::getInstance()->real_escape_string($oPage->pageData['cms_portal_id'])."'";
                if ($portal = MySqlLegacySupport::getInstance()->fetch_assoc(MySqlLegacySupport::getInstance()->query($query))) {
                    if (!empty($portal['url'])) {
                        $customerServerURL = $portal['url'];
                    }
                }
            }
        }

        return $customerServerURL;
    }

    /**
     * returns the dbobject of the primary portal.
     *
     * @return TCMSPortal
     */
    public function &GetPrimaryPortal()
    {
        $oPortal = &$this->GetFromInternalCache('_primaryPortal');
        if (is_null($oPortal)) {
            $oPortal = new TCMSPortal();
            $oPortal->Load($this->sqlData['cms_portal_id']);
            $this->SetInternalCache('_primaryPortal', $oPortal);
        }

        return $oPortal;
    }

    /**
     * returns true if CMS module exists and is activated.
     *
     * @return bool
     */
    public static function IsCMSModuleActive($moduleIdentifier)
    {
        $returnVal = false;
        $query = "SELECT * FROM `cms_module` WHERE `module` = '".MySqlLegacySupport::getInstance()->real_escape_string($moduleIdentifier)."' AND `active` = '1'";
        $result = MySqlLegacySupport::getInstance()->query($query);
        if (1 == MySqlLegacySupport::getInstance()->num_rows($result)) {
            $returnVal = true;
        }

        return $returnVal;
    }

    /**
     * return config parameter.
     *
     * @param string $sSystemName
     * @param bool   $bRefresh         forces refresh of static config array
     * @param bool   $bSuppressWarning prevents triggering an error if the key does not exist
     *
     * @return string|null
     */
    public function GetConfigParameter($sSystemName, $bRefresh = false, $bSuppressWarning = false)
    {
        $this->fillConfigValuesCache();

        if ($bRefresh) {
            $query = "SELECT * FROM `cms_config_parameter`
                   WHERE `systemname` = '".MySqlLegacySupport::getInstance()->real_escape_string($sSystemName)."'
                     AND `cms_config_id` = '".MySqlLegacySupport::getInstance()->real_escape_string($this->id)."'";
            if ($aVal = MySqlLegacySupport::getInstance()->fetch_assoc(MySqlLegacySupport::getInstance()->query($query))) {
                $this->aConfigValues[$sSystemName] = $aVal['value'];
            }
        }
        if (!array_key_exists($sSystemName, $this->aConfigValues)) {
            if (!$bSuppressWarning) {
                trigger_error('unable to find system config parameter ['.$sSystemName.'] in TCMSConfig::GetConfigParameter', E_USER_WARNING);
            }

            return null;
        } else {
            return $this->aConfigValues[$sSystemName];
        }
    }

    public function removeConfigParameter($systemName)
    {
        if (empty($systemName)) {
            throw new TCMSConfigException('Tried to delete a config value with an empty system name. All hell could break lose, therefore deal with this exception instead.');
        }
        if (null === $this->GetConfigParameter($systemName)) {
            throw new TCMSConfigException("Tried to delete a non-existent config parameter with the systemname '".$systemName."'");
        }
        $query = "DELETE FROM `cms_config_parameter` WHERE `systemname` = BINARY '".MySqlLegacySupport::getInstance()->real_escape_string($systemName)."';";
        MySqlLegacySupport::getInstance()->query($query);
        unset($this->aConfigValues[$systemName]);
    }

    public function getAllConfigValues()
    {
        $this->fillConfigValuesCache();

        return $this->aConfigValues;
    }

    /**
     * write value into config. NOTE: this value is NOT written using the objects to prevent circulare saves.
     *
     * @param string $sSystemName
     * @param string $sValue
     * @param bool   $addIfNotExists
     */
    public function SetConfigParameter($sSystemName, $sValue, $addIfNotExists = false)
    {
        $added = false;
        if ($addIfNotExists) {
            $currentValue = $this->GetConfigParameter($sSystemName, false, true);
            if (null === $currentValue) {
                $query = "INSERT INTO `cms_config_parameter`
                   SET
                       `id` = '".TTools::GetUUID()."',
                       `value` = '".MySqlLegacySupport::getInstance()->real_escape_string($sValue)."',
                       `systemname` = '".MySqlLegacySupport::getInstance()->real_escape_string($sSystemName)."',
                       `cms_config_id` = '".MySqlLegacySupport::getInstance()->real_escape_string($this->id)."'";
                MySqlLegacySupport::getInstance()->query($query);
                $added = true;
            }
        }
        if (false === $added) {
            $query = "UPDATE `cms_config_parameter`
                           SET `value` = '".MySqlLegacySupport::getInstance()->real_escape_string($sValue)."'
                         WHERE `systemname` = '".MySqlLegacySupport::getInstance()->real_escape_string($sSystemName)."'
                           AND `cms_config_id` = '".MySqlLegacySupport::getInstance()->real_escape_string($this->id)."'";
            MySqlLegacySupport::getInstance()->query($query);
        }

        $this->GetConfigParameter($sSystemName, true);
    }

    /**
     * init imageMagick class and determine if it is installed and which version.
     *
     * @return string|bool - returns false or the version in format: 6.5.4
     */
    public function GetImageMagickVersion()
    {
        $sImageMagickVersion = &$this->GetFromInternalCache('_imageMagickVersion');
        if (is_null($sImageMagickVersion)) {
            $sImageMagickVersion = false;
            $oImageMagick = new imageMagick();
            $oImageMagick->setFileManager(ServiceLocator::get('chameleon_system_core.filemanager'));
            $oImageMagick->Init();
            $sImageMagickVersion = $oImageMagick->GetImageMagickVersion();
            $this->oImageMagick = $oImageMagick;

            $this->SetInternalCache('_imageMagickVersion', $sImageMagickVersion);
        }

        return $sImageMagickVersion;
    }

    /**
     * returns an initialised object of imageMagick
     * TCMSConfig->GetImageMagickVersion(); needs to be called first!
     *
     * @return imageMagick
     */
    public function GetImageMagickObject()
    {
        $this->GetImageMagickVersion();

        return $this->oImageMagick;
    }

    /**
     * overwrite the __get method to return false when requesting property
     * fieldEnableCompileCache before the auto data object was updated to contain this field.
     *
     * @param string $sPropertyName
     *
     * @return bool
     */
    public function __get($sPropertyName)
    {
        if ('fieldEnableCompileCache' == $sPropertyName) {
            return false;
        }
        $trace = debug_backtrace();
        trigger_error('Undefined property via __get(): '.$sPropertyName.' in '.$trace[0]['file'].' on line '.$trace[0]['line'], E_USER_NOTICE);

        return null;
    }

    /**
     * return an array holding the languages supported in the front end for a field based translation.
     *
     * @return array
     */
    public function GetFieldBasedTranslationLanguageArray()
    {
        $aLanguages = $this->GetFromInternalCache('aFieldBasedTranslationLanguageArray');
        if (is_null($aLanguages)) {
            $aLanguages = array();
            $oLanguages = $this->GetMLT('cms_language_mlt', 'TdbCmsLanguage', '', 'CMSDataObjects', 'Core');
            while ($oLang = $oLanguages->Next()) {
                if ($oLang->id != $this->sqlData['translation_base_language_id']) {
                    $aLanguages[$oLang->fieldIso6391] = $oLang->fieldName;
                }
            }
            $this->SetInternalCache('aFieldBasedTranslationLanguageArray', $aLanguages);
        }

        return $aLanguages;
    }

    /**
     * return an array of translatable fields as an array like
     * (table=>array(field1,field2..)
     * if a table name is passed, then the method returns only the fields for the table in question.
     *
     * @return array
     */
    public function GetListOfTranslatableFields($sTable = null)
    {
        $aTranslatableFields = $this->GetFromInternalCache('aListOfTranslatbleFields');
        if (is_null($aTranslatableFields)) {
            $aCacheKey = array('class' => 'TCMSConfig', 'method' => 'GetListOfTranslatbleFields', 'comment' => 'listOfTranslatableFields');
            $cache = \ChameleonSystem\CoreBundle\ServiceLocator::get('chameleon_system_core.cache');
            $sKey = $cache->getKey($aCacheKey, false);
            $aTranslatableFields = $cache->get($sKey);
            if (null === $aTranslatableFields) {
                $aTranslatableFields = array();
                $sQuery = "SELECT `cms_field_conf`.`name`, `cms_tbl_conf`.`name` AS _tableName
                       FROM `cms_field_conf`
                 INNER JOIN `cms_tbl_conf` ON `cms_field_conf`.`cms_tbl_conf_id` = `cms_tbl_conf`.`id`
                      WHERE `cms_field_conf`.`is_translatable` = '1'
                    ";
                $tRes = MySqlLegacySupport::getInstance()->query($sQuery);
                while ($aRow = MySqlLegacySupport::getInstance()->fetch_assoc($tRes)) {
                    if (!array_key_exists($aRow['_tableName'], $aTranslatableFields)) {
                        $aTranslatableFields[$aRow['_tableName']] = array();
                    }
                    $aTranslatableFields[$aRow['_tableName']][] = $aRow['name'];
                }
                $aCacheTrigger = array();
                $aCacheTrigger[] = array('table' => 'cms_tbl_conf', 'id' => '');
                $aCacheTrigger[] = array('table' => 'cms_field_conf', 'id' => '');
                $cache->set($sKey, $aTranslatableFields, $aCacheTrigger);
            }
            $this->SetInternalCache('aListOfTranslatbleFields', $aTranslatableFields);
        }
        if (is_null($sTable)) {
            return $aTranslatableFields;
        } elseif (array_key_exists($sTable, $aTranslatableFields)) {
            return $aTranslatableFields[$sTable];
        } else {
            return false;
        }
    }

    /**
     * return true if request (agent is in bot list).
     *
     * @return bool
     */
    public static function RequestIsInBotList()
    {
        static $aBotList = null;
        $bIsBot = false;
        if (null === $aBotList) {
            $cache = \ChameleonSystem\CoreBundle\ServiceLocator::get('chameleon_system_core.cache');
            $key = $cache->getKey(array('class' => __CLASS__, 'method' => 'RequestIsInBotList'), false);
            $aBotList = $cache->get($key);
            if (null === $aBotList) {
                $aBotList = explode("\n", TdbCmsConfig::GetInstance()->fieldBotlist);
                $cache->set($key, $aBotList, array(array('table' => 'cms_config', 'id' => null)));
            }
        }

        $sAgent = '';
        $request = \ChameleonSystem\CoreBundle\ServiceLocator::get('request_stack')->getCurrentRequest();
        if (null !== $request) {
            $sAgent = mb_strtolower($request->server->get('HTTP_USER_AGENT'));
        }

        reset($aBotList);
        foreach ($aBotList as $sBotList) {
            if (!empty($sBotList) && false !== stripos($sAgent, $sBotList)) {
                $bIsBot = true;
                break;
            }
        }

        return $bIsBot;
    }

    /**
     * checks whether the user that request the page is allowed to get access to the page
     * this can be configured by a white list or a config constant so only some users from a specified
     * range of ip addresses may get access.
     *
     * @return bool $bUserIsWhiteListed
     */
    public function CurrentIpIsWhiteListed()
    {
        $request = \ChameleonSystem\CoreBundle\ServiceLocator::get('request_stack')->getCurrentRequest();
        $sUserIpAddress = $request->getClientIp();

        $bUserIsWhiteListed = true;

        $oIpWhiteList = $this->GetFieldCmsIpWhitelistList();
        $oIpWhiteList->AddFilterString("`cms_ip_whitelist`.`ip` != ''");

        if ($oIpWhiteList->Length() > 0) {
            $bUserIsWhiteListed = false;
            //first check the white list configured in the backend (cms settings)
            while ($oIP = &$oIpWhiteList->Next() && false === $bUserIsWhiteListed) {
                $bUserIsWhiteListed = $this->CheckIP($oIP->fieldIp, $sUserIpAddress);
            }

            if (!$bUserIsWhiteListed) {
                trigger_error('Non WhiteListed user with IP '.$sUserIpAddress.' tried to pass', E_USER_ERROR);
            }
        }

        //user is still not allowed? check config constant
        if (defined('CMS_ALLOWED_IP') && CMS_ALLOWED_IP != '') {
            if (!$this->CheckIP(CMS_ALLOWED_IP, $sUserIpAddress)) {
                trigger_error('Non WhiteListed user with IP '.$sUserIpAddress.' tried to pass', E_USER_ERROR);
            }
        }

        return $bUserIsWhiteListed;
    }

    /**
     * check if $sUserIpAddress is expected ip
     * or if the expected ip is a CIDR then we call the function for CIDR check.
     *
     * @param string $sExpectedIp
     * @param string $sUserIpAddress
     *
     * @return bool
     */
    protected function CheckIP($sExpectedIp, $sUserIpAddress)
    {
        if ($this->IsCIDR($sExpectedIp)) {
            $bUserIsWhiteListed = $this->CheckCIDR($sExpectedIp, $sUserIpAddress);
        } else {
            $bUserIsWhiteListed = ($sExpectedIp == $sUserIpAddress);
        }

        return $bUserIsWhiteListed;
    }

    /**
     * check if the given address is CIDR.
     *
     * @param string $sIpAddress
     *
     * @return bool
     */
    protected function IsCIDR($sIpAddress)
    {
        $bIsCIDR = false;
        if (false !== stripos($sIpAddress, '/')) {
            $bIsCIDR = true;
        }

        return $bIsCIDR;
    }

    /**
     * checks if given ip address is in given CIDR
     * taken from zend framework http://framework.zend.com/svn/framework/extras/incubator/library/ZendX/Whois/Adapter/Cidr.php.
     *
     * @param string $sCIDR
     * @param string $sIpAddress
     *
     * @return bool
     */
    protected function CheckCIDR($sCIDR, $sIpAddress)
    {
        // Get the base and the bits from the CIDR
        list($base, $bits) = explode('/', $sCIDR);

        // Now split it up into it's classes
        list($a, $b, $c, $d) = explode('.', $base);

        // Now do some bit shifting/switching to convert to ints
        $i = ($a << 24) + ($b << 16) + ($c << 8) + $d;
        $mask = 0 == $bits ? 0 : (~0 << (32 - $bits));

        // Here's our lowest int
        $low = $i & $mask;

        // Here's our highest int
        $high = $i | (~$mask & 0xFFFFFFFF);

        // Now split the ip we're checking against up into classes
        list($a, $b, $c, $d) = explode('.', $sIpAddress);

        // Now convert the ip we're checking against to an int
        $check = ($a << 24) + ($b << 16) + ($c << 8) + $d;

        // If the ip is within the range, including highest/lowest values,
        // then it's witin the CIDR range
        if ($check >= $low && $check <= $high) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * will fill $this->aConfigValues if it haven't been fetched, yet, OR if $refreshIsPresent is set to true.
     *
     * @param bool $refreshIfPresent - ignore current state of the cache and reload even if it has already been fetched
     */
    private function fillConfigValuesCache($refreshIfPresent = false)
    {
        if ($refreshIfPresent || is_null($this->aConfigValues)) {
            $this->aConfigValues = array();
            $query = "SELECT * FROM `cms_config_parameter` WHERE `cms_config_id` = '".MySqlLegacySupport::getInstance()->real_escape_string($this->id)."'";
            $res = MySqlLegacySupport::getInstance()->query($query);
            while ($aRow = MySqlLegacySupport::getInstance()->fetch_assoc($res)) {
                $this->aConfigValues[$aRow['systemname']] = $aRow['value'];
            }
        }
    }

    /**
     * @return CacheInterface
     */
    private static function getCache()
    {
        return ServiceLocator::get('chameleon_system_cms_cache.cache');
    }
}
