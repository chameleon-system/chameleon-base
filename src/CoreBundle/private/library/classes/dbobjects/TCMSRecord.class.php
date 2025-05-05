<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use ChameleonSystem\CoreBundle\BackwardsCompatibilityShims\NamedConstructorSupport;
use ChameleonSystem\CoreBundle\Service\LanguageServiceInterface;
use ChameleonSystem\CoreBundle\Service\PageServiceInterface;
use ChameleonSystem\CoreBundle\Service\TreeServiceInterface;
use ChameleonSystem\CoreBundle\ServiceLocator;
use ChameleonSystem\CoreBundle\Session\ChameleonSessionManagerInterface;
use ChameleonSystem\CoreBundle\Util\FieldTranslationUtil;
use ChameleonSystem\CoreBundle\Util\MltFieldUtil;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Driver\Statement;
use Doctrine\DBAL\ForwardCompatibility\DriverResultStatement;
use Doctrine\DBAL\ForwardCompatibility\DriverStatement;
use esono\pkgCmsCache\CacheInterface;

/**
 * holds one record of a table.
 *
 * This method is implemented in every inheriting Tdb class.
 * See also: CoreBundle/private/rendering/objectviews/TCMSTableToClass/record.view.php
 *
 * @method static static GetNewInstance(string|array|null $sData = null, string|null $language = null)
 */
class TCMSRecord implements IPkgCmsSessionPostWakeupListener
{
    use NamedConstructorSupport;

    /**
     * name of the table.
     *
     * @var string
     */
    public $table;

    /**
     * assoc array holding the sql data row.
     *
     * @var array<string, mixed>|false
     */
    public $sqlData = false;

    /**
     * id of table record (complex id string) - use fieldCmsIdent if you want an auto increment.
     *
     * @var string
     */
    public $id;

    /**
     * if the record has translations, then iLanguageId can
     * be used to specify the language to use (set via public function: SetLanguage(id).
     */
    protected ?string $iLanguageId = null;

    /**
     * used internally to cache results.
     *
     * @var array
     */
    protected $aResultCache = [];

    /**
     * holds the table config object.
     *
     * @var TCMSTableConf|null
     */
    public $_oTableConf;

    /**
     * sets the state of field based translation overload fallback
     * e.g. if __en field is empty should it return the value from base language de?
     *
     * @var bool|null
     */
    private $bFieldBasedTranslationFallbackActive;

    /**
     * can be set to false using $this->DisablePostLoadHook(true);.
     *
     * @var bool
     */
    protected $bAllowPostLoadHookExecution = true;

    /**
     * if set to true, then the Load, LoadFromField and LoadFromFields will use the caching alternative.
     *
     * @var bool
     */
    protected $bEnableObjectCaching;

    /**
     * @var Connection
     */
    private $databaseConnection;

    /**
     * A hash of the cache relevant request state. Used to prevent unnecessary work for the post wakeup hook.
     *
     * @var string
     */
    private $requestStateHash;

    public function SetEnableObjectCaching($bEnable = true)
    {
        $this->bEnableObjectCaching = $bEnable;
    }

    public function GetEnableObjectCaching()
    {
        if (is_null($this->bEnableObjectCaching)) {
            return TCacheManagerRuntimeCache::GetEnableAutoCaching();
        } else {
            return $this->bEnableObjectCaching;
        }
    }

    public function __destruct()
    {
    }

    public function __clone()
    {
        foreach ($this as $sPropName => $sPropVal) {
            if (is_object($this->$sPropName)) {
                $this->$sPropName = clone $this->$sPropName;
            }
        }
    }

    /**
     * create instance of table object.
     *
     * @param string $table
     * @param string $id
     * @param string $iLanguageId
     */
    public function __construct($table = null, $id = null, $iLanguageId = null)
    {
        if (null !== $table) {
            $this->table = $table;
        }
        if (null !== $iLanguageId) {
            $this->SetLanguage($iLanguageId);
        } else {
            static $languageService = null;
            if (null === $languageService) {
                $languageService = self::getLanguageService();
            }
            if (null !== $activeLanguageId = $languageService->getActiveLanguageId()) {
                $this->SetLanguage($activeLanguageId);
            }
        }
        if (null !== $id) {
            $this->id = $id;
        }
        if (null !== $this->id) {
            $this->Load($this->id);
        }
    }

    /**
     * Sett the language of the record to fetch. Make sure to call this function
     * before calling anything else!
     *
     * @param string|null $languageId - id from table: cms_language
     */
    public function SetLanguage(?string $languageId)
    {
        $this->iLanguageId = $languageId;
    }

    /**
     * returns id of current record language or NULL if not set.
     *
     * @return string - NULL by default
     */
    public function GetLanguage()
    {
        return $this->iLanguageId;
    }

    /**
     * load data record. returns true if data was returned, else false.
     *
     * @param int|string $id
     *
     * @return bool
     */
    public function Load($id)
    {
        $foundRecord = false;
        if (!empty($id)) {
            if ($this->GetEnableObjectCaching()) {
                $foundRecord = $this->LoadWithCaching($id);
            } else {
                $this->id = $id;

                $conditions = [];

                $conditions[] = sprintf('`%s`.`id` = ?', $this->table);
                $query = $this->GetQueryString($conditions);

                $query .= ' LIMIT 1';
                $this->sqlData = $this->ExecuteSQLQueries($query, [$id])->fetchAssociative();
                $foundRecord = (false !== $this->sqlData && is_array($this->sqlData) && count($this->sqlData) > 0);
                if ($foundRecord) {
                    if ($this->bAllowPostLoadHookExecution) {
                        $this->PostLoadHook();
                    }
                    // check found after post load hook again, since it may remove the record again
                    $foundRecord = (false !== $this->sqlData && is_array($this->sqlData) && count($this->sqlData) > 0);
                }
            }
        } else {
            $this->sqlData = false;
        }

        return $foundRecord;
    }

    /**
     * this method will return a query string to restrict to the id value of this record.
     *
     * @param string $sTableName - name of the table on which the restriction acts
     * @param string $iIdValue - this MUST be the id value
     *
     * @return string
     */
    protected static function GetIdRestrictionQueryString($sTableName, $iIdValue)
    {
        return "`{$sTableName}`.`id` = ".ServiceLocator::get('database_connection')->quote($iIdValue);
    }

    /**
     * same as Load, except that the result is cached using a cache (it makes sense to use this
     * function if the PostLoadHook is expensive). After the first load the item will be cached as a singleton
     * so multiple loads within the same php process will not require any additional database operations.
     *
     * Note: You should overwrite the protected method GetCacheRelatedTables so that any tables loaded
     * by PostLoadHook are included in the cache info (so the cache can react to changes in those tables)
     *
     * @param int $id
     *
     * @return bool
     */
    public function LoadWithCaching($id)
    {
        return $this->LoadFromFieldWithCaching('id', $id);
    }

    /**
     * see LoadWithCaching for info.
     *
     * @param string $sField
     * @param string $sValue
     *
     * @return bool
     */
    public function LoadFromFieldWithCaching($sField, $sValue)
    {
        $bRecordLoaded = false;
        if (!empty($sField) && !empty($sValue)) {
            $aFields = [$sField => $sValue];
            $bRecordLoaded = $this->LoadFromFieldsWithCaching($aFields, false);
        }

        return $bRecordLoaded;
    }

    /**
     * define which tables are connected to the record through the PostLoadHook.
     * The info is needed if you want to use the LoadWithCaching method.
     *
     * @param int $id - record id
     *
     * @return array
     */
    protected function GetCacheRelatedTables($id)
    {
        return [['table' => $this->table, 'id' => $id]];
    }

    /**
     * the elements that define a unique instance of the item (used by the LoadWithCaching method).
     *
     * @param string $sFieldName - field name used to load the record (eg: id)
     * @param string $sFieldValue - the field value
     *
     * @return array
     */
    protected function GetCacheParameters($sFieldName, $sFieldValue)
    {
        return ['sTableName' => $this->table, 'sFieldName' => $sFieldName, 'sFieldValue' => $sFieldValue, 'iLanguageId' => $this->iLanguageId, 'sObjectName' => get_class($this)];
    }

    /**
     * returns the select statement for the load and loadfromfield functions
     * allows you to overwrite which fields are included in the sqlData
     * it MUST contain the placeholder [{condition}] where other conditions are to be placed.
     *
     * @param string[] $conditions
     *
     * @return string
     */
    protected function GetQueryString(array $conditions)
    {
        static $cache = [];
        if (false === isset($cache[$this->table])) {
            $databaseConnection = $this->getDatabaseConnection();
            $cache[$this->table] = $databaseConnection->quoteIdentifier($this->table);
        }
        $conditions = array_filter($conditions); // remove empty elements
        $conditionString = '';
        if (count($conditions) > 0) {
            $conditionString = sprintf(' WHERE (%s)', implode(') AND (', $conditions));
        }

        return sprintf(
            'SELECT * FROM %s %s',
            $cache[$this->table],
            $conditionString
        );
    }

    /**
     * Load the database record from the table using $sValue as a filter on the field $sField.
     *
     * @param string $field
     * @param scalar $value
     *
     * @return bool
     */
    public function LoadFromField($field, $value)
    {
        $this->id = null;
        if (null === $value || '' === $value) {
            return false;
        }

        return $this->LoadFromFields([
            $field => $value,
        ]);
    }

