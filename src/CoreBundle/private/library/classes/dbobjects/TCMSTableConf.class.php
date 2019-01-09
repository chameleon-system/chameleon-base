<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use ChameleonSystem\CoreBundle\Service\LanguageServiceInterface;

/**
 * the Table metadata manager.
 * db table: cms_tbl_conf.
 */
class TCMSTableConf extends TCMSRecord
{
    /**
     * @param int|null $id
     */
    public function TCMSTableConf($id = null)
    {
        parent::TCMSRecord('cms_tbl_conf', $id);
    }

    /**
     * returns objects that manages the list display.
     *
     * @param null|string $listClassName
     * @param string      $listClassLocation DEPRECATED - default Core
     * @param string      $sListClassPath    DEPRECATED - default TCMSListManager
     *
     * @return TCMSListManager
     *
     * @todo find usages and refactor calls
     */
    public function &GetListObject($listClassName = null, $listClassLocation = 'Core', $sListClassPath = 'TCMSListManager')
    {
        /** @var $oGlobal TGlobal */
        $oGlobal = TGlobal::instance();
        /** @var $oList TCMSListManagerFullGroupTable */
        $oList = null;

        if (!is_null($listClassName)) {
            $oList = new $listClassName();
        } elseif ('cms_media' == $this->sqlData['name']) {
            if ($oGlobal->UserDataExists('sRestrictionField') && '_mlt' == substr($oGlobal->GetUserData('sRestrictionField'), -4)) {
                $oList = new TCMSListManagerImagedatabaseMLT();
            /** @var $oList TCMSListManagerImagedatabaseMLT */
            } else {
                $oList = new TCMSListManagerImagedatabase();
                /** @var $oList TCMSListManagerImagedatabase */
            }
        } elseif ('cms_tpl_page' == $this->sqlData['name']) {
            $oList = new TCMSListManagerWebpages();
        /** @var $oList TCMSListManagerFullGroupTable */
        } elseif ($oGlobal->UserDataExists('sRestrictionField') && '_mlt' == substr($oGlobal->GetUserData('sRestrictionField'), -4)) {
            $oList = new TCMSListManagerMLT();
        /** @var $oList TCMSListManagerMLT */
        } else {
            $oList = new TCMSListManagerFullGroupTable();
            /** @var $oList TCMSListManagerFullGroupTable */
        }

        if ($oGlobal->UserDataExists('sRestrictionField')) {
            $oList->sRestrictionField = $oGlobal->GetUserData('sRestrictionField');
            $oList->sRestriction = $oGlobal->GetUserData('sRestriction');
        }
        if ($oGlobal->UserDataExists('fieldCount')) {
            $oList->fieldCount = $oGlobal->GetUserData('fieldCount');
        }

        $oList->Init($this);

        return $oList;
    }

    /**
     * returns mlt list object (object that shows the NOT currently selected mlt entries.
     *
     * @param string $sTableName - name of the MLT field
     *
     * @return TCMSListManager
     */
    public static function &GetMLTListObject($sTableName)
    {
        $oList = null;
        // get the table conf - just in case the there is an mlt list
        $oTableConf = TdbCmsTblConf::GetNewInstance();
        $mltFieldHelper = self::getMltFieldUtil();
        $sTableName = $mltFieldHelper->getRealTableName($sTableName);
        $sClassName = null;
        $oMLTListExtension = false;
        if ($oTableConf->LoadFromField('name', $sTableName)) {
            $oMLTListExtension = $oTableConf->GetCmsTblListExtension('mltsearch');
        }
        if ($oMLTListExtension) {
            $sClassName = $oMLTListExtension->fieldClassname;
        } else {
            switch ($sTableName) {
                case 'cms_media':
                    $sClassName = 'TCMSListManagerImageMLTList';

                    break;
                default:
                    $sClassName = 'TCMSListManagerMLTList';

                    break;
            }
        }

        return $oTableConf->GetListObject($sClassName);
    }

    /**
     * return false if the extension with the name $sExtensionName does not exist, else returns extension.
     *
     * @param string $sExtensionName
     *
     * @return TdbCmsTblListClass
     */
    public function GetCmsTblListExtension($sExtensionName)
    {
        $sKey = 'sCmsTblListExtension-'.$sExtensionName;
        $oExtension = $this->GetFromInternalCache($sKey);
        if (is_null($oExtension)) {
            $oExtension = false;
            $query = "SELECT `cms_tbl_list_class`.*
                    FROM `cms_tbl_list_class`
                   WHERE `cms_tbl_list_class`.`cms_tbl_conf_id` = '".MySqlLegacySupport::getInstance()->real_escape_string($this->id)."'
                     AND `cms_tbl_list_class`.`name` = '".MySqlLegacySupport::getInstance()->real_escape_string($sExtensionName)."'
                 ";
            if ($aTmp = MySqlLegacySupport::getInstance()->fetch_assoc(MySqlLegacySupport::getInstance()->query($query))) {
                $oExtension = TdbCmsTblListClass::GetNewInstance();
                $oExtension->LoadFromRow($aTmp);
            }
            $this->SetInternalCache($sKey, $oExtension);
        }

        return $oExtension;
    }

