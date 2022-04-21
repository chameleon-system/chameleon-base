<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use ChameleonSystem\AutoclassesBundle\CacheWarmer\AutoclassesCacheWarmer;
use ChameleonSystem\CoreBundle\ServiceLocator;
use ChameleonSystem\DatabaseMigration\DataModel\LogChangeDataModel;
use Doctrine\DBAL\DBALException;

/**
 * manages creation and deletion of tables.
 *
/**/
class TCMSTableWriter extends TCMSTableEditor
{
    /**
     * the sql name of the generated table.
     *
     * @var string
     */
    protected $_sqlTableName = null;

    protected $sOldTblName = null;

    protected $aOldData = null;

    /**
     * @var string|null
     */
    private $oldTableComment;

    protected function LoadDataFromDatabase()
    {
        parent::LoadDataFromDatabase();
        if ($this->oTable && is_array($this->oTable->sqlData) && array_key_exists('name', $this->oTable->sqlData)) {
            $this->_sqlTableName = $this->oTable->sqlData['name'];
        }
    }

    /**
     * {@inheritdoc}
     */
    public function Save(&$postData, $bDataIsInSQLForm = false)
    {
        $this->aOldData = $this->oTable->sqlData;
        $this->sOldTblName = $this->aOldData['name'];
        $this->oldTableComment = $this->getTableComment($this->aOldData['translation'], $this->aOldData['notes']);

        // NOTE this is doubled in PrepareDataForSave() but necessary for backwards compatibility (field was set before Save()).
        $this->_sqlTableName = $this->getNewNormalizedTableName($this->sOldTblName, $postData['name']);

        parent::Save($postData);
    }

    private function getTableComment(string $translation, string $notes): string
    {
        return substr($translation.': '.$notes, 0, 255);
    }

    private function getNewNormalizedTableName(string $oldName, string $desiredName): string
    {
        $name = strtolower($desiredName);

        if ($oldName !== $name) {
            $name = $this->GetValidTableName($name);
        }

        return $name;
    }

    /**
     * {@inheritdoc}
     */
    protected function PrepareDataForSave($postData)
    {
        $postData = parent::PrepareDataForSave($postData);

        $postData['name'] = $this->getNewNormalizedTableName($this->sOldTblName, $postData['name']);

        return $postData;
    }

