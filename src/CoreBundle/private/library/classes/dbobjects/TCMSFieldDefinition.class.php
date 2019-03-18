<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Doctrine\DBAL\Connection;
use ChameleonSystem\DatabaseMigration\DataModel\LogChangeDataModel;

class TCMSFieldDefinition extends TCMSRecord
{
    /**
     * the field def holds a config field - this attribute is used to cache that.
     *
     * @var TPkgCmsStringUtilities_ReadConfig
     */
    private $oCacheExtraConfigFieldObject = null;

    public function TCMSFieldDefinition($id = null)
    {
        $table = 'cms_field_conf';
        parent::TCMSRecord($table, $id);
    }

    public function isVirtualField()
    {
        $type = $this->GetFieldType();

        return '' === trim($type->sqlData['mysql_type']);
    }

    /**
     * return a unique id for the table. you can pass either a table name, or an id.
     *
     * @param string $sTableNameOrTableId - either a table name or a table id
     *
     * @return string
     */
    public function GetUIDForTable($sTableNameOrTableId)
    {
        $sUID = '';
        $sTableName = '';
        if (is_numeric($sTableNameOrTableId)) {
            $oTableConf = TdbCmsTblConf::GetNewInstance();
            /** @var $oTableConf TdbCmsTblConf */
            if (!$oTableConf->Load($sTableNameOrTableId)) {
                trigger_error('Unable to find table ID ['.$sTableNameOrTableId.'] in TTools::GetUIDForTable', E_USER_ERROR);
            } else {
                $sTableName = $oTableConf->fieldName;
            }
        } else {
            $sTableName = $sTableNameOrTableId;
        }

        if (!empty($sTableName)) {
            $iMaxCount = 100;
            $sSafeTableName = MySqlLegacySupport::getInstance()->real_escape_string($sTableName);
            $sSafeFieldName = MySqlLegacySupport::getInstance()->real_escape_string($this->sqlData['name']);
            while (empty($sUID) && $iMaxCount > 0) {
                $sTmpUID = TTools::GetUUID("FID{$this->id}-");
                $sQ = 'SELECT * FROM `'.MySqlLegacySupport::getInstance()->real_escape_string($sSafeTableName).'` WHERE `'.MySqlLegacySupport::getInstance()->real_escape_string($sSafeFieldName)."` = '".MySqlLegacySupport::getInstance()->real_escape_string($sTmpUID)."'";
                $rRes = MySqlLegacySupport::getInstance()->query($sQ);
                if (MySqlLegacySupport::getInstance()->num_rows($rRes) < 1) {
                    $sUID = $sTmpUID;
                }
                --$iMaxCount;
            }
            if ($iMaxCount < 1) {
                trigger_error('Unable to find a free UID in '.$sTableName.' for field '.$this->sqlData['name'], E_USER_ERROR);
            }
        }

        return $sUID;
    }

    /**
     * returns the field object for the field.
     *
     * @return TCMSField|null
     */
    public function &GetFieldObject()
    {
        $field = null;
        // check if the field defines a class that overwrites the field type class
        if (!empty($this->sqlData['fieldclass'])) {
            $sClassName = $this->sqlData['fieldclass'];
            $field = new $sClassName();
        } else {
            $oFieldType = $this->GetFieldType();
            if (!is_null($oFieldType)) {
                $sClassName = $oFieldType->sqlData['fieldclass'];
                $field = new $sClassName();
            }
        }
        if (null !== $field) {
            $field->setDatabaseConnection($this->getDatabaseConnection());
        }

        return $field;
    }

    /**
     * returns the field type linked to this field definition.
     *
     * @return TdbCmsFieldType
     */
    public function GetFieldType()
    {
        $accessLayer = \ChameleonSystem\CoreBundle\ServiceLocator::get('chameleon_system_core.database_access_layer_cms_field_type');

        return $accessLayer->getFieldType($this->sqlData['cms_field_type_id']);
    }

    /**
     * returns an array of ids for the groups connected to this field.
     *
     * @return array();
     */
    public function GetPermissionGroups()
    {
        $groups = array();
        $query = "SELECT * FROM `cms_field_conf_cms_usergroup_mlt` WHERE `source_id` = '".MySqlLegacySupport::getInstance()->real_escape_string($this->id)."'";
        $tmp = MySqlLegacySupport::getInstance()->query($query);
        while ($group = MySqlLegacySupport::getInstance()->fetch_assoc($tmp)) {
            $groups[] = $group['target_id'];
        }

        return $groups;
    }