    /**
     * load data from more than one field.
     *
     * @param array $fieldData - assoc array in the form: fieldname=>value,...
     * @param bool $binary - use binary compare
     *
     * @return bool
     */
    public function LoadFromFields($fieldData, $binary = false)
    {
        $this->id = null;
        if ($this->GetEnableObjectCaching()) {
            return $this->LoadFromFieldsWithCaching($fieldData, $binary);
        }
        $conditions = [];
        $databaseConnection = $this->getDatabaseConnection();
        $languageService = self::getLanguageService();
        if (null !== $this->iLanguageId) {
            $language = $languageService->getLanguage($this->iLanguageId, $this->iLanguageId);
        } else {
            $language = $languageService->getActiveLanguage();
        }
        $fieldTranslationUtil = $this->getFieldTranslationUtil();
        foreach ($fieldData as $fieldName => $fieldValue) {
            $sqlFieldName = $fieldName;
            if ('id' !== $fieldName) {
                $sqlFieldName = $fieldTranslationUtil->getTranslatedFieldName($this->table, $fieldName, $language);
                $sqlFieldName = $databaseConnection->quoteIdentifier($sqlFieldName);
                if ($binary) {
                    $sqlFieldName = "CAST({$sqlFieldName} AS BINARY)";
                }
            } else {
                $sqlFieldName = $databaseConnection->quoteIdentifier($sqlFieldName);
            }

            $conditions[] = "{$sqlFieldName} = ?";
        }

        $query = $this->GetQueryString($conditions);
        $query .= ' LIMIT 1';

        if ($this->sqlData = $this->ExecuteSQLQueries($query, array_values($fieldData))->fetchAssociative()) {
            $this->id = $this->sqlData['id'];
            if ($this->bAllowPostLoadHookExecution) {
                $this->PostLoadHook();
            }
        }

        return is_array($this->sqlData) && count($this->sqlData);
    }

    /**
     * load data from more than one field
     * caches result.
     *
     * @param array $aFieldData - assoc array in the form: fieldname=>value,...
     * @param bool $bBinary - use binary compare
     *
     * @return bool
     */
    public function LoadFromFieldsWithCaching($aFieldData, $bBinary = false)
    {
        $bRecordLoaded = false;
        $aKey = $aFieldData;
        $aKey['class'] = __CLASS__;
        $aKey['method'] = 'LoadFromFieldsWithCaching';
        $aKey['table'] = $this->table;
        $aKey['__binary'] = $bBinary;
        $aKey['bFieldBasedTranslationFallbackActive'] = $this->isFieldBasedTranslationFallbackActive();
        $aKey['languageID'] = $this->iLanguageId;
        $sKey = TCacheManagerRuntimeCache::GetKey($aKey);
        // check local cache first
        $aData = null;
        if (false === TCacheManagerRuntimeCache::KeyExists($sKey)) {
            $bObjectCaching = $this->GetEnableObjectCaching();
            $this->SetEnableObjectCaching(false);
            $bRecordLoaded = $this->LoadFromFields($aFieldData, $bBinary);
            $this->SetEnableObjectCaching($bObjectCaching);
            if ($bRecordLoaded) {
                $aData = $this->sqlData;
            } else {
                $aData = null;
            }
            TCacheManagerRuntimeCache::SetContent($sKey, $aData);
        } else {
            $aData = TCacheManagerRuntimeCache::GetContents($sKey);
            if (null !== $aData) {
                $this->LoadFromRow($aData);
                $bRecordLoaded = true;
            }
        }

        if (false === $bRecordLoaded) {
            $this->sqlData = false;
        }

        return $bRecordLoaded;
    }

    /**
     * Initializes the record from the passed row.
     *
     * @param array $row
     */
    public function LoadFromRow($row)
    {
        $this->id = null;
        $this->sqlData = $row;
        if (is_array($row) && isset($row['id'])) {
            $this->id = $row['id'];
        }
        if ($this->bAllowPostLoadHookExecution) {
            $this->PostLoadHook();
        }
    }

    /**
     * this method is called after successful record loading.
     */
    protected function PostLoadHook()
    {
        // fetch translation
        if ($this->bAllowPostLoadHookExecution) {
            $this->ClearInternalCache();
        }
        $this->requestStateHash = $this->getRequestStateHash();
    }

    public function __wakeup()
    {
        if (null !== $this->requestStateHash && $this->requestStateHash === $this->getRequestStateHash()) {
            return;
        }

        $this->PostWakeUpHook();
    }

    /**
     * called when an object recovers from serialization.
     */
    protected function PostWakeUpHook()
    {
        if (false === ACTIVE_TRANSLATION) {
            return;
        }
        static $sessionManager = null;
        if (null === $sessionManager) {
            $sessionManager = $this->getSessionManager();
        }
        if (false === $sessionManager->isSessionStarting()) {
            return;
        }
        $activeLanguage = self::getLanguageService()->getActiveLanguage();

        if (null !== $activeLanguage && $activeLanguage->id !== $this->GetLanguage()) {
            $this->reloadTranslatedFields($activeLanguage);
        }
    }

    public function sessionWakeupHook()
    {
        if (null !== $this->requestStateHash && $this->requestStateHash === $this->getRequestStateHash()) {
            return;
        }

        $this->PostWakeUpHook();
    }

    private function reloadTranslatedFields(TdbCmsLanguage $language): void
    {
        if (!is_array($this->sqlData)) {
            return;
        }
        $this->SetLanguage($language->id);
        $translatedFields = $this->getTranslatableFields();
        foreach ($translatedFields as $translatedField) {
            $this->sqlData[$translatedField] = $this->sqlData[$translatedField.'__'.$language->fieldIso6391];
            $fieldName = 'field'.TCMSTableToClass::ConvertToClassString($translatedField);
            $this->$fieldName = $this->sqlData[$translatedField];
        }
        if (count($translatedFields) > 0) {
            $this->ClearInternalCache();
        }
    }

    /**
     * Returns a list of all translatable fields for this class.
     * This method does only make sense for TAdb subclasses. Do not expect code in the base method to be even executed.
     * We would like to turn TCMSRecord into an abstract class and define this method as abstract, but do not want
     * to break BC.
     *
     * @return string[]
     */
    public function getTranslatableFields()
    {
        return [];
    }

    /**
     * output a wysiwyg text for a field. will return an empty string, if the text field is "empty" (<div>&nbsp;</div> are considert empty too)
     * Optional custom variables can be placed into the wysiwyg editor - they will be replaced using the aCustomVariables passed.
     * These variables must have the following format: [{name:formatierung}]
     * "formatierung" ist either string, date, or number. It is possible to specify the number of decimals
     * used when formating a number: [{variable:number:decimalplaces}]
     * example [{costs:number:2}].
     *
     * @param string $fieldName - field name
     * @param int $width - max image width within the text
     * @param bool $includeClearDiv - include a clear div at the end of the text block
     * @param array $aCustomVariables - any custom variables you want to replace
     * @param array $aEffects - See method TCMSImage::GetThumbnailPointer for available effects
     * @param int $iMaxImageZoomWidth - max width of zoomed images
     * @param int $iMaxImageZoomHeight - max height of zoomed images
     *
     * @return string
     */
    public function GetTextField($fieldName, $width = 1200, $includeClearDiv = true, $aCustomVariables = null, $aEffects = [], $iMaxImageZoomWidth = -1, $iMaxImageZoomHeight = -1)
    {
        $cacheName = '_textField'.$fieldName.$width.md5(serialize($aCustomVariables).serialize($aEffects));
        $sContent = $this->GetFromInternalCache($cacheName);
        if (is_null($sContent)) {
            /** @var $oTextField TCMSTextField */
            $oTextField = new TCMSTextField();
            $oTextField->content = $this->sqlData[$fieldName];
            $oTextField->SetMaxImageZoomDimensions($iMaxImageZoomWidth, $iMaxImageZoomHeight);

            $sContent = $oTextField->GetText($width, $includeClearDiv, $aCustomVariables, $fieldName.$this->id, $aEffects);
            $this->SetInternalCache($cacheName, $sContent);
        }

        return $sContent;
    }

    /**
     * returns wysiwyg field contents as plaintext without HTML and by given length.
     *
     * @param string $fieldName - textfield to return
     * @param int $length - max length of the text
     *
     * @return string - the cutted plain text
     */
    public function GetTextFieldPlain($fieldName, $length = null)
    {
        $cacheName = '_textFieldPlain'.$fieldName;
        if (!is_null($length)) {
            $cacheName .= $length;
        }

        $sContent = $this->GetFromInternalCache($cacheName);
        if (is_null($sContent)) {
            $oTextField = new TCMSTextField();
            $oTextField->content = $this->sqlData[$fieldName];
            $sContent = $oTextField->GetPlainTextWordSave($length);
            $this->SetInternalCache($cacheName, $sContent);
        }

        return $sContent;
    }

    /**
     * Output a wysiwyg text for a field for external usage, such as emails, rss feeds etc.
     *
     * Will return an empty string, if the text field is "empty" (<div>&nbsp;</div> are considert empty too)
     * Optional custom variables can be placed into the wysiwyg editor - they will be replaced using the aCustomVariables passed.
     * These variables must have the following format: [{name:formatierung}]
     * "formatierung" ist either string, date, or number. It is possible to specify the number of decimals
     * used when formating a number: [{variable:number:decimalplaces}]
     * example [{costs:number:2}]
     *
     * @param string $fieldName - field name
     * @param int $width - max image width within the text
     * @param bool $includeClearDiv - include a clear div at the end of the text block
     * @param array $aCustomVariables - any custom variables you want to replace
     * @param bool $bClearThickBox - remove all a href with class thickbox
     * @param bool $bClearScriptTags - clear all script tags
     *
     * @return string
     */
    public function GetTextForExternalUsage($fieldName, $width = 600, $includeClearDiv = true, $aCustomVariables = null, $bClearThickBox = false, $bClearScriptTags = false)
    {
        $cacheName = '_textField'.$fieldName.$width.md5(serialize($aCustomVariables));
        $sContent = $this->GetFromInternalCache($cacheName);
        if (is_null($sContent)) {
            $oTextField = new TCMSTextField();
            /* @var $oTextField TCMSTextField */
            $oTextField->content = $this->sqlData[$fieldName];
            $sContent = $oTextField->GetTextForExternalUsage($width, $includeClearDiv, $aCustomVariables, $bClearThickBox, $bClearScriptTags);
            $this->SetInternalCache($cacheName, $sContent);
        }

        return $sContent;
    }