    /**
     *  filters invalid chars and mysql reserved names from table name and prevents doubled table names.
     *
     * @param string $sTableName
     *
     * @return string
     */
    protected function GetValidTableName($sTableName = 'data_new_table')
    {
        $sOriginalTableNameFromPost = $sTableName;
        $sTableName = trim($sTableName);
        $sTableName = strtolower($sTableName);
        $sTableName = preg_replace('/[^a-z-_0-9]/', '', $sTableName); // allow only characters allowed by MYSQL

        // filter mysql reserved words
        $aReservedMySQLWords = array('ACCESSIBLE', 'ADD', 'ALL', 'ALTER', 'ANALYZE', 'AND', 'AS', 'ASC', 'ASENSITIVE', 'BEFORE', 'BETWEEN', 'BIGINT', 'BINARY', 'BLOB', 'BOTH', 'BY', 'CALL', 'CASCADE', 'CASE', 'CHANGE', 'CHAR', 'CHARACTER', 'CHECK', 'COLLATE', 'COLUMN', 'CONDITION', 'CONSTRAINT', 'CONTINUE', 'CONVERT', 'CREATE', 'CROSS', 'CURRENT_DATE', 'CURRENT_TIME', 'CURRENT_TIMESTAMP', 'CURRENT_USER', 'CURSOR', 'DATABASE', 'DATABASES', 'DAY_HOUR', 'DAY_MICROSECOND', 'DAY_MINUTE', 'DAY_SECOND', 'DEC', 'DECIMAL', 'DECLARE', 'DEFAULT', 'DELAYED', 'DELETE', 'DESC', 'DESCRIBE', 'DETERMINISTIC', 'DISTINCT', 'DISTINCTROW', 'DIV', 'DOUBLE', 'DROP', 'DUAL', 'EACH', 'ELSE', 'ELSEIF', 'ENCLOSED', 'ESCAPED', 'EXISTS', 'EXIT', 'EXPLAIN', 'FALSE', 'FETCH', 'FLOAT', 'FLOAT4', 'FLOAT8', 'FOR', 'FORCE', 'FOREIGN', 'FROM', 'FULLTEXT', 'GENERAL', 'GRANT', 'GROUP', 'HAVING', 'HIGH_PRIORITY', 'HOUR_MICROSECOND', 'HOUR_MINUTE', 'HOUR_SECOND', 'IF', 'IGNORE', 'IGNORE_SERVER_IDS', 'IN', 'INDEX', 'INFILE', 'INNER', 'INOUT', 'INSENSITIVE', 'INSERT', 'INT', 'INT1', 'INT2', 'INT3', 'INT4', 'INT8', 'INTEGER', 'INTERVAL', 'INTO', 'IS', 'ITERATE', 'JOIN', 'KEY', 'KEYS', 'KILL', 'LEADING', 'LEAVE', 'LEFT', 'LIKE', 'LIMIT', 'LINEAR', 'LINES', 'LOAD', 'LOCALTIME', 'LOCALTIMESTAMP', 'LOCK', 'LONG', 'LONGBLOB', 'LONGTEXT', 'LOOP', 'LOW_PRIORITY', 'MASTER_HEARTBEAT_PERIOD', 'MASTER_SSL_VERIFY_SERVER_CERT', 'MATCH', 'MAXVALUE', 'MEDIUMBLOB', 'MEDIUMINT', 'MEDIUMTEXT', 'MIDDLEINT', 'MINUTE_MICROSECOND', 'MINUTE_SECOND', 'MOD', 'MODIFIES', 'NATURAL', 'NOT', 'NO_WRITE_TO_BINLOG', 'NULL', 'NUMERIC', 'ON', 'OPTIMIZE', 'OPTION', 'OPTIONALLY', 'OR', 'ORDER', 'OUT', 'OUTER', 'OUTFILE', 'PRECISION', 'PRIMARY', 'PROCEDURE', 'PURGE', 'RANGE', 'READ', 'READS', 'READ_WRITE', 'REAL', 'REFERENCES', 'REGEXP', 'RELEASE', 'RENAME', 'REPEAT', 'REPLACE', 'REQUIRE', 'RESIGNAL', 'RESTRICT', 'RETURN', 'REVOKE', 'RIGHT', 'RLIKE', 'SCHEMA', 'SCHEMAS', 'SECOND_MICROSECOND', 'SELECT', 'SENSITIVE', 'SEPARATOR', 'SET', 'SHOW', 'SIGNAL', 'SLOW[b]', 'SMALLINT', 'SPATIAL', 'SPECIFIC', 'SQL', 'SQLEXCEPTION', 'SQLSTATE', 'SQLWARNING', 'SQL_BIG_RESULT', 'SQL_CALC_FOUND_ROWS', 'SQL_SMALL_RESULT', 'SSL', 'STARTING', 'STRAIGHT_JOIN', 'TABLE', 'TERMINATED', 'THEN', 'TINYBLOB', 'TINYINT', 'TINYTEXT', 'TO', 'TRAILING', 'TRIGGER', 'TRUE', 'UNDO', 'UNION', 'UNIQUE', 'UNLOCK', 'UNSIGNED', 'UPDATE', 'USAGE', 'USE', 'USING', 'UTC_DATE', 'UTC_TIME', 'UTC_TIMESTAMP', 'VALUES', 'VARBINARY', 'VARCHAR', 'VARCHARACTER', 'VARYING', 'WHEN', 'WHERE', 'WHILE', 'WITH', 'WRITE', 'XOR', 'YEAR_MONTH', 'ZEROFILL', 'GENERAL', 'IGNORE_SERVER_IDS', 'MASTER_HEARTBEAT_PERIOD', 'MAXVALUE', 'RESIGNAL', 'SIGNAL', 'SLOW');
        if (in_array(strtoupper($sTableName), $aReservedMySQLWords)) {
            $sTableName = $sTableName.'_mysql_res_name';
        }

        $foundName = true;
        $count = 0;
        while ($foundName) {
            $tmpName = $sTableName;
            if ($count > 0) {
                $tmpName .= $count;
            }
            $foundName = TGlobal::TableExists($tmpName);
            ++$count;
            if (!$foundName && $sTableName != $tmpName) {
                $sTableName = $tmpName;
                /** @var $oMessageManager TCMSMessageManager */
                $oMessageManager = TCMSMessageManager::GetInstance();
                $sConsumerName = TCMSTableEditorManager::MESSAGE_MANAGER_CONSUMER;
                $oMessageManager->AddMessage($sConsumerName, 'TABLEEDITOR_TABLE_ALREADY_EXISTS', array('sTableName' => $sOriginalTableNameFromPost, 'sNewTableName' => $sTableName));
            }
        }

        return $sTableName;
    }