    /**
     * static function that returns all field types that may hold an image.
     *
     * @return array
     */
    public static function GetImageFieldTypes()
    {
        $query = "SELECT `constname` FROM `cms_field_type` WHERE `contains_images` = '1'";
        $databaseconnection = self::getDbConnection();
        $result = $databaseconnection->fetchAll($query);
        $imageFieldTypes = array();
        foreach ($result as $row) {
            $imageFieldTypes[] = $row['constname'];
        }

        return $imageFieldTypes;
    }

    /**
     * return true if the field is a list of checkboxes (like the mlt checkbox field).
     *
     * @return bool
     */
    public function IsCheckboxList()
    {
        return 46 == $this->sqlData['cms_field_type_id'];
    }

    /**
     * return true if the field is a list of checkboxes (like the mlt checkbox field).
     *
     * @return bool
     */
    public function IsPasswordField()
    {
        return 23 == $this->sqlData['cms_field_type_id'] || 29 == $this->sqlData['cms_field_type_id'];
    }

    /**
     * get field type specific config parameter value for given key.
     *
     * @param string $parameterKey
     *
     * @return string|null
     */
    public function GetFieldtypeConfigKey($parameterKey)
    {
        return $this->getFieldExtraConfigObject()->getConfigValue($parameterKey);
    }

    /**
     * return the extra config object (definable in the config via of the field via fieldtype_config.
     *
     * @return TPkgCmsStringUtilities_ReadConfig
     */
    public function getFieldExtraConfigObject()
    {
        if (null === $this->oCacheExtraConfigFieldObject) {
            $this->oCacheExtraConfigFieldObject = new TPkgCmsStringUtilities_ReadConfig($this->sqlData['fieldtype_config']);
        }

        return $this->oCacheExtraConfigFieldObject;
    }

    /**
     * Get array wich shows what action is required on field definition change
     * for translation fields.
     *
     * @return array
     */
    protected function GetTranslateFieldActionList()
    {
        $aChangedFieldAction = array('name' => 'CHANGE', 'cms_field_type_id' => 'DELETE', 'field_default_value' => 'CHANGE', 'length_set' => 'CHANGE');

        return $aChangedFieldAction;
    }

    /**
     * Returns required action on field definiton change or refresh field based translation.
     * Action for translation fields can be NONE, DELETE and CHANGE.
     *
     * @param array $aOriginalData original sql data befor field definition change
     *
     * @return string
     */
    protected function GetActionForTranslationFields($aOriginalData = array())
    {
        $sAction = 'NONE';
        $aChangedFieldAction = $this->GetTranslateFieldActionList();
        if ('1' != $this->sqlData['is_translatable']) {
            $sAction = 'DELETE';
        } elseif (is_array($aOriginalData) && count($aOriginalData) > 0) {
            foreach ($aChangedFieldAction as $sFieldName => $sTransFieldAction) {
                if (array_key_exists($sFieldName, $this->sqlData) && array_key_exists($sFieldName, $aOriginalData) && $this->sqlData[$sFieldName] != $aOriginalData[$sFieldName]) {
                    if ('DELETE' != $sAction) {
                        $sAction = $sTransFieldAction;
                    }
                }
            }
        }

        return $sAction;
    }

    /**
     * Delete translation fields are not in given language array.
     * Returns array for update logging.
     *
     * @param string $sTableName
     * @param string $sBaseFieldName base field name without language extension
     * @param array  $aLanguageArray
     *
     * @return array
     */
    protected function DeleteNotNeededTranslationFields($sTableName, $sBaseFieldName, $aLanguageArray)
    {
        $aQuery = array();
        $sQuery = 'SHOW FIELDS FROM `'.MySqlLegacySupport::getInstance()->real_escape_string($sTableName)."` WHERE field REGEXP '^".MySqlLegacySupport::getInstance()->real_escape_string($sBaseFieldName)."__[a-zA-Z]{2}\$'";
        $oRes = MySqlLegacySupport::getInstance()->query($sQuery);
        while ($aRow = MySqlLegacySupport::getInstance()->fetch_assoc($oRes)) {
            if (!array_key_exists(substr($aRow['Field'], -2), $aLanguageArray)) {
                $aQuery = array_merge($aQuery, $this->DeleteTranslationField($sTableName, $aRow['Field']));
            }
        }

        return $aQuery;
    }