    /**
     * returns content of moduleinstance given by field sFieldName.
     *
     * @param string $sFieldName
     * @param array $aAdditionalModuleParameters - will be available in the called module in $this->aModuleConfig
     * @param string $sModuleSpotName - optional
     *
     * @return string - generated module HTML
     */
    public function GetModuleInstanceField($sFieldName, $aAdditionalModuleParameters = [], $sModuleSpotName = 'tmpmodule')
    {
        $moduleInstanceId = $this->sqlData[$sFieldName];
        if ('' === $moduleInstanceId || '0' === $moduleInstanceId) {
            return '';
        }
        $oModuleInstance = new TCMSTPLModuleInstance();
        /* @var $oModuleInstance TCMSTPLModuleInstance */
        $oModuleInstance->Load($moduleInstanceId);

        return $oModuleInstance->Render($aAdditionalModuleParameters, $sModuleSpotName, $this->sqlData[$sFieldName.'_view']);
    }

    /**
     * returns the name of the record.
     *
     * @return string
     */
    public function GetName()
    {
        if (false === $this->sqlData) {
            return '';
        }

        $recordName = $this->GetFromInternalCache('recordName');
        if (null !== $recordName) {
            return $recordName;
        }

        $tableConfig = $this->GetTableConf();
        if (null === $tableConfig || null === $tableConfig->id) {
            $recordName = $this->sqlData['name'];
        } else {
            $nameColumn = $tableConfig->GetNameColumn();
            $nameColumnFieldExists = (is_array($this->sqlData) && isset($this->sqlData[$nameColumn]));

            if (false === $nameColumnFieldExists && !empty($tableConfig->sqlData['list_query'])) {
                $recordName = $this->getNameFromQuery($nameColumn);
                if (null !== $recordName) {
                    $this->sqlData[$nameColumn] = $recordName;
                    $nameColumnFieldExists = true;
                }
            }

            if ($nameColumnFieldExists) {
                $translationNameColumn = $this->getFieldTranslationUtil()->getTranslatedFieldName($this->table, $nameColumn);
                if ($translationNameColumn !== $nameColumn && isset($this->sqlData[$translationNameColumn])) {
                    $recordName = $this->sqlData[$translationNameColumn];
                } else {
                    $recordName = $this->sqlData[$nameColumn];
                }

                $nameFieldCallbackFunction = $tableConfig->GetNameFieldCallbackFunction();
                if (null !== $nameFieldCallbackFunction) {
                    $recordName = call_user_func($nameFieldCallbackFunction, $recordName, $this->sqlData, $nameColumn);
                }
            } else {
                $recordName = $tableConfig->GetName();
            }
        }

        $this->SetInternalCache('recordName', $recordName);

        return $recordName;
    }

    private function getNameFromQuery(string $nameColumn): ?string
    {
        $listQuery = $this->_oTableConf->sqlData['list_query'];

        $queryFilter = ' WHERE '.$this->getDatabaseConnection()->quoteIdentifier($this->table).'.`id` = '.$this->getDatabaseConnection()->quote($this->id);

        if (0 === preg_match('/\s+WHERE\s+/i', $listQuery)) {
            $listQuery .= $queryFilter;
        } else {
            $listQuery = preg_replace('/\s+WHERE\s+/i', $queryFilter.' AND ', $listQuery);
        }

        $nameRecord = $this->ExecuteSQLQueries($listQuery)->fetchAssociative();

        if (is_array($nameRecord) && isset($nameRecord[$nameColumn])) {
            return $nameRecord[$nameColumn];
        }

        return null;
    }

    /**
     * returns the name of the record modified to display it in breadcrumbs or anywhere
     * you may add icons, prefixes or whatever here.
     *
     * @return string
     */
    public function GetDisplayValue()
    {
        $sContent = $this->GetFromInternalCache('recordDisplayName');
        if (is_null($sContent)) {
            $sContent = '';
            $this->GetTableConf();
            if (!is_null($this->_oTableConf->id)) {
                $displayColumn = $this->_oTableConf->GetDisplayColumn();
                if (is_array($this->sqlData) && isset($this->sqlData[$displayColumn])) {
                    $prefix = TGlobal::GetLanguagePrefix();
                    if ('' !== $prefix && isset($this->sqlData[$displayColumn.'__'.$prefix])) {
                        $displayColumn = $displayColumn.'__'.$prefix;
                    }
                    $sContent = $this->sqlData[$displayColumn];
                    $sDisplayFieldCallbackFunction = $this->_oTableConf->GetDisplayFieldCallbackFunction();
                    if (!is_null($sDisplayFieldCallbackFunction)) {
                        // make sure the callback is available
                        $sContent = $sDisplayFieldCallbackFunction($sContent, $this->sqlData, $displayColumn);
                    }
                } else {
                    $sContent = TGlobal::OutHTML($this->_oTableConf->GetName());
                }
            } else {
                $sContent = TGlobal::OutHTML($this->sqlData['name']);
            }

            if (!stristr($sContent, '<') && !stristr($sContent, '>')) {
                if (mb_strlen($sContent) > 50) {
                    $sContent = substr($sContent, 0, 50).'...';
                }
            }

            $this->SetInternalCache('recordDisplayName', $sContent);
        }

        return $sContent;
    }

    /**
     * returns a date field formatted for the current locale (currently German format only!)
     * needs to use CMS locale later!
     *
     * @param string $sFieldName - name of the database column
     *
     * @return string - formatted date string
     */
    public function GetDateField($sFieldName)
    {
        return TCMSLocal::GetActive()->FormatDate($this->sqlData[$sFieldName], TCMSLocal::DATEFORMAT_SHOW_ALL);
    }

    /**
     * returns the definition of the table that holds this row.
     *
     * @return TdbCmsTblConf
     */
    public function GetTableConf()
    {
        if (is_null($this->_oTableConf)) {
            $this->_oTableConf = TdbCmsTblConf::GetNewInstance(null, $this->iLanguageId);

            // fallback to base class - needed during DB autoClass generation
            if (null === $this->_oTableConf) {
                $this->_oTableConf = new TCMSTableConf();
            }

            // load table conf only if we are not a table conf!
            if ('cms_tbl_conf' == $this->table && 'cms_tbl_conf' == $this->sqlData['name']) {
                // we are the table conf of the table conf...
                $this->_oTableConf->LoadFromRow($this->sqlData);
            } else {
                $this->_oTableConf->LoadFromFieldWithCaching('name', $this->table);
            }
        }

        return $this->_oTableConf;
    }

    /**
     * @return array - the fields with current values; indexed by field name
     */
    public function getFieldsIndexed(): array
    {
        $fieldsIterator = $this->GetTableConf()->GetFields($this);

        $fields = [];
        /**
         * @var TCMSField $field
         */
        while ($field = $fieldsIterator->next()) {
            $fields[$field->name] = $field;
        }

        return $fields;
    }

    /**
     * returns iterator of images.
     *
     * @param string $fieldName
     * @param bool $includeDummyImages
     *
     * @return TIterator
     */
    public function GetImages($fieldName = 'images', $includeDummyImages = false)
    {
        $sCacheName = 'imageField'.$fieldName;
        if ($includeDummyImages) {
            $sCacheName .= '_withDummyImages';
        }
        $oImages = $this->GetFromInternalCache($sCacheName);
        if (is_null($oImages)) {
            $idlist = explode(',', $this->sqlData[$fieldName]);
            $oImages = new TIterator();
            foreach ($idlist as $id) {
                if (!is_numeric($id) || $id >= 1000 || $includeDummyImages) {
                    $oImage = new TCMSImage();
                    /* @var $oImage TCMSImage */
                    $oImage->Load($id);
                    $oImages->AddItem($oImage);
                }
            }

            $this->SetInternalCache($sCacheName, $oImages);
        }

        return $oImages;
    }

    /**
     * returns TCMSImage object for given image nr. (NOT id!) in multiple images, field.
     *
     * @param int $imagePos
     * @param string $fieldName
     * @param bool $includeDummyImage
     *
     * @return TCMSImage|null - null, if no image is set
     */
    public function GetImage($imagePos = 0, $fieldName = 'images', $includeDummyImage = false)
    {
        $sKey = 'image-'.$imagePos.'-'.$fieldName;
        if ($includeDummyImage) {
            $sKey = $sKey.'true';
        }
        $oImage = $this->GetFromInternalCache($sKey);
        if (is_null($oImage)) {
            $oImage = false;
            $mediaID = $this->GetImageCMSMediaId($imagePos, $fieldName);
            if (!is_numeric($mediaID) || $mediaID >= 1000 || $includeDummyImage) {
                $oImage = new TCMSImage();
                /** @var $oImage TCMSImage */
                if (false == $oImage->Load($mediaID)) {
                    // return the not found image
                    $oImage->Load(-1);
                }
            }
            $this->SetInternalCache($sKey, $oImage);
        }
        if (false === $oImage) {
            $oImage = null;
        }

        return $oImage;
    }

    /**
     *Gets the cms media id from image field with given image position.
     *
     * @param int $iImagePos
     * @param string $sFieldName
     *
     * @return string|bool
     */
    public function GetImageCMSMediaId($iImagePos = 0, $sFieldName = 'images')
    {
        $sCmsMediaId = '';
        if (isset($this->sqlData[$sFieldName])) {
            $aImages = explode(',', $this->sqlData[$sFieldName]);
            if (is_int($iImagePos) && (is_array($aImages) && isset($aImages[$iImagePos]))) {
                $sCmsMediaId = $aImages[$iImagePos];
            }
        }

        return $sCmsMediaId;
    }

    /**
     * returns the E-Mail encoded so that it can not be harvested by a spambot.
     *
     * @deprecated
     *
     * @param string $sFieldName - field name
     * @param string $sDisplayText - link text to show instead of email address
     *
     * @return string
     */
    public function GetEMailLink($sFieldName, $sDisplayText = null)
    {
        return $this->sqlData[$sFieldName];
    }