    /**
     * returns iterator of field objects. if loadDefaults is set, then the fields
     * will be initialized with the default values from the database...
     *
     * @param TCMSRecord $oTableRow
     * @param bool       $loadDefaults
     * @param bool       $bDoNotUseAutoObjects - set to true if you want to prevent the class from using auto objects
     *
     * @return TIterator
     */
    public function &GetFields(&$oTableRow, $loadDefaults = false, $bDoNotUseAutoObjects = false)
    {
        $languageService = self::getLanguageService();

        $oFieldDefinition = &$this->GetFieldDefinitions(array(), $bDoNotUseAutoObjects);
        $oFields = new TIterator();
        while ($oFieldDef = $oFieldDefinition->next()) {
            /** @var $oFieldDef TdbCmsFieldConf|TCMSFieldDefinition */
            $oField = &$oFieldDef->GetFieldObject();

            if (null === $oField) {
                continue;
            }

            $oField->sTableName = $this->sqlData['name'];
            $oField->recordId = null;
            if ($oTableRow) {
                $oField->recordId = $oTableRow->id;
            }
            $oField->name = $oFieldDef->sqlData['name'];
            $oField->oDefinition = $oFieldDef;

            $bAllowAddingField = false;
            $oField->oTableRow = $oTableRow;
            /** @var $oFieldType TdbCmsFieldType */
            $oFieldType = $oFieldDef->GetLookup('cms_field_type_id');
            if (empty($oFieldType->sqlData['mysql_type'])) { // handle field less field types (e.g. fields, that only exist as config, but not in database)
                $bAllowAddingField = true;
                $oGlobal = TGlobal::instance();
                $oField->data = $oGlobal->GetUserData($oField->name);
            } else {
                if ($loadDefaults) {
                    $oField->data = $oFieldDef->sqlData['field_default_value'];
                    $bAllowAddingField = true;
                } elseif ($oTableRow && is_array($oTableRow->sqlData) && array_key_exists($oField->name, $oTableRow->sqlData)) {
                    if (array_key_exists($oField->name.'-original', $oTableRow->sqlData)) {
                        $oField->data = $oTableRow->sqlData[$oField->name.'-original'];
                    } else {
                        // Standard case
                        // Try to find the correct field name for the data in $oTableRow with respect to the language

                        $data = $this->getDataForCurrentLanguage($oFieldDef, $oTableRow->sqlData, $languageService);

                        if (null === $data) {
                            $data = $oTableRow->sqlData[$oField->name];
                        }

                        $oField->data = $data;
                    }
                    $bAllowAddingField = true;
                }
            }

            if ($bAllowAddingField) {
                $oFields->AddItem($oField);
            }
        }

        return $oFields;
    }

    /**
     * @param TdbCmsFieldConf|TCMSFieldDefinition $fieldDefinition
     * @param array                               $sqlData
     * @param LanguageServiceInterface            $languageService
     *
     * @return mixed
     */
    private function getDataForCurrentLanguage($fieldDefinition, array $sqlData, LanguageServiceInterface $languageService)
    {
        $languageId = $this->GetLanguage();

        if (null === $languageId) {
            return null;
        }

        $language = $languageService->getLanguage($languageId);

        if (null === $language) {
            return null;
        }

        $fieldNameForLanguage = $fieldDefinition->GetEditFieldNameForLanguage($language);

        if (false === $fieldNameForLanguage || false === \array_key_exists($fieldNameForLanguage, $sqlData)) {
            // could be a Save() - containing only mono-language data
            return null;
        }

        return $sqlData[$fieldNameForLanguage];
    }