    /**
     * {@inheritdoc}
     */
    protected function PostSaveHook(&$oFields, &$oPostTable)
    {
        parent::PostSaveHook($oFields, $oPostTable);

        $newTableName = $oPostTable->sqlData['name'];

        // reset table list cache
        if (array_key_exists('_listObjCache', $_SESSION)) {
            $_SESSION['_listObjCache'] = array();
        }

        $this->getAutoclassesCacheWarmer()->updateTableById($this->sId);

        $this->changeTableName($this->sOldTblName, $newTableName);
        $requestedEngine = $this->getTableEngine($oPostTable->sqlData);
        $this->changeTableEngine($newTableName, $requestedEngine);
        $this->changeTableComment($newTableName, $this->oldTableComment);
    }

    /**
     * @param string $oldTableName
     * @param string $newTableName
     *
     * @throws DBALException
     */
    private function changeTableName(string $oldTableName, string $newTableName): void
    {
        if ($oldTableName === $newTableName) {
            return;
        }

        $databaseConnection = $this->getDatabaseConnection();

        $query = sprintf(
            'ALTER TABLE %s RENAME %s',
            $databaseConnection->quoteIdentifier($oldTableName),
            $databaseConnection->quoteIdentifier($newTableName)
        );
        $databaseConnection->executeQuery($query);

        $aQuery = array(new LogChangeDataModel($query));
        TCMSLogChange::WriteTransaction($aQuery);

        $this->_RenameRelatedTables($oldTableName, $newTableName);
    }

    /**
     * @param string $tableName
     * @param string $oldTableComment
     *
     * @throws DBALException
     */
    private function changeTableComment(string $tableName, string $oldTableComment): void
    {
        $newTableComment = $this->getTableComment($this->oTable->sqlData['translation'], $this->oTable->sqlData['notes']);

        if ($oldTableComment === $newTableComment) {
            return;
        }

        $databaseConnection = $this->getDatabaseConnection();

        $query = sprintf('ALTER TABLE %s COMMENT %s', $databaseConnection->quoteIdentifier($tableName), $databaseConnection->quote($newTableComment));
        $databaseConnection->executeQuery($query);

        $aQuery = array(new LogChangeDataModel($query));
        TCMSLogChange::WriteTransaction($aQuery);
    }

    /**
     * Renames related tables like mlt tables for source and target connections.
     *
     * @param string $sOldName
     * @param string $sNewName
     */
    public function _RenameRelatedTables($sOldName, $sNewName)
    {
        $this->RenameRelatedTablesSource($sOldName, $sNewName);
        $this->RenameRelatedTablesTarget($sOldName, $sNewName);
    }