    /**
     * returns a list of connected downloads.
     *
     * @param string $sDownloadField - name of the field that connects the downloads
     * @param array $allowedFileTypes - optional array of file extensions the record list will be filtered by
     * @param bool $bOrderByPosition - set this to true if you want to use the MLT table entry_sort field
     *
     * @return TdbCmsDocumentList
     */
    public function GetDownloads($sDownloadField = 'data_pool', $allowedFileTypes = null, $bOrderByPosition = false)
    {
        $mltTable = $this->table.'_'.$sDownloadField.'_cms_document_mlt';

        $databaseConnection = $this->getDatabaseConnection();
        $quotedMltTableName = $databaseConnection->quoteIdentifier($mltTable);
        $quotedId = $databaseConnection->quote($this->id);

        if (!is_null($allowedFileTypes) && is_array($allowedFileTypes) && count($allowedFileTypes) > 0) {
            $fileExtensions = [];
            foreach ($allowedFileTypes as $fileExtension) {
                $fileExtensions[] = $databaseConnection->quote($fileExtension);
            }

            $sQuery = "SELECT `cms_document`.*
                       FROM `cms_document`
                       INNER JOIN $quotedMltTableName ON `cms_document`.`id` = $quotedMltTableName.`target_id`
                       LEFT JOIN `cms_filetype` ON `cms_document`.`cms_filetype_id` = `cms_filetype`.`id`
                       WHERE $quotedMltTableName.`source_id` = $quotedId
                       AND `cms_filetype`.`file_extension` IN (".implode(',', $fileExtensions).')';

            $sQuery .= 'ORDER BY `cms_document`.`name`
               ';
        } else {
            $sQuery = "SELECT `cms_document`.*
                  FROM `cms_document`
            INNER JOIN $quotedMltTableName ON `cms_document`.`id` = $quotedMltTableName.`target_id`
                 WHERE $quotedMltTableName.`source_id` = $quotedId";

            if ($bOrderByPosition) {
                $sQuery .= " ORDER BY $quotedMltTableName.`entry_sort` ";
            } else {
                $sQuery .= ' ORDER BY `cms_document`.`name` ';
            }
        }

        return TdbCmsDocumentList::GetList($sQuery);
    }

    /**
     * returns all connected entries for an mlt field
     * $sClassName is the class used to hold the Items. if this is a translation,
     * then it will fetch all records connected to the parent.
     *
     * Set parameter $sOrderBy for faster result. Otherwise the function get sorting automatically from the configured sort fields in target table.
     *
     * @param string $sFieldName
     * @param string $sClassName
     * @param string $sOrderBy - added after an "order by" sql statement
     * @param string $sClassSubType - specify what type of class sClassName is
     * @param string $sClassType - specify if the class is core, custom-core, or customer
     * @param string $sListAutoClassName - set if you want an auto class
     * @param bool $bForceLoad - set to true if you dont want that your list comes (and also is written - after load) into internal cache
     * @param string $sTargetTableName - name of the target table needed to generate the mlt table name. if not set function have to load the mlt field object to generate the mlt table name(lower performance).
     *
     * @return TCMSRecordList
     */
    public function GetMLT($sFieldName, $sClassName = 'TCMSRecord', $sOrderBy = '', $sClassSubType = 'dbobjects', $sClassType = 'Core', $sListAutoClassName = null, $bForceLoad = false, $sTargetTableName = '')
    {
        $sCacheName = 'mltField'.$sFieldName.'_'.$sClassName.'_'.$sOrderBy.'_'.$sClassSubType.'_'.$sClassType.'_'.$sTargetTableName;
        if (false === $bForceLoad) {
            $oMLTFields = $this->GetFromInternalCache($sCacheName);
        } else {
            $oMLTFields = null;
        }
        if (is_null($oMLTFields)) {
            if (empty($sTargetTableName)) {
                $oMltField = $this->GetMLTField($sFieldName);
            } else {
                $oMltField = null;
            }
            if (!is_null($oMltField) || !is_null($sTargetTableName)) {
                if (!empty($sTargetTableName)) {
                    $fTable = $sTargetTableName;
                    $sMLTTableName = $this->GetMltTableName($sFieldName, $sTargetTableName);
                } elseif (!is_null($oMltField)) {
                    $fTable = $oMltField->GetConnectedTableName();
                    $sMLTTableName = $oMltField->GetMLTTableName();
                }
                $sOrderBy = $this->GetMLTOrderBy($sOrderBy, $sFieldName, $sMLTTableName, $fTable, $this->table);

                $databaseConnection = $this->getDatabaseConnection();
                $quotedFTableName = $databaseConnection->quoteIdentifier($fTable);
                $quotedMltTableName = $databaseConnection->quoteIdentifier($sMLTTableName);

                $query = "SELECT $quotedFTableName.*
                          FROM $quotedMltTableName
                          INNER JOIN $quotedFTableName ON $quotedMltTableName.`target_id` = $quotedFTableName.`id`
                ";

                $query .= "WHERE $quotedMltTableName.`source_id` = ".$databaseConnection->quote($this->id);

                if (!empty($sOrderBy)) {
                    $query .= ' ORDER BY '.$sOrderBy;
                }

                if (is_null($sListAutoClassName)) {
                    $oMLTFields = new TCMSRecordList($sClassName, $fTable, $query, $this->iLanguageId);
                } else {
                    $oMLTFields = call_user_func([$sListAutoClassName, 'GetList'], $query);
                }

                if (false === $bForceLoad) {
                    $this->SetInternalCache($sCacheName, $oMLTFields);
                }
            }
        } else {
            $oMLTFields->GoToStart();
        }

        return $oMLTFields;
    }

    /**
     * Function generates mlt name for given field and table name.
     * Note: Parameter $sTableName could contain source table name or target table name.
     * For example:
     * if parameter $bActiveRecordIsTarget is true the parameter $sTableName have to contain the source table name.
     * if parameter $bActiveRecordIsTarget is false the parameter $sTableName have to contain the target table name.
     *
     * @param string $sFieldName name of the mlt field
     * @param string $sTableName name of the table
     * @param bool $bActiveRecordIsTarget
     *
     * @return string
     */
    protected function GetMltTableName($sFieldName, $sTableName, $bActiveRecordIsTarget = false)
    {
        if ($bActiveRecordIsTarget) {
            $sTargetTableName = $this->table;
            $sSourceTableName = $sTableName;
        } else {
            $sTargetTableName = $sTableName;
            $sSourceTableName = $this->table;
        }
        $sCleanedFieldName = $sFieldName;
        if (!empty($sFieldName)) {
            $mltFieldUtil = self::getMltFieldUtil();
            $sFieldName = $mltFieldUtil->cutMltExtension($sFieldName);
            $sCleanedFieldName = $mltFieldUtil->cutMultiMltFieldNumber($sFieldName);
        } else {
            $sFieldName = '';
        }

        if ($sCleanedFieldName == $sTargetTableName) {
            $sMLTTableName = $sSourceTableName.'_'.$sFieldName.'_mlt';
        } else {
            if (!empty($sFieldName)) {
                $sFieldName = '_'.$sFieldName;
            }
            $sMLTTableName = $sSourceTableName.$sFieldName.'_'.$sTargetTableName.'_mlt';
        }

        return $sMLTTableName;
    }

    /**
     * Get MLT field object for given field name.
     * If $sTableName is false field must exist in current object table else
     * in given table.
     *
     * @param string $sFieldIdName
     * @param bool $sTableName
     *
     * @return TCMSField|null
     */
    protected function GetMLTField($sFieldIdName, $sTableName = false)
    {
        if (false === $sTableName) {
            $sTableName = $this->table;
        }
        $oMLTField = null;
        $databaseConnection = $this->getDatabaseConnection();
        $quotedTableName = $databaseConnection->quote($sTableName);
        $quotedFieldIdName = $databaseConnection->quote($sFieldIdName);
        $sQuery = "SELECT `cms_field_conf`.* FROM `cms_tbl_conf`
                   INNER JOIN `cms_field_conf` ON `cms_field_conf`.`cms_tbl_conf_id` = `cms_tbl_conf`.`id`
                   WHERE `cms_tbl_conf`.`name` = $quotedTableName
                   AND `cms_field_conf`.`name` = $quotedFieldIdName";
        $oFieldDefinitionList = TdbCmsFieldConfList::GetList($sQuery);
        $oFieldDefinition = $oFieldDefinitionList->Current();
        if (false !== $oFieldDefinition) {
            $oMLTField = $oFieldDefinition->GetFieldObject();
            $oMLTField->sTableName = $sTableName;
            $oMLTField->name = $oFieldDefinition->sqlData['name'];
            $oMLTField->oDefinition = $oFieldDefinition;
            if (!$oMLTField->isMLTField) {
                $oMLTField = null;
            }
        }

        return $oMLTField;
    }

    /**
     * returns a record list of records connected as source via mlt.
     *
     * Set parameter $sOrderBy for faster result. Otherwise the function get sorting automatically from the configured sort fields in target table.
     *
     * @param string $sSourceTableName
     * @param string $sSourceFieldName - name of the mlt field in the source table
     * @param string $sOrderBy
     * @param string $sClassName
     * @param string $sClassSubType
     * @param string $sClassType
     *
     * @return TCMSRecordList
     */
    public function GetMLTSourceRecords($sSourceTableName, $sSourceFieldName, $sOrderBy = '', $sClassName = 'TCMSRecord', $sClassSubType = 'dbobjects', $sClassType = 'Core')
    {
        $sCacheName = 'mltField'.$sSourceFieldName.'_'.$sClassName.'_'.$sOrderBy.'_'.$sClassSubType.'_'.$sClassType;
        $oMLTFields = null;
        if (is_null($oMLTFields)) {
            $fTable = $this->table;
            $sMLTTableName = $this->GetMltTableName($sSourceFieldName, $sSourceTableName, true);
            $sOrderBy = $this->GetMLTOrderBy($sOrderBy, $sSourceFieldName, $sMLTTableName, $fTable, $sSourceTableName);

            $databaseConnection = $this->getDatabaseConnection();
            $quotedFTableName = $databaseConnection->quoteIdentifier($fTable);
            $quotedMltTableName = $databaseConnection->quoteIdentifier($sMLTTableName);
            $quotedSourceTableName = $databaseConnection->quoteIdentifier($sSourceTableName);

            $query = "SELECT $quotedSourceTableName.*
                      FROM $quotedMltTableName
                 ";
            if ($fTable == $sSourceTableName) {
                $query .= "INNER JOIN $quotedSourceTableName ON $quotedMltTableName.`source_id` = $quotedSourceTableName.`id`";
            } else {
                $query .= "INNER JOIN $quotedSourceTableName ON $quotedMltTableName.`source_id` = $quotedSourceTableName.`id`
                     INNER JOIN $quotedFTableName ON $quotedMltTableName.`target_id` = $quotedFTableName.`id`";
            }
            $query .= "WHERE $quotedMltTableName.`target_id` = ".$databaseConnection->quote($this->id);
            if (!empty($sOrderBy)) {
                $query .= ' ORDER BY '.$sOrderBy;
            }
            $oMLTFields = new TCMSRecordList($sClassName, $sSourceTableName, $query, $this->iLanguageId);
            $this->SetInternalCache($sCacheName, $oMLTFields);
        }

        return $oMLTFields;
    }

    /**
     * returns true if the record is connected using an MLT
     * returns false if not connected or no MLT exists.
     *
     * @param string $sSourceTableName
     * @param int $iSourceID - THIS MUST BE THE ID FIELD OF THE THE SOURCE TABLE (NOT THE cmsident)
     * @param string $sMLTTable - option: overwrite the table info to use $sMLTTable instead for the connection check
     * @param string $sMLTSourceFieldName If source table has more then one mlt connection to the active object table the you can specify with the mlt source field name
     *
     * @return bool
     */
    public function isConnected($sSourceTableName, $iSourceID, $sMLTTable = null, $sMLTSourceFieldName = '')
    {
        $databaseConnection = $this->getDatabaseConnection();
        $quotedSourceId = $databaseConnection->quote($iSourceID);

        $sCacheName = 'isConnected'.$sSourceTableName.'_'.$iSourceID.'_'.$sMLTSourceFieldName.'_'.$this->table;
        $isConnected = $this->GetFromInternalCache($sCacheName);
        if (is_null($isConnected)) {
            $aMLTTableNameList = $this->GetConnectedMLTTableNameList($sSourceTableName, $sMLTSourceFieldName, $sMLTTable);
            if (count($aMLTTableNameList) > 0) {
                $iRealId = $this->id;
                foreach ($aMLTTableNameList as $sMLTTableName) {
                    if (!$isConnected) {
                        $quotedMltTableName = $databaseConnection->quoteIdentifier($sMLTTableName);
                        $sQuery = "SELECT * FROM $quotedMltTableName WHERE `target_id` = ".$databaseConnection->quote($iRealId)." AND `source_id` = $quotedSourceId";
                        $result = $this->ExecuteSQLQueries($sQuery);
                        if ($result->rowCount() > 0) {
                            $isConnected = true;
                        }
                    }
                }
                $this->SetInternalCache($sCacheName, $isConnected);
            }
        }

        return $isConnected;
    }

    /**
     * Returns List of all connected mlt tables.
     *
     * @param string $sSourceTableName name of the source table
     * @param string $sMLTSourceFieldName If source table has more then one mlt connection to the active object table the you can specify with the mlt source field name
     * @param string $sMLTTable option: overwrite the table info to use $sMLTTable instead for the connected mlt tables
     *
     * @return array
     */
    protected function GetConnectedMLTTableNameList($sSourceTableName, $sMLTSourceFieldName = '', $sMLTTable = null)
    {
        $aMLTTableNameList = [];
        if (is_null($sMLTTable)) {
            $sQuery = "SELECT `cms_field_conf`.`name` as sFieldName  FROM `cms_field_conf`
                    INNER JOIN `cms_field_type` ON `cms_field_type`.`id` = `cms_field_conf`.`cms_field_type_id`
                    INNER JOIN `cms_tbl_conf` ON `cms_tbl_conf`.`id` = `cms_field_conf`.`cms_tbl_conf_id`
                         WHERE `cms_field_type`.`base_type` ='mlt'
                           AND `cms_tbl_conf`.`name` = ".$this->getDatabaseConnection()->quote($sSourceTableName);
            if (!empty($sMLTSourceFieldName)) {
                $sQuery .= ' AND `cms_field_conf`.`name` = '.$this->getDatabaseConnection()->quote($sMLTSourceFieldName);
            } else {
                $sQuery .= " AND (`cms_field_conf`.`name` REGEXP '^".$this->table."([1-9]?_mlt$|[1-9]*$|_mlt$)'
                            OR `cms_field_conf`.`fieldtype_config` REGEXP 'connectedTableName\s*=\s*".$this->table."(\s*$|$)')";
            }
            $oMySqLSourceFieldName = $this->getDatabaseConnection()->executeQuery($sQuery);
            if ($oMySqLSourceFieldName->rowCount() > 0) {
                while ($oSourceFieldNameRow = $oMySqLSourceFieldName->fetchAssociative()) {
                    $aMLTTableNameList[] = $this->GetMltTableName($oSourceFieldNameRow['sFieldName'], $sSourceTableName, true);
                }
            }
        } elseif (self::TableExists($sMLTTable)) {
            $aMLTTableNameList[] = $sMLTTable;
        }

        return $aMLTTableNameList;
    }