    /**
     * Delete all translation fields for given field name
     * Returns array for update logging.
     *
     * @param string $sTableName
     * @param string $sBaseFieldName base field name without language extension
     * @param array  $aLanguageArray
     *
     * @return array
     */
    protected function DeleteTranslationFields($sTableName, $sBaseFieldName, $aLanguageArray)
    {
        $aQuery = array();
        foreach ($aLanguageArray as $sExtension => $sLanguage) {
            $aQuery = array_merge($aQuery, $this->DeleteTranslationField($sTableName, $sBaseFieldName.'__'.$sExtension));
        }

        return $aQuery;
    }

    /**
     * Deletes one translation field.
     * Returns array for update logging.
     * If field not exists do nothing and return empty array.
     *
     * @param string $sTableName
     * @param string $sFieldName field name to delete with language extension
     *
     * @return array
     */
    protected function DeleteTranslationField($sTableName, $sFieldName)
    {
        $aQuery = array();
        if (TTools::FieldExists($sTableName, $sFieldName)) {
            $query = 'ALTER TABLE `'.MySqlLegacySupport::getInstance()->real_escape_string($sTableName).'` DROP `'.MySqlLegacySupport::getInstance()->real_escape_string($sFieldName).'`';
            MySqlLegacySupport::getInstance()->query($query);
            $aQuery['remove field '.$sFieldName] = $query;
        }

        return $aQuery;
    }

    /**
     * Chnages all translation fields for given field name
     * Returns array for update logging.
     *
     * @param string $sTableName
     * @param string $sBaseFieldName base field name without language extension
     * @param array  $aLanguageArray
     *
     * @return array
     */
    protected function ChangeTranslationFields($sTableName, $sBaseFieldName, $aLanguageArray)
    {
        $aQuery = array();
        foreach ($aLanguageArray as $sLanguageExtension => $sLanguage) {
            $aSubQuery = $this->ChangeTransLationField($sTableName, $sBaseFieldName, $sLanguageExtension, $sLanguage);
            if (0 == count($aSubQuery)) {
                $aSubQuery = $this->AddNewTranslationField($sTableName, $sBaseFieldName, $sLanguageExtension, $sLanguage);
            }
            $aQuery = array_merge($aQuery, $aSubQuery);
        }

        return $aQuery;
    }

    /**
     * Chnages one translation field.
     * Returns array for update logging.
     * If field not exists do nothing and return empty array.
     *
     * @param string $sTableName
     * @param string $sBaseFieldName     base field name without language extension
     * @param string $sLanguageExtension
     * @param string $sLanguage
     *
     * @return array
     */
    protected function ChangeTransLationField($sTableName, $sBaseFieldName, $sLanguageExtension, $sLanguage)
    {
        $aQuery = array();
        if (TTools::FieldExists($sTableName, $sBaseFieldName.'__'.$sLanguageExtension)) {
            $sNewBaseFieldName = $this->sqlData['name'];
            $sQuery = 'SHOW FIELDS FROM `'.MySqlLegacySupport::getInstance()->real_escape_string($sTableName)."` WHERE field = '".MySqlLegacySupport::getInstance()->real_escape_string($sNewBaseFieldName)."'";
            if ($aSourceFieldSQLData = MySqlLegacySupport::getInstance()->fetch_assoc(MySqlLegacySupport::getInstance()->query($sQuery))) {
                $aQuery = $this->ExecuteTranslationFieldAction($sTableName, $sBaseFieldName, $sLanguage, $sLanguageExtension, $aSourceFieldSQLData, 'CHANGE');
            }
        }

        return $aQuery;
    }