    /**
     * Renames related tables like mlt tables for mlt fields in this table.
     *
     * @param string $sOldTableName
     * @param string $sNewTableName
     */
    protected function RenameRelatedTablesSource($sOldTableName, $sNewTableName)
    {
        if (!empty($sNewTableName) && !empty($sNewTableName)) {
            $sQuery = "SELECT `cms_field_conf`.* FROM `cms_field_conf`
                      INNER JOIN `cms_tbl_conf` ON `cms_tbl_conf`.`id` = `cms_field_conf`.`cms_tbl_conf_id`
                      INNER JOIN `cms_field_type` ON `cms_field_type`.`id` = `cms_field_conf`.`cms_field_type_id`
                           WHERE `cms_tbl_conf`.`id` = '".MySqlLegacySupport::getInstance()->real_escape_string($this->sId)."'
                             AND (`cms_field_type`.`base_type` = 'mlt' OR `cms_field_type`.`constname` = 'CMSFIELD_DOCUMENTS')";
            $oFieldDefinitionList = TdbCmsFieldConfList::GetList($sQuery);
            $aQuery = array();
            while ($oFieldDefinition = $oFieldDefinitionList->Next()) {
                $oMLTField = $oFieldDefinition->GetFieldObject();
                $oMLTField->sTableName = $sOldTableName;
                $oMLTField->name = $oFieldDefinition->sqlData['name'];
                $oMLTField->oDefinition = $oFieldDefinition;
                $sMltTableName = $oMLTField->GetMLTTableName();
                $newMLTName = $sNewTableName.substr($sMltTableName, strlen($sOldTableName));
                $query = 'ALTER TABLE `'.MySqlLegacySupport::getInstance()->real_escape_string($sMltTableName).'` RENAME `'.MySqlLegacySupport::getInstance()->real_escape_string($newMLTName).'`';
                MySqlLegacySupport::getInstance()->query($query);
                $aQuery[] = new LogChangeDataModel($query);
            }
            TCMSLogChange::WriteTransaction($aQuery);
        }
    }

    /**
     * Renames related tables like mlt tables and fields for mlt fields in other tables are connected to this table.
     *
     * @param string $sOldTableName
     * @param string $sNewTableName
     */
    protected function RenameRelatedTablesTarget($sOldTableName, $sNewTableName)
    {
        if (!empty($sNewTableName) && !empty($sNewTableName)) {
            $sQuery = "SELECT `cms_field_conf`.* FROM `cms_field_conf`
                    INNER JOIN `cms_field_type` ON `cms_field_type`.`id` = `cms_field_conf`.`cms_field_type_id`
                    INNER JOIN `cms_tbl_conf` ON `cms_tbl_conf`.`id` = `cms_field_conf`.`cms_tbl_conf_id`
                         WHERE (`cms_field_conf`.`name` REGEXP '^".MySqlLegacySupport::getInstance()->real_escape_string($sOldTableName)."([1-9]?_mlt$|[1-9]*$|_mlt$)'
                            OR `cms_field_conf`.`fieldtype_config` REGEXP 'connectedTableName\s*=\s*".MySqlLegacySupport::getInstance()->real_escape_string($sOldTableName)."(\s*|$)')
                           AND `cms_field_type`.`base_type` ='mlt'
                           AND `cms_tbl_conf`.`id` != '".MySqlLegacySupport::getInstance()->real_escape_string($this->sId)."'";
            $tables = MySqlLegacySupport::getInstance()->query($sQuery);
            while ($table = MySqlLegacySupport::getInstance()->fetch_assoc($tables)) {
                $oTableManager = TTools::GetTableEditorManager('cms_field_conf', $table['id']);
                $oTableManager->Save($this->ChangeMLTFieldConfigConnectedTableNameData($table, $sOldTableName, $sNewTableName));
            }
        }
    }

    /**
     * Changes given mlt field config sql data array for new table name.
     *
     * @param array  $aFieldConfigSqlData sql data of cms_field_conf
     * @param string $sOldTableName
     * @param string $sNewTableName
     *
     * @return array
     */
    protected function ChangeMLTFieldConfigConnectedTableNameData($aFieldConfigSqlData, $sOldTableName, $sNewTableName)
    {
        $sPatter = '#connectedTableName\s*=\s*'.$sOldTableName.'#';
        $sReplace = 'connectedTableName='.$sNewTableName;
        $sNewFieldConfig = preg_replace($sPatter, $sReplace, $aFieldConfigSqlData['fieldtype_config']);
        if (0 == strcmp($sNewFieldConfig, $aFieldConfigSqlData['fieldtype_config'])) {
            $sPatter = '#^'.$sOldTableName.'([1-9]?_mlt$|[1-9]*$|_mlt$)#';
            $sReplace = $sNewTableName;
            $sNewFieldName = preg_replace($sPatter, $sReplace, $aFieldConfigSqlData['name']);
            $aFieldConfigSqlData['name'] = $sNewFieldName;
        } else {
            $aFieldConfigSqlData['fieldtype_config'] = $sNewFieldConfig;
        }
        $aFieldConfigSqlData['bTargetTableChangeForMLTField'] = true;

        return $aFieldConfigSqlData;
    }