    /**
     * checks if a record is connected to this active object through the mlt field given by sMLTField
     * (use isConnected if you need the information the other way around - ie you want to know if this
     * record is connected to via mlt from some other table).
     *
     * @param string $sMLTField - mlt field in this table
     * @param string $iTargetID - target id to check
     * @param string $sTargetTableName - name of the target table needed to generate the mlt table name. if not set function have to load the mlt field object to generate the mlt table name(lower performance).
     *
     * @return bool
     */
    public function HasConnection($sMLTField, $iTargetID, $sTargetTableName = '')
    {
        $sCacheName = 'hasConnected'.$sMLTField.'_'.$iTargetID.'_'.$sTargetTableName;
        $bIsConnected = $this->GetFromInternalCache($sCacheName);
        if (is_null($bIsConnected)) {
            // connections are always between parents... if this is a multilang table we need to fetch the parent...
            $iSourceID = $this->id;
            if (empty($sTargetTableName)) {
                $oMltField = $this->GetMLTField($sMLTField);
            } else {
                $oMltField = null;
            }
            if (!is_null($oMltField) || !is_null($sTargetTableName)) {
                if (!empty($sTargetTableName)) {
                    $sMLTTable = $this->GetMltTableName($sMLTField, $sTargetTableName);
                } elseif (!is_null($oMltField)) {
                    $sMLTTable = $oMltField->GetMLTTableName();
                }
                $databaseConnection = $this->getDatabaseConnection();
                $quotedMltTableName = $databaseConnection->quoteIdentifier($sMLTTable);
                $quotedSourceId = $databaseConnection->quote($iSourceID);
                $quotedTargetId = $databaseConnection->quote($iTargetID);
                $query = "SELECT * FROM $quotedMltTableName
                          WHERE `source_id` = $quotedSourceId
                          AND `target_id` = $quotedTargetId";

                $oMysqlRes = $this->ExecuteSQLQueries($query);
                if ($oMysqlRes->rowCount() > 0) {
                    $bIsConnected = true;
                }
                $this->SetInternalCache($sCacheName, $bIsConnected);
            }
        }

        return $bIsConnected;
    }

    /**
     * returns the target_id list of the connected mlt field. if this is a multi-language table
     * then we will always return the parent language records!
     *
     * @param string $sTableName name of the target table or source table
     * @param string|null $sMLTFieldName name of the mlt field
     * @param TCMSField|null $oField Field object form the mlt field. If not set function will load ist for given field name
     *
     * @return string[]
     */
    protected function GetMLTIDListCleared($sTableName, $sMLTFieldName = null, $oField = null)
    {
        $sInternalCacheKey = 'getmltidlist'.$sTableName.$sMLTFieldName;
        $aMatches = $this->GetFromInternalCache($sInternalCacheKey);
        if (is_null($aMatches)) {
            $mltTableName = $this->GetMltTableName($sMLTFieldName, $sTableName);
            if ($this->table != $sTableName) {
                // field exists so this table is the source table
                $selectField = 'target_id';
                $lookupField = 'source_id';
            } else {
                // this table is target table
                $selectField = 'source_id';
                $lookupField = 'target_id';
            }

            $databaseConnection = $this->getDatabaseConnection();
            $quotedMltTableName = $databaseConnection->quoteIdentifier($mltTableName);

            $query = 'SELECT `'.$selectField."` FROM $quotedMltTableName";
            $query .= ' WHERE `'.$lookupField.'` = :lookupFieldValue';

            $aMatches = [];
            $res = $this->ExecuteSQLQueries($query, ['lookupFieldValue' => $this->id]);
            while ($match = $res->fetchAssociative()) {
                $aMatches[] = $match[$selectField];
            }
            $this->SetInternalCache($sInternalCacheKey, $aMatches);
        }

        return $aMatches;
    }

    /**
     * returns the target_id list of the connected mlt field. if this is a multi-language table
     * then we will always return the parent language records!
     *
     * @param string $table - name of the target table or source table or the field name if the field name contains targettable_mlt
     * @param string $sFieldName - name of the mlt field. If field not exists in objects table the function interprets given table as source table
     * @param TCMSField $oField - target field object to check file type
     *
     * @return string[] - all target_ids of the mlt table that connected the the record
     */
    public function GetMLTIdList($table, $sFieldName = null, $oField = null)
    {
        // todo: this method covers a lot of different options - it, and all of its related Methods should be completely refactored
        return $this->GetMLTIDListCleared($table, $sFieldName, $oField);
    }