    /**
     * Adds all translation fields for given field name
     * Returns array for update logging.
     *
     * @param string $sTableName
     * @param string $sBaseFieldName base field name without language extension
     * @param array  $aLanguageArray
     *
     * @return array
     */
    protected function AddNewTranslationFields($sTableName, $sBaseFieldName, $aLanguageArray)
    {
        $aQuery = array();
        foreach ($aLanguageArray as $sLanguageExtension => $sLanguage) {
            $aQuery = array_merge($aQuery, $this->AddNewTranslationField($sTableName, $sBaseFieldName, $sLanguageExtension, $sLanguage));
        }

        return $aQuery;
    }

    /**
     * Adds one translation field.
     * Returns array for update logging.
     * If field already exists do nothing and return empty array.
     *
     * @param string $sTableName
     * @param string $sBaseFieldName     base field name without language extension
     * @param string $sLanguageExtension
     * @param string $sLanguage
     *
     * @return array
     */
    protected function AddNewTranslationField($sTableName, $sBaseFieldName, $sLanguageExtension, $sLanguage)
    {
        $aQuery = array();
        if (!TTools::FieldExists($sTableName, $sBaseFieldName.'__'.$sLanguageExtension)) {
            $sNewBaseFieldName = $this->sqlData['name'];
            $sQuery = 'SHOW FIELDS FROM `'.MySqlLegacySupport::getInstance()->real_escape_string($sTableName)."` WHERE field = '".MySqlLegacySupport::getInstance()->real_escape_string($sNewBaseFieldName)."'";
            if ($aSourceFieldSQLData = MySqlLegacySupport::getInstance()->fetch_assoc(MySqlLegacySupport::getInstance()->query($sQuery))) {
                $aQuery = $this->ExecuteTranslationFieldAction($sTableName, $sNewBaseFieldName, $sLanguage, $sLanguageExtension, $aSourceFieldSQLData, 'ADD');
            }
        }

        return $aQuery;
    }

    /**
     * Executes ADD, DELETE or CHANGE Action for one Translation field.
     * Returns array for update logging.
     *
     * @param string $sTableName
     * @param string $sBaseFieldName       base field name without language extension
     * @param string $sLanguage
     * @param string $sLanguageExtension
     * @param array  $aSourceFieldSQLData  sql data from base field to copy field definition to translation fields
     * @param string $sSQLAlterTableAction action for translation field (ADD CHANGE)
     *
     * @return array
     */
    protected function ExecuteTranslationFieldAction($sTableName, $sBaseFieldName, $sLanguage, $sLanguageExtension, $aSourceFieldSQLData, $sSQLAlterTableAction)
    {
        $aQuery = array();
        $sQuery = $this->GetTranlationFieldQueryFromOriginalField($sTableName, $sBaseFieldName, $sLanguage, $sLanguageExtension, $aSourceFieldSQLData, $sSQLAlterTableAction);
        MySqlLegacySupport::getInstance()->query($sQuery);
        $aQuery[$sSQLAlterTableAction.' field ['.$sTableName.','.$this->sqlData['name'].'__'.$sLanguageExtension.']'] = $sQuery;
        $sQuery = $this->GetTranslationFieldQueryForClearingNotNeededKeys($sTableName, $sBaseFieldName, $sLanguageExtension, 'MUL');
        if (!empty($sQuery)) {
            MySqlLegacySupport::getInstance()->query($sQuery);
            $aQuery['Drop Index for '.$sBaseFieldName.'__'.$sLanguageExtension] = $sQuery;
        }
        $sQuery = $this->GetTranslationFieldQueryForClearingNotNeededKeys($sTableName, $sBaseFieldName, $sLanguageExtension, 'UNI');
        if (!empty($sQuery)) {
            MySqlLegacySupport::getInstance()->query($sQuery);
            $aQuery['Drop UNIQUE for '.$sBaseFieldName.'__'.$sLanguageExtension] = $sQuery;
        }
        $sQuery = $this->GetTransLationFieldQueryForIndexFromOringinalField($sTableName, $this->sqlData['name'], $sLanguageExtension, $aSourceFieldSQLData);
        if (!empty($sQuery)) {
            MySqlLegacySupport::getInstance()->query($sQuery);
            $aQuery['Add Index for '.$this->sqlData['name'].'__'.$sLanguageExtension] = $sQuery;
        }

        return $aQuery;
    }