    /**
     * {@inheritdoc}
     */
    protected function GetCustomMenuItems()
    {
        parent::GetCustomMenuItems();

        /** @var $oMenuItem TCMSTableEditorMenuItem */
        $oMenuItem = new TCMSTableEditorMenuItem();
        $oMenuItem->sDisplayName = 'SQL Export';
        $oMenuItem->sIcon = 'fas fa-file-export';
        $aParams = array();
        $aParams['pagedef'] = 'tableeditor';
        $aParams['tableid'] = $this->oTableConf->id;
        $aParams['id'] = $this->oTable->id;
        $aParams['_fnc'] = 'GetTableDDLExport';
        $aParams['module_fnc[contentmodule]'] = 'ExecuteAjaxCall';
        $aParams['_noModuleFunction'] = 'true';

        $sURL = TTools::GetArrayAsURLForJavascript($aParams);
        $oMenuItem->sOnClick = "GetAjaxCall('".PATH_CMS_CONTROLLER.'?'.$sURL."', DisplayAjaxTextarea);";
        $this->oMenuItems->AddItem($oMenuItem);

        $this->oMenuItems->RemoveItem('sItemKey', 'copy');

        /** @var $oSaveButtonItemFound TIterator */
        $oSaveButtonItemFound = $this->oMenuItems->FindItemsWithProperty('sItemKey', 'save');
        $oSaveButtonItem = $oSaveButtonItemFound->Current();
        $oSaveButtonItem->sOnClick = "ExecutePostCommand('Save');";

        $this->oMenuItems->UpdateOrAddItem($oSaveButtonItem, 'sItemKey');
    }