    /**
     * returns the connected entry for a lookup field
     * $sClassName is the class used to hold the Items.
     *
     * @param string $sFieldName
     * @param string|array|null $sClassName - default string null - assumes an array like array('sClassName'=>'TCMSRecord','sClassSubType'=>'dbobjects','sClassType'=>'Core')
     *
     * @return TCMSRecord
     *
     * @todo find usages of GetLookUp and change calls where $sClassName = array(...). afterwards change trigger_error MSG in LINE 1647
     */
    public function GetLookup($sFieldName, $sClassName = null)
    {
        if (is_null($sClassName)) {
            $sClassName = 'TCMSRecord';
            /** @var $oTableConf TdbCmsTblConf */
            $oTableConfig = $this->GetTableConf();
            $oFieldConfig = $oTableConfig->GetField($sFieldName, $this);
            $sTargetTable = '';
            if (!is_null($oFieldConfig)) {
                if (property_exists($oFieldConfig, 'oDefinition') && !is_null($oFieldConfig->oDefinition)) {
                    $sTargetTable = $oFieldConfig->oDefinition->GetFieldtypeConfigKey('connectedTableName');
                } else {
                    trigger_error('field '.$sFieldName.' in table '.$this->table.' has no definition', E_USER_WARNING);
                }
            } else {
                trigger_error('no field with field name '.$sFieldName.' in table '.$this->table, E_USER_WARNING);
            }
            if (!isset($_SESSION['bAutoObjectGenerationIsRunning'])) {
                if (!empty($sTargetTable)) {
                    $sClassName = TCMSTableToClass::GetClassName(TCMSTableToClass::PREFIX_CLASS, $sTargetTable);
                } else {
                    if (isset($this->sqlData[$sFieldName]) && '_id' == substr($sFieldName, -3)) {
                        $sClassName = TCMSTableToClass::GetClassName(TCMSTableToClass::PREFIX_CLASS, substr($sFieldName, 0, -3));
                    }
                }
            }
        } else {
            if (is_array($sClassName)) {
                $aClassName = $sClassName;
                if (isset($aClassName['sClassName']) && !empty($aClassName['sClassName'])) {
                    $sClassName = $aClassName['sClassName'];
                } else {
                    trigger_error('missing parameters in sClassName array in GetLookup', E_USER_ERROR);
                }
            }
        }

        $sCacheName = 'lookupField_'.$sFieldName.'_'.$sClassName;
        $oLookupRecord = $this->GetFromInternalCache($sCacheName);

        if (is_null($oLookupRecord) && !empty($this->sqlData[$sFieldName])) {
            $foreignTable = substr($sFieldName, 0, -3);

            $cacheName = $foreignTable.'_'.$this->iLanguageId.'_'.$this->sqlData[$sFieldName];
            static $internalCache = [];
            if (array_key_exists($cacheName, $internalCache)) {
                $oLookupRecord = $internalCache[$cacheName];
            } else {
                $oRecord = new $sClassName();
                /* @var $oRecord TCMSRecord */
                $oRecord->SetLanguage($this->iLanguageId);
                $oRecord->table = $foreignTable;
                $oRecord->Load($this->sqlData[$sFieldName]);

                $oLookupRecord = $oRecord;
                $internalCache[$cacheName] = $oRecord;
            }

            $this->SetInternalCache($sCacheName, $oLookupRecord);
        }

        return $oLookupRecord;
    }

    /**
     * returns a record list for the specified property.
     *
     * @param string $sFieldName
     * @param string $sClassName - default string TCMSRecord
     * @param string $sOrderBy - CAUTION! The parameter needs to be MySQL escaped; default cmsident ASC
     * @param array $aFilterConditions - default null - array of fieldname as key and filter condition as value that will be added to the query
     *                                 - may also be a string - in that case it will just be added to the query as "AND ($condition)"
     * @param string $sParentFieldNameInTargetTable - set if the lookup field is not named TABLENAME_id
     *
     * @return TCMSRecordList
     *
     * @deprecated - Tdb objects have a method for every property field - use that
     */
    public function GetProperties($sFieldName, $sClassName = null, $sOrderBy = '`cmsident`', $aFilterConditions = null, $sParentFieldNameInTargetTable = null)
    {
        if (false === $this->sqlData) {
            return new TCMSRecordList();
        }
        $sListClassName = 'TCMSRecordList';
        /** @var $oFieldConfig TCMSFieldPropertyTable */
        $oFieldConfig = ServiceLocator::get('chameleon_system_core.database_access_layer_field_config')->getFieldConfig($this->table, $sFieldName, $this, false);
        $targetTableName = $oFieldConfig->GetPropertyTableNameFrontend();

        if (is_null($sClassName)) {
            $sTargetTable = $oFieldConfig->oDefinition->GetFieldtypeConfigKey('connectedTableName');
            if (!empty($sTargetTable)) {
                $sClassName = TCMSTableToClass::GetClassName(TCMSTableToClass::PREFIX_CLASS, $sTargetTable);
            } else {
                $sClassName = TCMSTableToClass::GetClassName(TCMSTableToClass::PREFIX_CLASS, $targetTableName);
            }
        } else {
            if (is_array($sClassName)) {
                $aClassName = $sClassName;
                if (isset($aClassName['sClassName']) && !empty($aClassName['sClassName'])) {
                    $sClassName = $aClassName['sClassName'];
                } else {
                    trigger_error('missing parameters in sClassName array in GetProperties', E_USER_ERROR);
                }
            }
        }

        if ('Tdb' === substr($sClassName, 0, 3)) {
            $sListClassName = $sClassName.'List';
        }

        $sCacheName = 'propertyField_'.$sFieldName.'_'.$sClassName.'_'.$sOrderBy;

        // add filter conditions to cache key
        if (is_array($aFilterConditions)) {
            foreach ($aFilterConditions as $key => $val) {
                $sCacheName .= '_'.$key.'_'.$val;
            }
            reset($aFilterConditions);
        } elseif (!empty($aFilterConditions)) {
            // is string... add to query
            $sCacheName .= '_'.$aFilterConditions;
        }

        $oPropertyList = $this->GetFromInternalCache($sCacheName);
        if (is_null($oPropertyList)) {
            $linkField = $this->table.'_id';
            if (!is_null($sParentFieldNameInTargetTable)) {
                $linkField = $sParentFieldNameInTargetTable;
            }

            $databaseConnection = $this->getDatabaseConnection();
            $quotedTargetTableName = $databaseConnection->quoteIdentifier($targetTableName);
            $quotedLinkField = $databaseConnection->quoteIdentifier($linkField);
            $query = "SELECT *
                  FROM $quotedTargetTableName
                  WHERE $quotedLinkField = ".$databaseConnection->quote($this->id);

            // add filter conditions to query
            if (!is_null($aFilterConditions) && is_array($aFilterConditions)) {
                foreach ($aFilterConditions as $key => $val) {
                    $quotedKey = $databaseConnection->quoteIdentifier($key);
                    $quotedVal = $databaseConnection->quote($val);
                    $query .= " AND $quotedKey = $quotedVal";
                }
            } elseif (!empty($aFilterConditions)) {
                // is a string
                $query .= " AND ({$aFilterConditions})";
            }

            $query .= ' ORDER BY '.$sOrderBy;

            $oPropertyList = new $sListClassName();
            /* @var $oPropertyList TCMSRecordList */
            $oPropertyList->sTableName = $targetTableName;
            $oPropertyList->SetLanguage($this->iLanguageId);
            $oPropertyList->sTableObject = $sClassName;
            $oPropertyList->Load($query);

            $this->SetInternalCache($sCacheName, $oPropertyList);
        } elseif (!is_null($oPropertyList)) {
            $oPropertyList->GoToStart();
        }

        return $oPropertyList;
    }

    /**
     * returns an array of ids with the connected properties for field sFieldName.
     *
     * @param string $sFieldName
     * @param string $sOrderBy - field name to sort the result
     *
     * @return array
     */
    public function GetPropertiesIdList($sFieldName, $sOrderBy = 'cmsident')
    {
        $aIdList = [];
        $sCacheName = 'propertyField_'.$sFieldName.'__idlist';
        $oPropertyList = $this->GetFromInternalCache($sCacheName);
        if (is_null($oPropertyList)) {
            /** @var $oTableConf TdbCmsTblConf */
            $oTableConfig = $this->GetTableConf();
            /** @var $oFieldConfig TCMSFieldPropertyTable */
            $oFieldConfig = $oTableConfig->GetField($sFieldName, $this);
            $targetTableName = $oFieldConfig->GetPropertyTableNameFrontend();
            $linkField = $this->table.'_id';

            $databaseConnection = $this->getDatabaseConnection();
            $quotedTargetTableName = $databaseConnection->quoteIdentifier($targetTableName);
            $quotedLinkField = $databaseConnection->quoteIdentifier($linkField);

            $query = "SELECT `id`, `cmsident`
                  FROM $quotedTargetTableName
               ";
            $query .= "WHERE $quotedLinkField = ".$databaseConnection->quote($this->id);
            $quotedOrderBy = $databaseConnection->quoteIdentifier($sOrderBy);
            $query .= "ORDER BY $quotedOrderBy";

            $tres = $this->ExecuteSQLQueries($query);
            while ($prop = $tres->fetchAssociative()) {
                $aIdList[] = $prop['id'];
            }
            $this->SetInternalCache($sCacheName, $aIdList);
        }

        return $aIdList;
    }

    /**
     * returns the tree node attached to this record, or null if nothing is attached.
     *
     * @return TCMSTreeNode
     */
    public function GetTreeNode()
    {
        $sCacheName = 'treeNode';
        $oTreeNode = $this->GetFromInternalCache($sCacheName);
        if (is_null($oTreeNode)) {
            $query = 'SELECT `cms_tree`.*
                    FROM `cms_tree`
              INNER JOIN `cms_tree_node` ON `cms_tree`.`id` = `cms_tree_node`.`cms_tree_id`
                   WHERE `cms_tree_node`.`tbl` = '.$this->getDatabaseConnection()->quote($this->table).'

                 ';

            $query .= ' AND `cms_tree_node`.`contid` = '.$this->getDatabaseConnection()->quote($this->id);
            if ($node = $this->ExecuteSQLQueries($query)->fetchAssociative()) {
                $oTreeNode = new TCMSTreeNode();
                $oTreeNode->LoadFromRow($node);
            }

            $this->SetInternalCache($sCacheName, $oTreeNode);
        }

        return $oTreeNode;
    }