    /**
     * returns the TCMSField object of a field.
     *
     * @param string     $sFieldName
     * @param TCMSRecord $oTableRow
     * @param bool       $loadDefaults
     *
     * @return TCMSField
     */
    public function &GetField($sFieldName, &$oTableRow, $loadDefaults = false)
    {
        $oField = null;
        /** @var $oFieldDef TCMSFieldDefinition */
        $oFieldDef = $this->GetFieldDefinition($sFieldName);
        if (!is_null($oFieldDef)) {
            $oField = $oFieldDef->GetFieldObject();
            $oField->sTableName = $this->sqlData['name'];
            if (!is_null($oTableRow)) {
                $oField->recordId = $oTableRow->id;
            }
            $oField->name = $oFieldDef->sqlData['name'];
            if ($loadDefaults) {
                $oField->data = $oFieldDef->sqlData['field_default_value'];
            } elseif (null !== $oTableRow && is_array($oTableRow->sqlData) && array_key_exists($oField->name, $oTableRow->sqlData)) {
                $languageService = self::getLanguageService();

                $data = $this->getDataForCurrentLanguage($oFieldDef, $oTableRow->sqlData, $languageService);

                if (null === $data) {
                    $data = $oTableRow->sqlData[$oField->name];
                }

                $oField->data = $data;
            }
            $oField->oDefinition = $oFieldDef;
            $oField->oTableRow = $oTableRow;
        }

        return $oField;
    }

    /**
     * returns an iterator of field definitions that match the types given in aFieldTypes
     * (aFieldTypes = `constname` in cms_field_type).
     *
     * @param array $aFieldTypes
     * @param bool  $bDoNotUseAutoObjects - set to true if you want to prevent the class from using auto objects
     *
     * @return TIterator
     */
    public function &GetFieldDefinitions($aFieldTypes = array(), $bDoNotUseAutoObjects = false)
    {
        $cacheName = '_oFieldDefinition'.serialize($aFieldTypes);
        if ($bDoNotUseAutoObjects) {
            $cacheName .= 'noAutoObjects';
        }
        $oFieldDefinitions = &$this->GetFromInternalCache($cacheName);
        if (is_null($oFieldDefinitions)) {
            $databaseConnection = $this->getDatabaseConnection();
            $quotedId = $databaseConnection->quote($this->id);

            if (count($aFieldTypes) > 0) {
                $fieldTypeString = implode(',', array_map(array($databaseConnection, 'quote'), $aFieldTypes));
                $query = "SELECT `cms_field_conf`.*
                      FROM `cms_field_conf`
                INNER JOIN `cms_field_type` ON `cms_field_conf`.`cms_field_type_id` = `cms_field_type`.`id`
                     WHERE `cms_field_conf`.`cms_tbl_conf_id` = $quotedId
                       AND `cms_field_type`.`constname` IN ($fieldTypeString)
                  ORDER BY `cms_field_conf`.`position`";
            } else {
                $query = "SELECT `cms_field_conf`.*
                      FROM `cms_field_conf`
                     WHERE `cms_field_conf`.`cms_tbl_conf_id` = $quotedId
                  ORDER BY `cms_field_conf`.`position`";
            }
            if ($bDoNotUseAutoObjects) {
                $oFieldDefinitions = new TCMSRecordList();
                $oFieldDefinitions->sTableName = 'cms_field_conf';
                $oFieldDefinitions->sTableObject = 'TCMSFieldDefinition';
                $oFieldDefinitions->Load($query);
            } else {
                $oFieldDefinitions = &TdbCmsFieldConfList::GetList($query);
            }
            $oFieldDefinitions->SetLanguage($this->getLanguageForDefinition());

            $this->SetInternalCache($cacheName, $oFieldDefinitions);
        }

        $oFieldDefinitions->GoToStart();

        return $oFieldDefinitions;
    }

    /**
     * Returns the language that should be set for field definition objects. As the definitions specify the language in
     * which e.g. help texts are displayed, we set it to the current language, independent of the language of the field.
     * Otherwise these texts would be displayed in the current edit language.
     *
     * @return null|string
     */
    private function getLanguageForDefinition()
    {
        $language = self::getLanguageService()->getActiveLanguageId();
        if (null === $language || '' === $language) {
            $language = $this->iLanguageId;
        }

        return $language;
    }

    /**
     * returns a fielddefinition for a given field.
     *
     * @param string $fieldName the sql name of the field
     *
     * @return TdbCmsFieldConf|TCMSFieldDefinition
     */
    public function GetFieldDefinition($fieldName)
    {
        $cacheName = $fieldName.'_'.$this->id;
        static $internalCache = array();
        if (array_key_exists($cacheName, $internalCache)) {
            $fieldDefinition = $internalCache[$cacheName];
        } else {
            $query = "SELECT * FROM `cms_field_conf`
                     WHERE `name`='".MySqlLegacySupport::getInstance()->real_escape_string($fieldName)."'
                       AND `cms_tbl_conf_id` = '".MySqlLegacySupport::getInstance()->real_escape_string($this->id)."'";
            $fieldDefinition = null;
            if ($row = MySqlLegacySupport::getInstance()->fetch_assoc(MySqlLegacySupport::getInstance()->query($query))) {
                $fieldDefinition = TdbCmsFieldConf::GetNewInstance();
                // fallback to base class - needed during DB autoClass generation
                if (is_null($fieldDefinition)) {
                    $fieldDefinition = new TCMSFieldDefinition();
                }
                $fieldDefinition->SetLanguage($this->iLanguageId);
                $fieldDefinition->LoadFromRow($row);
            }
            $internalCache[$cacheName] = $fieldDefinition;
        }

        return $fieldDefinition;
    }