    /**
     * Get query to add or change translation field.
     *
     * @param string $sTableName
     * @param string $sBaseFieldName       base field name without language extension
     * @param string $sLanguage
     * @param string $sLanguageExtension
     * @param array  $aSourceFieldSQLData  sql data from base field to copy field definition to translation fields
     * @param string $sSQLAlterTableAction action for translation field (ADD CHANGE)
     *
     * @return string
     */
    protected function GetTranlationFieldQueryFromOriginalField($sTableName, $sBaseFieldName, $sLanguage, $sLanguageExtension, $aSourceFieldSQLData, $sSQLAlterTableAction)
    {
        if ('ADD' == $sSQLAlterTableAction) {
            $sQuery = 'ALTER TABLE `'.MySqlLegacySupport::getInstance()->real_escape_string($sTableName).'` '.MySqlLegacySupport::getInstance()->real_escape_string($sSQLAlterTableAction).' `'.MySqlLegacySupport::getInstance()->real_escape_string($this->sqlData['name'].'__'.$sLanguageExtension).'` '.$aSourceFieldSQLData['Type'];
        } else {
            $sQuery = 'ALTER TABLE `'.MySqlLegacySupport::getInstance()->real_escape_string($sTableName).'` '.MySqlLegacySupport::getInstance()->real_escape_string($sSQLAlterTableAction).' `'.MySqlLegacySupport::getInstance()->real_escape_string($sBaseFieldName.'__'.$sLanguageExtension).'` '.MySqlLegacySupport::getInstance()->real_escape_string($this->sqlData['name'].'__'.$sLanguageExtension).'  '.$aSourceFieldSQLData['Type'];
        }
        if ('NO' == $aSourceFieldSQLData['Null']) {
            $sQuery .= ' NOT NULL';
        }
        if (!is_null($aSourceFieldSQLData['Default'])) {
            $sQuery .= " DEFAULT '".MySqlLegacySupport::getInstance()->real_escape_string($aSourceFieldSQLData['Default'])."'";
        }
        $sQuery .= " COMMENT '".MySqlLegacySupport::getInstance()->real_escape_string('translation '.$sLanguage)."'";

        return $sQuery;
    }

    /**
     * Get query to clean index or unique translation field if exists an not needed.
     *
     * @param string $sTableName
     * @param string $sBaseFieldName     base field name without language extension
     * @param string $sLanguageExtension
     * @param string $sKey               (MUL = index, UNI = unique)
     *
     * @return string
     */
    protected function GetTranslationFieldQueryForClearingNotNeededKeys($sTableName, $sBaseFieldName, $sLanguageExtension, $sKey)
    {
        $sQuery = '';
        $sIndexQuery = 'SHOW fields FROM `'.MySqlLegacySupport::getInstance()->real_escape_string($sTableName)."` WHERE `field` = '".MySqlLegacySupport::getInstance()->real_escape_string($this->sqlData['name'].'__'.$sLanguageExtension)."' AND `key` ='".MySqlLegacySupport::getInstance()->real_escape_string($sKey)."'";
        $oMysqlRes = MySqlLegacySupport::getInstance()->query($sIndexQuery);
        if (MySqlLegacySupport::getInstance()->num_rows($oMysqlRes) > 0) {
            $sQuery = 'ALTER TABLE `'.MySqlLegacySupport::getInstance()->real_escape_string($sTableName).'` DROP INDEX `'.MySqlLegacySupport::getInstance()->real_escape_string($sBaseFieldName.'__'.$sLanguageExtension).'` ';
        }

        return $sQuery;
    }

    /**
     * Get query to add new index or unique for tanslation field.
     *
     * @param string $sTableName
     * @param string $sBaseFieldName      base field name without language extension
     * @param string $sLanguageExtension
     * @param array  $aSourceFieldSQLData sql data from base field
     *
     * @return string
     */
    protected function GetTransLationFieldQueryForIndexFromOringinalField($sTableName, $sBaseFieldName, $sLanguageExtension, $aSourceFieldSQLData)
    {
        $sQuery = '';
        if ('MUL' == $aSourceFieldSQLData['Key']) {
            $sQuery = 'ALTER TABLE `'.MySqlLegacySupport::getInstance()->real_escape_string($sTableName).'` ADD INDEX ( `'.MySqlLegacySupport::getInstance()->real_escape_string($sBaseFieldName.'__'.$sLanguageExtension).'` )';
        } elseif ('UNI' == $aSourceFieldSQLData['Key']) {
            $sQuery = 'ALTER TABLE `'.MySqlLegacySupport::getInstance()->real_escape_string($sTableName).'` ADD  UNIQUE ( `'.MySqlLegacySupport::getInstance()->real_escape_string($sBaseFieldName.'__'.$sLanguageExtension).'` )';
        }

        return $sQuery;
    }