    /**
     * returns true if the table exists.
     *
     * @param string $tableName
     * @param bool $bEnableCache - default true
     *
     * @return bool
     */
    public static function TableExists($tableName, $bEnableCache = true)
    {
        static $aTableExistsLookup = [];
        if ($bEnableCache && is_array($aTableExistsLookup) && isset($aTableExistsLookup[$tableName])) {
            $bTableExists = $aTableExistsLookup[$tableName];
        } else {
            $query = 'SHOW TABLES LIKE '.ServiceLocator::get('database_connection')->quote($tableName);
            $res = ServiceLocator::get('database_connection')->executeQuery($query);
            $bTableExists = ($res->rowCount() > 0);
            $aTableExistsLookup[$tableName] = $bTableExists;
        }

        return $bTableExists;
    }

    /**
     * returns true a field exists in a table.
     *
     * @param string $tableName
     * @param string $sFieldName
     *
     * @return bool
     */
    public static function FieldExists($tableName, $sFieldName)
    {
        static $aFieldExistsLookup = [];
        $bExists = false;
        if (!empty($tableName) && !empty($sFieldName)) {
            $sStaticCacheKey = $tableName.'_'.$sFieldName;
            if (is_array($aFieldExistsLookup) && isset($aFieldExistsLookup[$sStaticCacheKey])) {
                $bExists = $aFieldExistsLookup[$sStaticCacheKey];
            } else {
                if (self::TableExists($tableName)) {
                    $query = "SHOW COLUMNS FROM `{$tableName}` LIKE ".ServiceLocator::get('database_connection')->quote($sFieldName);
                    $res = ServiceLocator::get('database_connection')->executeQuery($query);
                    $bExists = ($res->rowCount() > 0);
                    $aFieldExistsLookup[$sStaticCacheKey] = $bExists;
                }
            }
        }

        return $bExists;
    }

    /**
     * restores an object from internal cache. returns null if the object was not found.
     *
     * @param string $varName - cache key
     *
     * @return mixed|null
     */
    protected function GetFromInternalCache($varName)
    {
        $resultPointer = null;
        if (is_array($this->aResultCache) && isset($this->aResultCache[$varName])) {
            $resultPointer = $this->aResultCache[$varName];
        }

        return $resultPointer;
    }

    /**
     * saves object in class internal cache.
     *
     * @param string $varName - key name
     */
    protected function SetInternalCache($varName, $content)
    {
        $this->aResultCache[$varName] = $content;
    }

    /**
     * generate sql for current record as an insert statement.
     *
     * @param array $excludeFields
     *
     * @return string
     */
    public function ExportRowData($excludeFields = [])
    {
        $oTableConf = $this->GetTableConf();
        $oFields = $oTableConf->GetFields($this);

        $databaseConnection = $this->getDatabaseConnection();
        $quotedTableName = $databaseConnection->quoteIdentifier($this->table);
        $quotedId = $databaseConnection->quote($this->id);

        $sql = "INSERT INTO $quotedTableName ";
        $isFirst = true;
        /** @var TCMSField $oField */
        while ($oField = $oFields->Next()) {
            if ($this->isFieldExportable($oField, $excludeFields, $this->sqlData)) {
                $oField->data = $this->sqlData[$oField->name];
                $sqlValue = $oField->GetSQL();
                if (false !== $sqlValue) {
                    if ($isFirst) {
                        $isFirst = false;
                        $sql .= 'SET ';
                    } else {
                        $sql .= ', ';
                    }
                    $quotedFieldName = $databaseConnection->quoteIdentifier($oField->name);
                    $quotedSqlValue = $databaseConnection->quote($sqlValue);
                    $sql .= "$quotedFieldName = $quotedSqlValue";
                }
            }
        }

        $sNewId = $this->id;
        // need to add the id if it is non numeric
        if (is_numeric($this->id)) {
            $sNewId = $databaseConnection->quote(TTools::GetUUID());
            $sql .= ', `id` = '.$sNewId;
        } else {
            $sql .= ", `id` = $quotedId";
        }

        $sql .= ";\n";
        $sql .= 'SET @recordId'.$this->table."='{$sNewId}';\n";

        // now add mlt data
        $oFieldDefinitions = $oTableConf->GetFieldDefinitions(['CMSFIELD_MULTITABLELIST', 'CMSFIELD_MULTITABLELIST_CHECKBOXES']);
        while ($oFieldDef = $oFieldDefinitions->Next()) {
            /** @var $oFieldDef TCMSFieldDefinition */
            $tableName = $this->table.'_'.$oFieldDef->sqlData['name'];
            $quotedSubTableName = $databaseConnection->quoteIdentifier($tableName);
            $query = "SELECT * FROM $quotedSubTableName WHERE `source_id` = $quotedId";
            $mltRecs = $this->ExecuteSQLQueries($query);
            while ($mlt = $mltRecs->fetchAssociative()) {
                $sql .= "INSERT INTO $quotedSubTableName".'        SET `source_id` = @recordId'.$this->table.', `target_id` = '.$databaseConnection->quote($mlt['target_id']).";\n";
            }
        }
        // now insert property records
        $oFieldDefinitions = $oTableConf->GetFieldDefinitions(['CMSFIELD_PROPERTY']);
        $quotedSqlDataTableName = $databaseConnection->quoteIdentifier($oTableConf->sqlData['name'].'_id');
        while ($oFieldDef = $oFieldDefinitions->Next()) {
            /** @var $oFieldDef TCMSFieldDefinition */
            $tableName = $oFieldDef->sqlData['field_default_value'];
            $quotedSubTableName = $databaseConnection->quoteIdentifier($tableName);
            $query = "SELECT * FROM $quotedSubTableName
                   WHERE $quotedSqlDataTableName = $quotedId
                   ";
            $propertyRes = $this->ExecuteSQLQueries($query);
            while ($property = $propertyRes->fetchAssociative()) {
                $oProperty = new self($tableName);
                $property[$oTableConf->sqlData['name'].'_id'] = '##@recordId'.$this->table.'##';
                $oProperty->LoadFromRow($property);
                $propertySQL = $oProperty->ExportRowData();
                $propertySQL = str_replace("'##@recordId".$this->table."##'", '@recordId'.$this->table, $propertySQL);
                $sql .= $propertySQL;
            }
        }

        return $sql;
    }

    private function isFieldExportable(TCMSField $field, array $excludeFields, array $sqlData): bool
    {
        return !in_array($field->name, $excludeFields, true)
            && !$field->isPropertyField
            && !$field->isMLTField
            && array_key_exists($field->name, $sqlData);
    }

    /**
     * returns true if the passed item is the same as this item.
     *
     * @template T of TCMSRecord
     *
     * @param T $oItem
     *
     * @return bool
     */
    public function IsSameAs($oItem)
    {
        $bIsSame = false;
        if (!is_null($oItem)) {
            if (!is_null($this->id) && !is_null($oItem->id)) {
                $bIsSame = ($oItem->id == $this->id);
            } elseif ($this->id != $oItem->id) {
                return false;
            } else {
                $bIsSame = $this->hasSameDataInSqlData($oItem);
            }
        }

        return $bIsSame;
    }

    /**
     * returns true if the objects passed sqlData is identical with that of the current object.
     *
     * @param TCMSRecord $oCompareWith
     *
     * @return bool
     */
    public function hasSameDataInSqlData($oCompareWith)
    {
        $aThisData = $this->sqlData;
        $aItemData = $oCompareWith->sqlData;
        $bIsSame = (is_array($aThisData) && is_array($aItemData));
        if ($bIsSame) {
            // ignore empty entries in both arrays
            foreach (array_keys($aThisData) as $sKey) {
                if (empty($aThisData[$sKey])) {
                    unset($aThisData[$sKey]);
                }
            }
        }
        if (false !== $aItemData) {
            foreach (array_keys($aItemData) as $sKey) {
                if (empty($aItemData[$sKey])) {
                    unset($aItemData[$sKey]);
                }
            }
        }
        $bIsSame = ($bIsSame && (count($aThisData) == count($aItemData)));

        if ($bIsSame) {
            reset($aThisData);
            foreach (array_keys($aThisData) as $fieldName) {
                $bIsSame = ($bIsSame && (is_array(
                    $aItemData
                ) && isset($aItemData[$fieldName])) && $aThisData[$fieldName] == $aItemData[$fieldName]);
            }
        }

        return $bIsSame;
    }

    /**
     * return array with required fields.
     *
     * @return string[]
     */
    public function GetRequiredFields()
    {
        $requiredFields = [];
        $tableConf = $this->GetTableConf();
        $fields = $tableConf->GetFieldDefinitions();
        $fields->GoToStart();
        while ($field = $fields->Next()) {
            /** @var $field TCMSFieldDefinition */
            if ('1' == $field->sqlData['isrequired']) {
                $requiredFields[] = $field->sqlData['name'];
            }
        }

        return $requiredFields;
    }

    /**
     * return the properties of the object as an array of the form classname__property=>value.
     *
     * @return array (does not include objects or arrays)
     */
    public function GetObjectPropertiesAsArray()
    {
        $aData = [];
        $aPropertyList = TTools::GetPublicProperties($this);
        $sClassName = get_class($this);
        foreach ($aPropertyList as $sPropName) {
            if (property_exists($this, $sPropName)) {
                // only allow non object and non arrays
                if (!is_array($this->$sPropName) && !is_object($this->$sPropName)) {
                    $aData[$sClassName.'__'.$sPropName] = $this->$sPropName;
                }
            }
        }

        return $aData;
    }