    /**
     * overwrite the insert to create the actual table.
     */
    public function Insert()
    {
        $sTableName = $this->GetValidTableName();

        $query = 'CREATE TABLE `'.MySqlLegacySupport::getInstance()->real_escape_string($sTableName)."` (
                  `id` CHAR( 36 ) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL ,
                  `cmsident` INT UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'Key is used so that records can be easily identified in Chameleon',
                  PRIMARY KEY ( `id` ),
                  UNIQUE (`cmsident`)
                ) ENGINE = InnoDB";
        MySqlLegacySupport::getInstance()->query($query);
        $sqlError = MySqlLegacySupport::getInstance()->error();
        if (!empty($sqlError)) {
            $msg = "Error creating Table: <br>\n".htmlspecialchars($query)."<br>\n".'Error: '.htmlspecialchars($sqlError);
            trigger_error($msg, E_USER_ERROR);
        } else {
            // save the table name to a class variable so it can be used
            // in the _OverwriteDefaults function
            $aQuery = array(new LogChangeDataModel($query));
            TCMSLogChange::WriteTransaction($aQuery);

            $this->_sqlTableName = $sTableName;
            parent::Insert();
        }
    }

    /**
     * overwrite the name field with the generated table name.
     *
     * @param TIterator $oFields
     */
    public function _OverwriteDefaults(&$oFields)
    {
        if (!is_null($this->_sqlTableName)) {
            $oFields->GoToStart();
            while ($oField = &$oFields->Next()) {
                /** @var $oField TCMSField */
                if ('name' == $oField->name) {
                    $oField->data = $this->_sqlTableName;
                }
            }
            $oFields->GoToStart();
        }
    }

    /**
     * note that property tables will NOT be deleted (to prevent the delete from accidentally removing tables which may
     * still be required in some other context (a table may be a property of more than one parent for example).
     *
     * @param int|null $sId
     */
    public function Delete($sId = null)
    {
        $oClassWriter = new TCMSTableToClass($this->getFileManager(), $this->getAutoclassesDir());
        $oClassWriter->Load($this->sId);
        $oClassWriter->Delete();

        parent::Delete($sId);

        // attempt to delete the table from the database.
        $query = 'DROP TABLE `'.MySqlLegacySupport::getInstance()->real_escape_string($this->_sqlTableName).'`';
        MySqlLegacySupport::getInstance()->query($query);
        $sErrorMessage = MySqlLegacySupport::getInstance()->error();
        if (!empty($sErrorMessage)) {
            trigger_error('SQL Error: '.$sErrorMessage, E_USER_WARNING);
        }
        $aQuery = array(new LogChangeDataModel($query));
        TCMSLogChange::WriteTransaction($aQuery);
    }

    /**
     * {@inheritdoc}
     */
    public function DeleteConnectedRecordReferences()
    {
        parent::DeleteConnectedRecordReferences();

        $this->deleteMultiTableRecordReferencesForDeletedTable($this->_sqlTableName);
    }

    protected function deleteMultiTableRecordReferencesForDeletedTable(string $tableName)
    {
        $this->deleteMultiTableRecordReferences($tableName);
    }

    /**
     * {@inheritdoc}
     */
    public function Copy($postData, $bNoConversion = false)
    {
        // we do not allow tables to be copied... so no code here
    }

    /**
     * {@inheritdoc}
     */
    public function DatabaseCopy($languageCopy = false, $aOverloadedFields = array(), $bCopyAllLanguages = false)
    {
        // we do not allow tables to be copied... so no code here
    }

    /**
     * {@inheritdoc}
     */
    public function DefineInterface()
    {
        parent::DefineInterface();
        $this->methodCallAllowed[] = 'GetTableDDLExport';
    }

    /**
     * generates a SQL export of the current table
     * does not include records, only table create.
     *
     * @return string
     */
    public function GetTableDDLExport()
    {
        $oTableConf = new TCMSTableConf();
        $oTableConf->Load($this->sId);

        return $oTableConf->ExportTable();
    }

    /**
     * @param array $postData
     *
     * @return string
     */
    protected function getTableEngine($postData)
    {
        $defaultEngine = isset($postData['engine']) && !empty($postData['engine']) ? ($postData['engine']) : 'InnoDB';

        if (isset($postData['dbobject_extend_class'])) {
            $sExtends = $postData['dbobject_extend_class'];

            if (true === is_subclass_of($sExtends, 'TCMSRecordWritable') || 'TCMSRecordWritable' === $sExtends) {
                $defaultEngine = 'InnoDB';
            }
        }

        return $defaultEngine;
    }

    /**
     * @param string $tableName
     *
     * @return string
     *
     * @throws DBALException
     */
    protected function getRealTableEngine($tableName)
    {
        $query = 'SELECT `engine`
                    FROM INFORMATION_SCHEMA.TABLES
                   WHERE `table_schema` = :databaseName
                     AND `table_name` = :tableName';

        $databaseConnection = $this->getDatabaseConnection();
        $engine = $databaseConnection->fetchColumn($query, [
            'databaseName' => $databaseConnection->getDatabase(),
            'tableName' => $tableName,
        ]);
        if (false === $engine) {
            $engine = 'InnoDB';
        }

        return $engine;
    }

    /**
     * @param string $tableName
     * @param string $newEngine
     *
     * @throws DBALException
     */
    protected function changeTableEngine($tableName, $newEngine)
    {
        $realEngine = $this->getRealTableEngine($tableName);
        if (strtolower($realEngine) !== strtolower($newEngine)) {
            $databaseConnection = $this->getDatabaseConnection();
            $query = sprintf('ALTER TABLE %s ENGINE %s', $databaseConnection->quoteIdentifier($tableName), $newEngine);
            $databaseConnection->executeQuery($query);

            TCMSLogChange::WriteTransaction(array(new LogChangeDataModel($query)));

            $newTableEngine = $this->getRealTableEngine($tableName);
            $this->SaveField('engine', $newTableEngine, false);
        }
    }

    /**
     * @return AutoclassesCacheWarmer
     */
    private function getAutoclassesCacheWarmer()
    {
        return ServiceLocator::get('chameleon_system_autoclasses.cache_warmer');
    }

    /**
     * @return IPkgCmsFileManager
     */
    private function getFileManager()
    {
        return ServiceLocator::get('chameleon_system_core.filemanager');
    }

    /**
     * @return string
     */
    private function getAutoclassesDir()
    {
        return ServiceLocator::getParameter('chameleon_system_autoclasses.cache_warmer.autoclasses_dir');
    }
}