    /**
     * returns the fieldname/sql name of the "name" field.
     *
     * @return string
     */
    public function GetNameColumn()
    {
        if (!empty($this->sqlData['name_column'])) {
            return $this->sqlData['name_column'];
        } else {
            return 'name';
        }
    }

    /**
     * returns the fieldname/sql name of the "display" field.
     *
     * @return string
     */
    public function GetDisplayColumn()
    {
        if (!empty($this->sqlData['display_column'])) {
            return $this->sqlData['display_column'];
        } else {
            return $this->GetNameColumn();
        }
    }

    /**
     * returns the name of the callback function to use for the namefield (or null if not defined).
     *
     * @return string
     */
    public function GetNameFieldCallbackFunction()
    {
        $callbackFunction = null;
        if (is_array($this->sqlData) && array_key_exists('name_column_callback', $this->sqlData) && !empty($this->sqlData['name_column_callback'])) {
            $callbackFunction = $this->sqlData['name_column_callback'];
            TGlobal::LoadCallbackFunction($callbackFunction);
        }

        return $callbackFunction;
    }

    /**
     * returns the name of the callback function to use for the displayfield, if not
     * defined, then it will return the callback of the namefield.
     *
     * @return string
     */
    public function GetDisplayFieldCallbackFunction()
    {
        $callbackFunction = null;
        if (is_array($this->sqlData) && array_key_exists('display_column_callback', $this->sqlData) && !empty($this->sqlData['display_column_callback'])) {
            $callbackFunction = $this->sqlData['display_column_callback'];
            TGlobal::LoadCallbackFunction($callbackFunction);
        } else {
            $callbackFunction = $this->GetNameFieldCallbackFunction();
        }

        return $callbackFunction;
    }

    /**
     * Export current table (no content, just definition).
     *
     * @return string - sql dump
     */
    public function ExportTable()
    {
        static $aExportedTables = array();
        $sql = '';
        if (!in_array($this->sqlData['name'], $aExportedTables)) {
            $aExportedTables[] = $this->sqlData['name'];
            // get table create statement..
            $sql .= 'CREATE TABLE `'.MySqlLegacySupport::getInstance()->real_escape_string($this->sqlData['name'])."` (
                    `id` CHAR( 36 ) NOT NULL ,
                    `cmsident` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'Key is used so that records can be easily identified in Chameleon',
                    PRIMARY KEY ( `id` ) ,
                    UNIQUE (`cmsident`)
                  );\n";
            // add fields...

            $oFields = &$this->GetFields($this, true);
            while ($oField = $oFields->Next()) {
                /** @var $oField TCMSField */
                $sql .= $oField->CreateFieldDefinition(true, $oField);
                $sql .= $oField->CreateFieldIndex(true);
                $sql .= $oField->CreateRelatedTables(true);
                // create table field insert...
            }

            $sql .= $this->ExportRowData();

            // get property tables
            // now insert property records
            $oFieldDefinitions = &$this->GetFieldDefinitions(array('CMSFIELD_PROPERTY'));
            while ($oFieldDef = $oFieldDefinitions->Next()) {
                /** @var $oFieldDef TCMSFieldDefinition */
                $tableName = $oFieldDef->sqlData['field_default_value'];
                $oPropertyTableConf = new self();
                /** @var $oPropertyTableConf TCMSTableConf */
                $oPropertyTableConf->LoadFromField('name', $tableName);
                $sql .= $oPropertyTableConf->ExportTable();
            }
        }

        return $sql;
    }

    /**
     * fetches the TdbFooBaa class name and loads it for current table config record.
     *
     * @param string|null $recordID - record to load
     *
     * @return TCMSRecord
     */
    public function GetTableObjectInstance($recordID = null)
    {
        $sClassName = TCMSTableToClass::GetClassName('Tdb', $this->sqlData['name']);
        /**
         * @var TCMSRecord $oTdbDataTmp
         */
        $oTdbDataTmp = new $sClassName();
        if (!is_null($recordID)) {
            $oTdbDataTmp->Load($recordID);
        }

        return $oTdbDataTmp;
    }
}