    /**
     * Updates translation fields for one field.
     * If field definition data has changed, set original data.
     *
     * @param array $originalData SQL data from field definition before change
     */
    public function UpdateFieldTranslationKeys($originalData = array())
    {
        if (null === $tableName = $this->getTableNameFromConfigId()) {
            return;
        }

        $baseFieldName = $this->sqlData['name'];
        if (is_array($originalData) && array_key_exists('name', $originalData)) {
            $baseFieldName = $originalData['name'];
        }
        $languageArray = TdbCmsConfig::GetInstance()->GetFieldBasedTranslationLanguageArray();
        $queries = $this->DeleteNotNeededTranslationFields($tableName, $baseFieldName, $languageArray);
        switch ($this->GetActionForTranslationFields($originalData)) {
            case 'CHANGE':
                $queries = array_merge($queries, $this->ChangeTranslationFields($tableName, $baseFieldName, $languageArray));
                break;
            case 'DELETE':
                $queries = array_merge($queries, $this->DeleteTranslationFields($tableName, $baseFieldName, $languageArray));
                if (1 == $this->sqlData['is_translatable']) {
                    $queries = array_merge($queries, $this->AddNewTranslationFields($tableName, $baseFieldName, $languageArray));
                }
                break;
            case 'NONE':
                $queries = array_merge($queries, $this->ChangeTranslationFields($tableName, $baseFieldName, $languageArray));
                break;
        }
        if (count($queries) > 0) {
            $logChangeDataModels = array();
            foreach ($queries as $query) {
                $logChangeDataModels[] = new LogChangeDataModel($query);
            }
            TCMSLogChange::WriteTransaction($logChangeDataModels);
        }
    }

    /**
     * @return string|null
     */
    private function getTableNameFromConfigId()
    {
        $databaseConnection = $this->getDatabaseConnection();
        $tableName = $databaseConnection->fetchColumn('SELECT `name` FROM `cms_tbl_conf` WHERE `id` = :id', array(
                'id' => $this->sqlData['cms_tbl_conf_id'],
            ));

        if (false === $tableName) {
            return null;
        }

        return $tableName;
    }

    public function isTranslatable(): bool
    {
        return '1' === $this->sqlData['is_translatable'];
    }

    /**
     * returns the field name - respects the current language setting.
     *
     * @param string|null $sLanguageID
     *
     * @return string
     */
    public function GetRealEditFieldName($sLanguageID = null)
    {
        $sTargetFieldName = $this->sqlData['name'];

        if (false === $this->isTranslatable()) {
            return $sTargetFieldName;
        }

        $language = null;
        if (null === $sLanguageID) {
            $oUser = &TCMSUser::GetActiveUser();
            if (null !== $oUser) {
                $language = $oUser->GetCurrentEditLanguageObject();
            }
        } else {
            $language = self::getLanguageService()->getLanguage($sLanguageID);
        }

        if (null === $language) {
            return $sTargetFieldName;
        } else {
            /**
             * We already made sure that the field is translatable, and therefore the return value will always be a string.
             */
            return $this->GetEditFieldNameForLanguage($language);
        }
    }

    /**
     * Get field name for language. If no field exists for given language return false.
     *
     * @param TdbCmsLanguage $oLanguage
     *
     * @return string|bool false if the value is not translatable, else the translated field name
     */
    public function GetEditFieldNameForLanguage($oLanguage)
    {
        if (false === $this->isTranslatable()) {
            return false;
        }

        if (TdbCmsConfig::GetInstance()->fieldTranslationBaseLanguageId === $oLanguage->id) {
            return $this->sqlData['name'];
        } else {
            return $this->sqlData['name'].'__'.$oLanguage->sqlData['iso_6391'];
        }
    }

    /**
     * @return Connection
     */
    private static function getDbConnection()
    {
        return \ChameleonSystem\CoreBundle\ServiceLocator::get('database_connection');
    }
}