    /**
     * return sqlData content in the form tablename__fieldname.
     *
     * @param string $sPrefix
     *
     * @return array
     */
    public function GetSQLWithTablePrefix($sPrefix = '')
    {
        $aTmpData = [];
        reset($this->sqlData);
        if (empty($sPrefix)) {
            $sPrefix = $this->table;
        }
        $sPrefix .= '__';
        foreach ($this->sqlData as $sKey => $sVal) {
            $aTmpData[$sPrefix.$sKey] = $sVal;
        }

        return $aTmpData;
    }

    /**
     * clear the internal cache.
     */
    protected function ClearInternalCache()
    {
        $this->aResultCache = [];
    }

    /**
     * if no order is set the function will get the order by query string from backend custom sort field or sorts by MLT sort order if enabled.
     *
     * @param string $sOrderBy
     * @param string $sMLTFieldName
     * @param string $sMLTTableName
     * @param string $sTargetTableName
     * @param string $sSourceTableName
     *
     * @return string
     */
    protected function GetMLTOrderBy($sOrderBy, $sMLTFieldName, $sMLTTableName, $sTargetTableName, $sSourceTableName)
    {
        if (false === empty($sOrderBy)) {
            return $sOrderBy;
        }

        $sourceObjectName = TCMSTableToClass::GetClassName(TCMSTableToClass::PREFIX_CLASS, $sSourceTableName);
        if (true === method_exists($sourceObjectName, 'GetMLTTargetListOrderBy')) {
            $sOrderBy = call_user_func([$sourceObjectName, 'GetMLTTargetListOrderBy'], $sMLTFieldName);
        } else {
            $sOrderBy = $this->getMltOrderByFallback($sMLTFieldName, $sMLTTableName, $sTargetTableName, $sSourceTableName);
        }

        if (false === $sOrderBy) {
            $sOrderBy = '';
        }

        return $sOrderBy;
    }

    /**
     * only called during autoClass generation, because the GetMLTTargetListOrderBy may not exist at this state.
     */
    private function getMltOrderByFallback(string $multiLinkFieldName, string $multiLinkTableName, string $targetTableName, string $sourceTableName): string
    {
        $tableConfiguration = TdbCmsTblConf::GetNewInstance();
        $tableConfiguration->LoadFromField('name', $sourceTableName);
        $multiLinkTableFieldConfiguration = $tableConfiguration->GetFieldDefinition($multiLinkFieldName);

        $isCustomSortOrderEnabled = false;
        if (true === is_object($multiLinkTableFieldConfiguration)) {
            $isCustomSortOrderEnabled = $multiLinkTableFieldConfiguration->GetFieldtypeConfigKey('bAllowCustomSortOrder');
        }

        $databaseConnection = $this->getDatabaseConnection();
        if (true === $isCustomSortOrderEnabled) {
            $quotedMultiLinkTableName = $databaseConnection->quoteIdentifier($multiLinkTableName);

            return "$quotedMultiLinkTableName.`entry_sort` ASC";
        } else {
            $tableConfiguration = TdbCmsTblConf::GetNewInstance();
            $tableConfiguration->LoadFromFieldWithCaching('name', $targetTableName);
            $orderFieldList = $tableConfiguration->GetFieldPropertyOrderFieldsList();

            if (null === $orderFieldList || 0 === $orderFieldList->Length()) {
                return '';
            }

            $orderByQuery = '';
            $quoteTargetTableName = $databaseConnection->quoteIdentifier($targetTableName);
            while ($orderField = $orderFieldList->Next()) {
                if (!empty($orderField->fieldName) && preg_match("/^$quoteTargetTableName.`(.+?)`$/", trim($orderField->fieldName), $matches) && (is_array($this->sqlData) && isset($this->sqlData[$matches[1]]))) {
                    if (!empty($orderByQuery)) {
                        $orderByQuery .= ' , ';
                    }
                    $orderByQuery .= " $quoteTargetTableName.`".$matches[1].'` '.$orderField->fieldSortOrderDirection;
                }
            }

            return $orderByQuery;
        }
    }

    /**
     * Allows you to disable execution of the post load hook.
     *
     * @param bool $bDisableHook - set to false if you want to reenable the hook
     */
    public function DisablePostLoadHook($bDisableHook = true)
    {
        $this->bAllowPostLoadHookExecution = (false == $bDisableHook);
    }

    /**
     * @throws Doctrine\DBAL\Exception
     */
    private function ExecuteSQLQueries(string $query, array $parameter = [], array $types = []): DriverStatement|DriverResultStatement
    {
        return $this->getDatabaseConnection()->executeQuery($query, $parameter, $types);
    }

    /**
     * overloads $this->sqlData[$sFieldName] based on $sLanguagePrefix
     * returns the overloaded value of the field.
     *
     * @param string $sFieldName
     * @param string $sLanguagePrefix
     *
     * @return string
     */
    protected function transformFieldTranslation($sFieldName, $sLanguagePrefix)
    {
        /*
         * This method is called hundreds of times per request, so we need it to be fast. As we cannot do dependency
         * injection in this class, we use the static keyword.
         */
        static $baseLanguageIso = null;
        if (null === $baseLanguageIso) {
            $languageService = self::getLanguageService();
            $baseLanguageIso = $languageService->getLanguageIsoCode($languageService->getCmsBaseLanguageId());
        }

        if (null !== $baseLanguageIso) {
            $fieldNameBaseLanguage = $sFieldName.'__'.$baseLanguageIso;
            if (!isset($this->sqlData[$fieldNameBaseLanguage])) {
                $this->sqlData[$fieldNameBaseLanguage] = $this->sqlData[$sFieldName];
            }
        }

        $sFieldValue = '';
        if (isset($this->sqlData[$sFieldName])) {
            $fieldNameTargetLanguage = $sFieldName.'__'.$sLanguagePrefix;
            if (isset($this->sqlData[$fieldNameTargetLanguage])) {
                if ('' !== $this->sqlData[$fieldNameTargetLanguage]) {
                    $this->sqlData[$sFieldName] = $this->sqlData[$fieldNameTargetLanguage];
                } elseif (!$this->isFieldBasedTranslationFallbackActive() && !TGlobal::IsCMSMode()) {
                    $this->sqlData[$sFieldName] = '';
                }
            }
            $sFieldValue = $this->sqlData[$sFieldName];
        }

        return $sFieldValue;
    }

    public function setFieldBasedTranslationFallbackActive($bVal = true)
    {
        $this->bFieldBasedTranslationFallbackActive = $bVal;
    }

    protected function isFieldBasedTranslationFallbackActive()
    {
        $bVal = CMS_TRANSLATION_FIELD_BASED_EMPTY_TRANSLATION_FALLBACK_TO_BASE_LANGUAGE;
        if (!is_null($this->bFieldBasedTranslationFallbackActive)) {
            $bVal = $this->bFieldBasedTranslationFallbackActive;
        }

        return $bVal;
    }

    public function setDatabaseConnection(Connection $connection)
    {
        $this->databaseConnection = $connection;
    }

    /**
     * @return Connection
     */
    protected function getDatabaseConnection()
    {
        if (null !== $this->databaseConnection) {
            return $this->databaseConnection;
        }

        return ServiceLocator::get('database_connection');
    }

    /**
     * @return LanguageServiceInterface
     */
    protected static function getLanguageService()
    {
        static $languageService;
        if (null === $languageService) {
            $languageService = ServiceLocator::get('chameleon_system_core.language_service');
        }

        return $languageService;
    }

    /**
     * @return TreeServiceInterface
     */
    protected static function getTreeService()
    {
        static $treeService;
        if (null === $treeService) {
            $treeService = ServiceLocator::get('chameleon_system_core.tree_service');
        }

        return $treeService;
    }

    /**
     * @return PageServiceInterface
     */
    protected static function getPageService()
    {
        static $pageService;
        if (null === $pageService) {
            $pageService = ServiceLocator::get('chameleon_system_core.page_service');
        }

        return $pageService;
    }

    /**
     * @return MltFieldUtil
     */
    protected static function getMltFieldUtil()
    {
        static $mltFieldUtil;
        if (null === $mltFieldUtil) {
            $mltFieldUtil = ServiceLocator::get('chameleon_system_core.util.mlt_field');
        }

        return $mltFieldUtil;
    }

    private function getSessionManager(): ChameleonSessionManagerInterface
    {
        return ServiceLocator::get('chameleon_system_core.session.chameleon_session_manager');
    }

    private function getFieldTranslationUtil(): FieldTranslationUtil
    {
        return ServiceLocator::get('chameleon_system_core.util.field_translation');
    }

    private function getRequestStateHash(): ?string
    {
        static $stateHashProvider = null;
        if (null === $stateHashProvider) {
            $stateHashProvider = ServiceLocator::get('chameleon_system_core.request_state_hash_provider');
        }

        return $stateHashProvider->getHash();
    }

    public function getCellFormattingFunction(array $cellConfig, string $cellFormattingFunctionName = ''): ?array
    {
        $tdbClassName = get_class($this);

        if ('' === $cellFormattingFunctionName && 'id' === $cellConfig['db_alias'] && 'ID' === $cellConfig['title']) {
            $cellFormattingFunctionName = 'callBackUuid';
        }

        if ('' === $cellFormattingFunctionName) {
            return null;
        }

        if (false === is_callable([$tdbClassName, $cellFormattingFunctionName])) {
            return null;
        }

        return [$tdbClassName, $cellFormattingFunctionName];
    }

    public static function callBackUuid(string $id)
    {
        return '<span title="'.TGlobal::OutHTML($id).'"><i class="fas fa-fingerprint"></i> '.self::getShortUuid($id).'</span>';
    }

    protected static function getShortUuid(string $uuid)
    {
        if (strlen($uuid) > 8) {
            return substr($uuid, 0, 8);
        }

        return $uuid;
    }

    protected function getCacheService(): CacheInterface
    {
        return ServiceLocator::get('chameleon_system_core.cache');
    }
}
