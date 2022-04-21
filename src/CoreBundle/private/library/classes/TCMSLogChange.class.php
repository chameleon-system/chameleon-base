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
use ChameleonSystem\AutoclassesBundle\DataAccess\AutoclassesRequestCacheDataAccess;
use ChameleonSystem\AutoclassesBundle\Handler\TPkgCoreAutoClassHandler_TPkgCmsClassManager;
use ChameleonSystem\CoreBundle\Service\LanguageServiceInterface;
use ChameleonSystem\CoreBundle\ServiceLocator;
use ChameleonSystem\CoreBundle\UpdateManager\StripVirtualFieldsFromQuery;
use ChameleonSystem\CoreBundle\Util\FieldTranslationUtil;
use ChameleonSystem\DatabaseMigration\Counter\MigrationCounterManagerInterface;
use ChameleonSystem\DatabaseMigration\DataModel\LogChangeDataModel;
use ChameleonSystem\DatabaseMigration\Query\MigrationQueryData;
use ChameleonSystem\DatabaseMigration\Query\QueryInterface;
use ChameleonSystem\DatabaseMigrationBundle\Bridge\Chameleon\Recorder\MigrationRecorder;
use ChameleonSystem\DatabaseMigrationBundle\Bridge\Chameleon\Recorder\MigrationRecorderStateHandler;
use ChameleonSystem\ViewRenderer\Exception\DataAccessException;
use ChameleonSystem\ViewRenderer\SnippetChain\SnippetChainModifier;
use Doctrine\Common\Collections\ExpressionBuilder;
use Doctrine\DBAL\DBALException;
use Doctrine\DBAL\Driver\Statement;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpKernel\KernelInterface;

/**
 * Provides a facade for operations required for recording and executing migration scripts.
 */
class TCMSLogChange
{
    const INFO_MESSAGE_LEVEL_INFO = 'INFO';
    const INFO_MESSAGE_LEVEL_ERROR = 'ERROR';
    const INFO_MESSAGE_LEVEL_WARNING = 'WARNING';
    const INFO_MESSAGE_LEVEL_TODO = 'TODO';

    /**
     * @param string $bundleName
     *
     * @throws TCMSConfigException
     */
    public static function deleteUpdateCounter($bundleName)
    {
        self::getMigrationCounterManager()->deleteMigrationCounter($bundleName);
    }

    /**
     * write a database change to the change log.
     *
     * @param LogChangeDataModel[] $dataModels
     */
    public static function WriteTransaction(array $dataModels)
    {
        if (self::getMigrationRecorderStateHandler()->isDatabaseLoggingActive()) {
            $migrationRecorder = self::getMigrationRecorder();
            $filePointer = $migrationRecorder->startTransation(null, self::getCurrentBuildNumber());
            $migrationRecorder->writeQueries($filePointer, $dataModels);
            $migrationRecorder->endTransaction($filePointer);
        }
    }

    /**
     * write a database change to the change log.
     *
     * @param string $sName       - name of the transaction
     * @param array  $phpCommands - queries to write
     */
    public static function WriteSqlTransactionWithPhpCommands($sName, array $phpCommands)
    {
        if (self::getMigrationRecorderStateHandler()->isDatabaseLoggingActive()) {
            $migrationRecorder = self::getMigrationRecorder();
            $filePointer = $migrationRecorder->startTransation(null, self::getCurrentBuildNumber());
            fwrite($filePointer, "/* {$sName} */\n");
            foreach ($phpCommands as $command) {
                fwrite($filePointer, $command."\n");
            }

            $migrationRecorder->endTransaction($filePointer);
        }
    }

    /**
     * @return string
     */
    private static function getCurrentBuildNumber()
    {
        return self::getMigrationRecorderStateHandler()->getCurrentBuildNumber();
    }

    /**
     * returns true if the transactionnr is bigger than the databaseversion of the database
     * sets the database version to the new transaction Nr.
     *
     * @param int    $buildNumber
     * @param string $bundleName
     * @param string $counterDescription was used to create new counters @deprecated since 6.2.0 - no longer used.
     *
     * @return bool
     *
     * @deprecated since 6.2.0 - no longer required. Updates are executed if and only if they did not run before, so no
     * further manual check is required.
     */
    public static function AllowTransaction($buildNumber, $bundleName, $counterDescription = '')
    {
        return false === TCMSUpdateManager::GetInstance()->isUpdateAlreadyProcessed($bundleName, $buildNumber);
    }

    /**
     * fetches the id for a tablename.
     *
     * @param string $sTableName
     * @param bool   $bForceLoad
     *
     * @return int
     */
    public static function GetTableId($sTableName, $bForceLoad = false)
    {
        return TTools::GetCMSTableId($sTableName, $bForceLoad);
    }

    /**
     * returns the id of a field.
     *
     * @param int    $tableId
     * @param string $sFieldName
     *
     * @return string
     */
    public static function GetTableFieldId($tableId, $sFieldName)
    {
        $fieldID = '';
        $query = "SELECT `id` FROM `cms_field_conf` WHERE `cms_tbl_conf_id` = '".MySqlLegacySupport::getInstance()->real_escape_string($tableId)."' AND `name` = '".MySqlLegacySupport::getInstance()->real_escape_string($sFieldName)."'";
        if ($tmp = MySqlLegacySupport::getInstance()->fetch_assoc(MySqlLegacySupport::getInstance()->query($query))) {
            $fieldID = $tmp['id'];
        }
        if (empty($fieldID)) {
            $logger = self::getLogger();
            $logger->critical(
                sprintf('UNABLE TO FIND FIELD %s for TABLE %s', $sFieldName, $tableId),
                [
                    'sFieldName' => $sFieldName,
                    'tableId' => $tableId,
                ]
            );

            trigger_error(self::escapeJSOutput("UNABLE TO FIND FIELD '{$sFieldName}' for TABLE [{$tableId}] in FILE [".__FILE__.'] ON LINE: '.__LINE__), E_USER_ERROR);
        }

        return $fieldID;
    }

    /**
     * return id of field type for codename passed.
     *
     * @param string $sFieldTypeCodename
     *
     * @return int
     */
    public static function GetFieldType($sFieldTypeCodename)
    {
        $sFieldId = 0;
        $sFieldTypeCodename = trim($sFieldTypeCodename);
        $query = "SELECT * FROM `cms_field_type` WHERE `constname` = '".MySqlLegacySupport::getInstance()->real_escape_string($sFieldTypeCodename)."'";
        if ($aType = MySqlLegacySupport::getInstance()->fetch_assoc(MySqlLegacySupport::getInstance()->query($query))) {
            $sFieldId = $aType['id'];
        } else {
            self::DisplayErrorMessage("ERROR: Unable to find field type [{$sFieldTypeCodename}] in TCMSLogChange::GetFieldType");
        }

        return $sFieldId;
    }

    /**
     * @param string $sMessage
     */
    public static function DisplayErrorMessage($sMessage)
    {
        self::getLogger()->warning($sMessage);

        self::addInfoMessage($sMessage, self::INFO_MESSAGE_LEVEL_ERROR);
    }

    /**
     * adds a Message to Update-Manager.
     *
     * @param string $sMessage The Message to show
     * @param string $sLevel   (optional) use one of TCMSLogChange::INFO_MESSAGE_LEVEL_*
     */
    public static function addInfoMessage($sMessage, $sLevel = self::INFO_MESSAGE_LEVEL_INFO)
    {
        TCMSUpdateManager::GetInstance()->addUpdateMessage($sMessage, $sLevel);
    }

    /**
     * @param string $tableId
     * @param string $sFieldName
     *
     * @return string
     *
     * @throws ErrorException
     * @throws TPkgCmsException_Log
     */
    public static function GetTableListFieldId($tableId, $sFieldName)
    {
        $fieldID = '';
        $query = "SELECT `id` FROM `cms_tbl_display_list_fields` WHERE `cms_tbl_conf_id` = '".MySqlLegacySupport::getInstance()->real_escape_string($tableId)."' AND `name` = '".MySqlLegacySupport::getInstance()->real_escape_string($sFieldName)."'";
        if ($tmp = MySqlLegacySupport::getInstance()->fetch_assoc(MySqlLegacySupport::getInstance()->query($query))) {
            $fieldID = $tmp['id'];
        }
        if (empty($fieldID)) {
            $logger = self::getLogger();
            $logger->critical(
                sprintf('UNABLE TO FIND FIELD %s for TABLE %s', $sFieldName, $tableId),
                [
                    'sFieldName' => $sFieldName,
                    'tableId' => $tableId,
                ]
            );
            trigger_error(self::escapeJSOutput("UNABLE TO FIND FIELD '{$sFieldName}' for TABLE [{$tableId}] in FILE [".__FILE__.'] ON LINE: '.__LINE__), E_USER_ERROR);
        }

        return $fieldID;
    }

    /**
     * @param string $sTableid
     * @param string $sFieldAlias
     *
     * @return string
     *
     * @throws ErrorException
     * @throws TPkgCmsException_Log
     */
    public static function GetTableListFieldIdFromAlias($sTableid, $sFieldAlias)
    {
        $fieldID = '';
        $query = "SELECT `id` FROM `cms_tbl_display_list_fields` WHERE `cms_tbl_conf_id` = '".MySqlLegacySupport::getInstance()->real_escape_string($sTableid)."' AND `db_alias` = '".MySqlLegacySupport::getInstance()->real_escape_string($sFieldAlias)."'";
        if ($tmp = MySqlLegacySupport::getInstance()->fetch_assoc(MySqlLegacySupport::getInstance()->query($query))) {
            $fieldID = $tmp['id'];
        }
        if (empty($fieldID)) {
            $logger = self::getLogger();
            $logger->critical(
                sprintf('UNABLE TO FIND DISPLAY FIELD %s for TABLE %s', $sFieldAlias, $sTableid),
                [
                    'sFieldName' => $sFieldAlias,
                    'tableId' => $sTableid,
                ]
            );

            trigger_error(self::escapeJSOutput("UNABLE TO FIND DISPLAY FIELD '{$sFieldAlias}' for TABLE [{$sTableid}] in FILE [".__FILE__.'] ON LINE: '.__LINE__), E_USER_ERROR);
        }

        return $fieldID;
    }

    /**
     * @param string $tableId
     *
     * @return string
     */
    public static function getTableName($tableId)
    {
        $query = 'SELECT `name` FROM cms_tbl_conf WHERE `id` = :tableConfId';

        return self::getDatabaseConnection()->fetchColumn($query, array(
            'tableConfId' => $tableId,
        ));
    }

    /**
     * @param string $fieldId
     *
     * @return string
     */
    public static function getFieldConstantName($fieldId)
    {
        $query = 'SELECT `constname` FROM cms_field_type WHERE `id` = :fieldConfId';

        return self::getDatabaseConnection()->fetchColumn($query, array(
            'fieldConfId' => $fieldId,
        ));
    }

    /**
     * executes a log query.
     *
     * @param string $query
     * @param int    $line
     * @param bool   $bInsertId - add id to statement if it is an insert without id
     *
     * @return Statement
     *
     * @deprecated use RunQuery with bound parameters instead
     */
    public static function _RunQuery($query, $line, $bInsertId = true)
    {
        /** @var $stripNonExistingFields StripVirtualFieldsFromQuery */
        $stripNonExistingFields = \ChameleonSystem\CoreBundle\ServiceLocator::get('chameleon_system_core.update_manager_strip_virtual_fields_from_query');
        $query = $stripNonExistingFields->stripNonExistingFields($query);

        $sOriginalQuery = $query;
        $sCommand = substr($query, 0, 11);
        $sCommand = strtolower($sCommand);
        if ('insert into' === $sCommand && $bInsertId) {
            // get table name
            $sTmp = trim(substr($query, strlen($sCommand)));
            $aTmp = explode(' ', $sTmp);
            $sTableName = str_replace('`', '', $aTmp[0]);
            $sTableName = trim($sTableName);

            $sStrippedQuery = str_replace(' ', '', $query);
            $iPosIdFound = strpos($sStrippedQuery, '`id`');
            $iRealPosId = strpos($sStrippedQuery, '`id`=');
            if (TCMSRecord::FieldExists($sTableName, 'cmsident') && (false === $iPosIdFound || (false !== $iPosIdFound && "'" === substr($sStrippedQuery, $iPosIdFound + 4, 1))) && !$iRealPosId) {
                $bDoesNotExists = false;
                while (!$bDoesNotExists) {
                    $sId = TTools::GetUUID();
                    $sTmpQuery = "SELECT * FROM `{$sTableName}` WHERE `id` = '".MySqlLegacySupport::getInstance()->real_escape_string($sId)."'";
                    $tRes = MySqlLegacySupport::getInstance()->query($sTmpQuery);
                    if (MySqlLegacySupport::getInstance()->num_rows($tRes) < 1) {
                        $bDoesNotExists = true;
                    }
                }
                $query .= ", `id` = '".MySqlLegacySupport::getInstance()->real_escape_string($sId)."'";
            }
        }
        $res = MySqlLegacySupport::getInstance()->query($query);
        $err = MySqlLegacySupport::getInstance()->error();
        $logger = self::getLogger();
        if (!empty($err)) {
            $logger->error(
                sprintf('SQL error in line %s: %s (Query %s)', $line, $err, $query),
                [
                    'originalQuery' => $sOriginalQuery,
                    'queryUsed' => $query,
                    'bInsertId' => $bInsertId,
                ]
            );
            TCMSUpdateManager::GetInstance()->addErrorQuery($query, $line, $err);
        } else {
            $logger->info(
                sprintf('Query Successfully executed in line %s: %s', $line, $query),
                [
                    'originalQuery' => $sOriginalQuery,
                    'bInsertId' => $bInsertId,
                ]
            );
        }

        return $res;
    }

    /**
     * @param int    $line
     * @param string $sql
     * @param array  $parameter - parameter list (indexed or named)
     * @param array  $types     - if nothing is passed, then string type is assumed
     */
    public static function RunQuery($line, $sql, array $parameter = array(), array $types = null)
    {
        $db = \ChameleonSystem\CoreBundle\ServiceLocator::get('database_connection');

        $logger = self::getLogger();
        try {
            $stm = $db->prepare($sql);
            foreach ($parameter as $name => $value) {
                $type = (is_array($types) && isset($types[$name])) ? $types[$name] : null;
                $stm->bindValue($name, $value, $type);
            }
            $stm->execute();
            $logger->info(
                sprintf('Query Successfully executed in line %s: %s with %s', $line, $sql, print_r($parameter, true)),
                [
                    'originalQuery' => $sql,
                    'parameter' => $parameter,
                ]
            );

            self::outputSuccess($line, $sql, $parameter);
        } catch (DBALException $e) {
            $logger->error(
                sprintf('Sql error in line %s: %s', $line, (string) $e),
                [
                    'originalQuery' => $sql,
                    'parameter' => $parameter,
                ]
            );

            self::outputError($e);
        }
    }

    /**
     * moves a field behind a given fieldname.
     *
     * @param int    $tableId
     * @param string $fieldName
     * @param string $afterThisField name of the field after which to place the passed field
     */
    public static function SetFieldPosition($tableId, $fieldName, $afterThisField)
    {
        $pos = 0;
        if ('id' !== $afterThisField) {
            // check position of field where we want to set the new field behind
            $afterThisFieldId = self::GetTableFieldId($tableId, $afterThisField);
            $query = "SELECT * FROM `cms_field_conf` WHERE `id` = '".MySqlLegacySupport::getInstance()->real_escape_string($afterThisFieldId)."'";
            $result = MySqlLegacySupport::getInstance()->query($query);
            $posFieldRow = MySqlLegacySupport::getInstance()->fetch_assoc($result);
            $pos = $posFieldRow['position'];
        }

        $query = "UPDATE `cms_field_conf` SET `position` = `position`+1 WHERE `position` > {$pos} AND `cms_tbl_conf_id` = '{$tableId}'";
        self::_RunQuery($query, __LINE__);

        $fieldID = self::GetTableFieldId($tableId, $fieldName);
        $query = "UPDATE `cms_field_conf` SET `position` = '".MySqlLegacySupport::getInstance()->real_escape_string(($pos + 1))."' WHERE `id` = '{$fieldID}'";
        self::_RunQuery($query, __LINE__);
    }

    /**
     * set the position of a table display field relative to another field.
     *
     * @param string $sTableId
     * @param string $sNameField
     * @param string $sNameFieldAfterWhichTheNewFieldShouldBePlaced
     */
    public static function SetDisplayFieldPosition($sTableId, $sNameField, $sNameFieldAfterWhichTheNewFieldShouldBePlaced)
    {
        $iPos = 0;
        $query = "SELECT * FROM cms_tbl_display_list_fields
                 WHERE `cms_tbl_conf_id` = '".MySqlLegacySupport::getInstance()->real_escape_string($sTableId)."'
                   AND `name` = '".MySqlLegacySupport::getInstance()->real_escape_string($sNameFieldAfterWhichTheNewFieldShouldBePlaced)."'";
        if ($aBeforeField = MySqlLegacySupport::getInstance()->fetch_assoc(MySqlLegacySupport::getInstance()->query($query))) {
            $iPos = $aBeforeField['position'] + 1;
            $query = "UPDATE cms_tbl_display_list_fields SET `position` = `position` +1
                   WHERE `cms_tbl_conf_id` = '".MySqlLegacySupport::getInstance()->real_escape_string($sTableId)."'
                     AND `position` > ".$aBeforeField['position'].'
                 ';
            MySqlLegacySupport::getInstance()->query($query);
        } else {
            $query = "SELECT MAX(`position`) AS maxpos
                    FROM cms_tbl_display_list_fields
                   WHERE `cms_tbl_conf_id` = '".MySqlLegacySupport::getInstance()->real_escape_string($sTableId)."'
                GROUP BY `cms_tbl_conf_id`
         ";
            $aRow = MySqlLegacySupport::getInstance()->fetch_assoc(MySqlLegacySupport::getInstance()->query($query));
            $iPos = $aRow['maxpos'] + 1;
        }
        $query = "UPDATE `cms_tbl_display_list_fields`
                   SET `position` = '{$iPos}'
                 WHERE `cms_tbl_conf_id` = '".MySqlLegacySupport::getInstance()->real_escape_string($sTableId)."'
                   AND `name` = '".MySqlLegacySupport::getInstance()->real_escape_string($sNameField)."'";
        MySqlLegacySupport::getInstance()->query($query);
    }

    /**
     * set the position of a table display field relative to another field.
     *
     * @param string $sTableId
     * @param string $sAliasField
     * @param string $sAliasFieldAfterWhichTheNewFieldShouldBePlaced
     */
    public static function SetDisplayFieldPositionByAlias($sTableId, $sAliasField, $sAliasFieldAfterWhichTheNewFieldShouldBePlaced)
    {
        $query = "SELECT * FROM cms_tbl_display_list_fields
                 WHERE `cms_tbl_conf_id` = '".MySqlLegacySupport::getInstance()->real_escape_string($sTableId)."'
                   AND `db_alias` = '".MySqlLegacySupport::getInstance()->real_escape_string($sAliasField)."'";

        $query2 = "SELECT * FROM cms_tbl_display_list_fields
                 WHERE `cms_tbl_conf_id` = '".MySqlLegacySupport::getInstance()->real_escape_string($sTableId)."'
                   AND `db_alias` = '".MySqlLegacySupport::getInstance()->real_escape_string($sAliasFieldAfterWhichTheNewFieldShouldBePlaced)."'";

        if (($aBeforeField = MySqlLegacySupport::getInstance()->fetch_assoc(MySqlLegacySupport::getInstance()->query($query))) && ($aAfterField = MySqlLegacySupport::getInstance()->fetch_assoc(MySqlLegacySupport::getInstance()->query($query2)))) {
            self::SetDisplayFieldPosition($sTableId, $aBeforeField['name'], $aAfterField['name']);
        }
    }

    public static function setMainMenuPosition(
        string $mainMenuCategorySystemName,
        ?string $afterThisMainMenuCategory = null): void
    {
        $databaseConnection = self::getDatabaseConnection();

        $query = 'SELECT * FROM `cms_menu_category` WHERE `system_name` = :systemName';
        $sourceMenu = $databaseConnection->fetchAssoc($query, array('systemName' => $mainMenuCategorySystemName));

        if (false === $sourceMenu) {
            $message = sprintf('Could not place main menu category: %s, because this category is missing.', $mainMenuCategorySystemName);
            self::addInfoMessage($message, self::INFO_MESSAGE_LEVEL_WARNING);

            return;
        }

        $newPosition = 0;

        if (null !== $afterThisMainMenuCategory) {
            $query = 'SELECT * FROM `cms_menu_category` WHERE `system_name` = :systemName';
            $targetMenu = $databaseConnection->fetchAssoc($query, array('systemName' => $afterThisMainMenuCategory));

            if (false === $targetMenu) {
                $message = sprintf('Could not place main menu category: %s, behind %s because the target category is missing.', $mainMenuCategorySystemName, $afterThisMainMenuCategory);
                self::addInfoMessage($message, self::INFO_MESSAGE_LEVEL_WARNING);

                return;
            }

            $newPosition = (int) $targetMenu['position'] + 1;
        }

        $query = 'UPDATE `cms_menu_category` SET `position` = `position`+1 WHERE `position` >= :newPosition';
        $databaseConnection->executeQuery($query, array('newPosition' => $newPosition));

        $query = 'UPDATE `cms_menu_category` SET `position` = :newPosition WHERE `id` = :sourceId';
        $databaseConnection->executeQuery($query, array('newPosition' => $newPosition, 'sourceId' => $sourceMenu['id']));
    }

    /**
     * fetches the id of a user role by given identifier e.g. 'chief_editor'.
     *
     * @param string $identifierKey
     *
     * @return string
     */
    public static function GetUserRoleIdByKey($identifierKey)
    {
        $returnVal = false;
        if (!empty($identifierKey)) {
            $query = "SELECT * FROM `cms_role` WHERE `name` = '".MySqlLegacySupport::getInstance()->real_escape_string($identifierKey)."'";
            $result = MySqlLegacySupport::getInstance()->query($query);
            if (1 == MySqlLegacySupport::getInstance()->num_rows($result)) {
                $row = MySqlLegacySupport::getInstance()->fetch_assoc($result);
                $returnVal = $row['id'];
            }
        }

        return $returnVal;
    }

    /**
     * fetches the id of a user group by given identifier e.g. 'website_editor'.
     *
     * @param string $identifierKey
     *
     * @return string
     */
    public static function GetUserGroupIdByKey($identifierKey)
    {
        $returnVal = false;
        if (!empty($identifierKey)) {
            $query = "SELECT * FROM `cms_usergroup` WHERE `internal_identifier` = '".MySqlLegacySupport::getInstance()->real_escape_string($identifierKey)."'";
            $result = MySqlLegacySupport::getInstance()->query($query);
            if (1 == MySqlLegacySupport::getInstance()->num_rows($result)) {
                $row = MySqlLegacySupport::getInstance()->fetch_assoc($result);
                $returnVal = $row['id'];
            }
        }

        return $returnVal;
    }

    /**
     * fetches the id of a user right by given identifier e.g. 'flush_cms_cache'.
     *
     * @param string $identifierKey
     *
     * @return string
     */
    public static function GetUserRightIdByKey($identifierKey)
    {
        $returnVal = false;
        if (!empty($identifierKey)) {
            $query = "SELECT * FROM `cms_right` WHERE `name` = '".MySqlLegacySupport::getInstance()->real_escape_string($identifierKey)."'";
            $result = MySqlLegacySupport::getInstance()->query($query);
            if (1 == MySqlLegacySupport::getInstance()->num_rows($result)) {
                $row = MySqlLegacySupport::getInstance()->fetch_assoc($result);
                $returnVal = $row['id'];
            }
        }

        return $returnVal;
    }

    /**
     * fetches the id of a user by login name (e.g. admin).
     *
     * @param string $sName
     *
     * @return string
     */
    public static function GetUserIdByLoginName($sName)
    {
        $returnVal = false;
        if (!empty($sName)) {
            $query = "SELECT * FROM `cms_user` WHERE `login` = '".MySqlLegacySupport::getInstance()->real_escape_string($sName)."'";
            $result = MySqlLegacySupport::getInstance()->query($query);
            if (1 == MySqlLegacySupport::getInstance()->num_rows($result)) {
                $row = MySqlLegacySupport::getInstance()->fetch_assoc($result);
                $returnVal = $row['id'];
            }
        }

        return $returnVal;
    }

    /**
     * updates the autogenerated table classes for one table or if tableID is missing, for all tables.
     *
     * @param string $tableID
     */
    public static function UpdateAutoClasses($tableID = null)
    {
        self::getAutoclassesDataAccessRequestCache()->clearCache();
        $autoclassesCacheWarmer = self::getAutoclassesCacheWarmer();
        if (null !== $tableID) {
            $autoclassesCacheWarmer->updateTableById($tableID);
        } else {
            $autoclassesCacheWarmer->updateAllTables();
        }
    }

    /**
     * checks if a field exists.
     *
     * @param string $sTableName
     * @param string $sFieldName
     *
     * @return bool
     */
    public static function FieldExists($sTableName, $sFieldName)
    {
        return TTools::FieldExists($sTableName, $sFieldName, true);
    }

    /**
     * sets permissions for a role.
     *
     * role fieldnr list:
     * 0 = new record
     * 1 = edit record
     * 2 = delete record
     * 3 = edit all records
     * 4 = add new language
     * 5 = publish record using workflow (deprecated since 6.2.0 - do not use)
     * 6 = show all records
     *
     * @param string     $sRoleName
     * @param string     $sTableName
     * @param bool       $bResetRoles  - indicates if all other roles will be kicked and the new role has the exclusive right
     * @param array|bool $aPermissions - array of the role fieldnr. 0,1,2,3,4,5,6, default = false
     */
    public static function SetTableRolePermissions($sRoleName = 'cms_admin', $sTableName, $bResetRoles = false, $aPermissions = false)
    {
        $databaseConnection = self::getDatabaseConnection();
        $roleID = self::GetUserRoleIdByKey($sRoleName);
        $tableID = self::GetTableId($sTableName);
        $quotedTableId = $databaseConnection->quote($tableID);
        $quotedRoleId = $databaseConnection->quote($roleID);

        if ($aPermissions && is_array($aPermissions)) {
            foreach ($aPermissions as $roleNr) {
                if (0 == $roleNr) {
                    $roleNr = '';
                }
                $quotedRoleTableName = $databaseConnection->quoteIdentifier("cms_tbl_conf_cms_role{$roleNr}_mlt");

                if ($bResetRoles) {
                    $query = "DELETE FROM $quotedRoleTableName WHERE `source_id` = $quotedTableId";
                    self::RunQuery(__LINE__, $query, array());
                }

                /** @noinspection SuspiciousAssignmentsInspection */
                $query = "INSERT IGNORE INTO $quotedRoleTableName SET `source_id` = $quotedTableId, `target_id` = $quotedRoleId";
                self::RunQuery(__LINE__, $query, array());
            }
        } else {
            if ($bResetRoles) {
                $query = "DELETE FROM `cms_tbl_conf_cms_role_mlt` WHERE `source_id` = $quotedTableId";
                self::RunQuery(__LINE__, $query, array());
            } else {
                $query = "DELETE FROM `cms_tbl_conf_cms_role_mlt` WHERE `source_id` = $quotedTableId AND `target_id` = $quotedRoleId";
                self::RunQuery(__LINE__, $query, array());
            }
            for ($i = 1; $i < 7; ++$i) {
                if ($bResetRoles) {
                    $query = 'DELETE FROM `cms_tbl_conf_cms_role'.$i."_mlt` WHERE `source_id` = $quotedTableId";
                    self::RunQuery(__LINE__, $query, array());
                } else {
                    $query = 'DELETE FROM `cms_tbl_conf_cms_role'.$i."_mlt` WHERE `source_id` = $quotedTableId AND `target_id` = $quotedRoleId";
                    self::RunQuery(__LINE__, $query, array());
                }
            }
        }
    }

    /**
     * checks if table exists in database.
     *
     * @param string $sTableName
     *
     * @return bool
     */
    public static function TableExists($sTableName)
    {
        return TGlobalBase::TableExists($sTableName);
    }

    /**
     * checks if record exists in table.
     *
     * @param string $sTableName
     * @param string $sFieldName
     * @param string $sFieldValue
     *
     * @return bool
     */
    public static function RecordExists($sTableName, $sFieldName, $sFieldValue)
    {
        return TTools::RecordExists($sTableName, $sFieldName, $sFieldValue);
    }

    /**
     * checks if CMS backend module exists.
     *
     * @param string $sModuleUniqueName
     *
     * @return bool
     */
    public static function CMSModuleExists($sModuleUniqueName)
    {
        $bModuleExists = false;
        $query = "SELECT * FROM `cms_module` WHERE `uniquecmsname` = '".MySqlLegacySupport::getInstance()->real_escape_string($sModuleUniqueName)."'";
        $result = MySqlLegacySupport::getInstance()->query($query);
        if (MySqlLegacySupport::getInstance()->num_rows($result) > 0) {
            $bModuleExists = true;
        }

        return $bModuleExists;
    }

    /**
     * returns the id of a field tab or empty string if nothing found.
     *
     * @param string $sTableID
     * @param string $sTabName
     *
     * @return string - id of field tab (default = empty string)
     */
    public static function GetTabIDByName($sTableID, $sTabName)
    {
        $sTabID = '';
        if (!empty($sTableID) && !empty($sTabName)) {
            $query = "SELECT *
                        FROM `cms_tbl_field_tab`
                       WHERE `cms_tbl_conf_id` = '".MySqlLegacySupport::getInstance()->real_escape_string($sTableID)."'
                         AND `name` = '".MySqlLegacySupport::getInstance()->real_escape_string($sTabName)."' ";
            $oCmsTblFieldTabList = TdbCmsTblFieldTabList::GetList($query);
            /** @var $oCmsTblFieldTabList TdbCmsTblFieldTabList */
            if ($oCmsTblFieldTabList->Length() > 0) {
                $oCmsTblFieldTab = $oCmsTblFieldTabList->Current();
                $sTabID = $oCmsTblFieldTab->id;
            }
        }

        return $sTabID;
    }

    /**
     * @param string $sTableName
     * @param string $sTabSystemName
     *
     * @return bool
     *
     * @throws ErrorException
     * @throws TPkgCmsException_Log
     */
    public static function getTabIDBySystemName($sTableName, $sTabSystemName)
    {
        $sTabId = false;
        $query = "SELECT *
                        FROM `cms_tbl_field_tab`
                       WHERE `cms_tbl_conf_id` = '".MySqlLegacySupport::getInstance()->real_escape_string(self::GetTableId($sTableName))."'
                         AND `systemname` = '".MySqlLegacySupport::getInstance()->real_escape_string($sTabSystemName)."' ";
        if ($aRow = MySqlLegacySupport::getInstance()->fetch_assoc(MySqlLegacySupport::getInstance()->query($query))) {
            $sTabId = $aRow['id'];
        }

        return $sTabId;
    }

    /**
     * @param string $tableName
     * @param string $tabSystemName
     *
     * @throws DBALException
     */
    public static function deleteTabBySystemName($tableName, $tabSystemName)
    {
        $tabId = self::getTabIDBySystemName($tableName, $tabSystemName);
        $fieldList = self::getFieldIdListForTab($tabId);
        if (!empty($fieldList)) {
            throw new LogicException("There are still fields on the tab $tableName:$tabSystemName. Delete these fields first or move them to another tab.");
        }

        $query = 'DELETE
                        FROM `cms_tbl_field_tab`
                       WHERE `cms_tbl_conf_id` = :tableId
                         AND `systemname` = :tabSystemName';
        $tableId = self::GetTableId($tableName);
        self::getDatabaseConnection()->executeQuery($query, array(
            'tableId' => $tableId,
            'tabSystemName' => $tabSystemName,
        ));
    }

    /**
     * @param string $tabId
     *
     * @return array
     */
    public static function getFieldIdListForTab($tabId)
    {
        $query = 'SELECT `id` FROM `cms_field_conf` WHERE `cms_tbl_field_tab` = :tabId';

        return self::getDatabaseConnection()->fetchArray($query, array('tabId' => $tabId));
    }

    /**
     * Check if given language is configured.
     *
     * @param string $sIso6391Code
     *
     * @return bool
     */
    public static function CheckIfLanguageExists($sIso6391Code)
    {
        $bReturnVal = false;
        if (!empty($sIso6391Code)) {
            $oConfig = &TdbCmsConfig::GetInstance();

            $oLangList = $oConfig->GetFieldCmsLanguageList();
            if ($oLangList->Length()) {
                while ($oLangItem = $oLangList->Next()) {
                    if (strtolower(trim($oLangItem->fieldIso6391)) == strtolower(trim($sIso6391Code))) {
                        $bReturnVal = true;
                    }
                }
            }
        }

        return $bReturnVal;
    }

    /**
     * Rebuild translation fields.
     */
    public static function UpdateTranslationFields()
    {
        $oConfig = TdbCmsConfig::GetInstance();
        $iTableID = TTools::GetCMSTableId('cms_config');
        $oTableEditor = new TCMSTableEditorCMSConfig();
        $oTableEditor->Init($iTableID, $oConfig->id);
        $oTableEditor->UpdateTranslationFields();
    }

    /**
     * @see FieldTranslationUtil::makeFieldMultilingual()
     *
     * @param string $tableName
     * @param string $fieldName
     */
    public static function makeFieldMultilingual($tableName, $fieldName)
    {
        $fieldId = self::GetTableFieldId(self::GetTableId($tableName), $fieldName);
        self::getFieldTranslationUtil()->makeFieldMultilingual($fieldId);
    }

    /**
     * @see FieldTranslationUtil::makeFieldMonolingual()
     *
     * @param string $tableName
     * @param string $fieldName
     */
    public static function makeFieldMonolingual($tableName, $fieldName)
    {
        $fieldId = self::GetTableFieldId(self::GetTableId($tableName), $fieldName);
        self::getFieldTranslationUtil()->makeFieldMonolingual($fieldId);
    }

    /**
     * Get id of cms_message_manager_message_type by name (e.g. "Popup Notice").
     *
     * @deprecated since 7.0.13 - use TCMSLogChange::getMessageTypeIdBySystemName() instead.
     *
     * @param string $sMessageTypeName
     * @param string $languageIso - default is "DE" for backwards compatibility reasons.
     *
     * @return string
     */
    public static function GetMessageTypeByName(string $sMessageTypeName, string $languageIso = 'de')
    {
        if (true === empty($sMessageTypeName)) {
            return '';
        }

        $language = self::getLanguageService()->getLanguageFromIsoCode(\strtolower($languageIso));

        $messageType = \TdbCmsMessageManagerMessageType::GetNewInstance();
        if (null !== $language) {
            $messageType->SetLanguage($language->id);
        }
        if (false === $messageType->LoadFromField('name', $sMessageTypeName)) {
            return '';
        }

        return $messageType->id;
    }

    /**
     * The available default system names are: unknown, notice, warning, error, error_striking.
     *
     * @param string $messageTypeName
     * @return string|null
     */
    public static function getMessageTypeIdBySystemName(string $messageTypeName): ?string
    {
        $messageType = TdbCmsMessageManagerMessageType::GetNewInstance();
        if (false === $messageType->LoadFromField('systemname', $messageTypeName)) {
            return null;
        }

        return $messageType->id;
    }

    /**
     * Inserts a portal based frontend message if it doesn't exist already or updates an existing autogenerated or empty message.
     *
     * @param string      $sIdentifierName
     * @param string      $sMessage
     * @param string      $sMessageTypeId
     * @param string      $sDescription
     * @param string      $sPortalID
     * @param string      $sMessageLocationType
     * @param string      $sMessageView
     * @param string|null $language
     *
     * @throws DBALException
     */
    public static function AddFrontEndMessage($sIdentifierName, $sMessage, $sMessageTypeId = '4', $sDescription = '', $sPortalID = '', $sMessageLocationType = 'Core', $sMessageView = 'standard', $language = null)
    {
        if (empty($sDescription)) {
            $sDescription = $sMessage;
        }
        $connection = static::getDatabaseConnection();

        $fields = array(
            'description' => $sDescription,
            'message' => $sMessage,
            'cms_message_manager_message_type_id' => $sMessageTypeId,
        );
        if (TCMSRecord::FieldExists('cms_message_manager_message', 'message_location_type')) {
            $fields['message_location_type'] = $sMessageLocationType;
        }
        if (TCMSRecord::FieldExists('cms_message_manager_message', 'message_view')) {
            $fields['message_view'] = $sMessageView;
        }

        $existsCheckQuery = 'SELECT * FROM `cms_message_manager_message` WHERE `name` = :name';
        $existsCheckArray = [ 'name' => $sIdentifierName, ];

        if ('' !== $sPortalID) {
            $existsCheckQuery .= ' AND `portal_id` = :portalId';
            $existsCheckArray['portalId'] = $sPortalID;
        }

        $existingDatasets = $connection->fetchAll($existsCheckQuery, $existsCheckArray);

        $languageCode = self::getLanguageCodeFromArgument($language);
        $data = static::createMigrationQueryData('cms_message_manager_message', $languageCode);

        if (count($existingDatasets) > 0) {
            $row = $existingDatasets[0];
            if (false === static::isMessageEmptyOrInsertedAutomatically($row, $languageCode)) {
                return;
            }
            $fields['cms_portal_id'] = $row['cms_portal_id'];
            $data->setFields($fields);
            $data->setWhereEquals(array(
                'id' => $row['id'],
            ));
            static::update(__LINE__, $data);
        } else { // not found... insert it
            $fields['name'] = $sIdentifierName;
            if (empty($sPortalID)) {
                // insert into every portal if no explicit portal id was set
                $statement = $connection->prepare('SELECT `id` FROM `cms_portal`');
                if ($statement->execute()) {
                    while ($row = $statement->fetch()) {
                        $fields['id'] = self::createUnusedRecordId('cms_message_manager_message');
                        $fields['cms_portal_id'] = $row['id'];
                        $data->setFields($fields);
                        static::insert(__LINE__, $data);
                    }
                }
            } else {
                $fields['id'] = self::createUnusedRecordId('cms_message_manager_message');
                $fields['cms_portal_id'] = $sPortalID;
                $data->setFields($fields);
                static::insert(__LINE__, $data);
            }
        }
    }

    /**
     * @param array  $row
     * @param string $languageCode
     *
     * @return bool
     */
    private static function isMessageEmptyOrInsertedAutomatically(array $row, $languageCode)
    {
        $descriptionFieldName = 'description';
        if (null !== $languageCode) {
            $language = self::getLanguageService()->getLanguageFromIsoCode($languageCode);
            $descriptionFieldName = self::getFieldTranslationUtil()->getTranslatedFieldName('cms_message_manager_message', $descriptionFieldName, $language);
        }
        $description = $row[$descriptionFieldName];

        return '' === $description || 0 === strpos($description, TCMSMessageManager::AUTO_CREATED_MARKER);
    }

    /**
     * Inserts a backend message if it doesn't already exist, or updates an existing autogenerated message.
     *
     * @param string          $identifierName
     * @param string          $message
     * @param string          $messageTypeId
     * @param string          $description
     * @param int|string|null $language       The language for which to insert or update the message. This should be the
     *                                        two-letter ISO-639-1 language code (e.g. "en"). To ensure backwards compatibility,
     *                                        it is also possible to pass a Chameleon language ID. If you do not pass any
     *                                        language value, the system's base language is used. This is not recommended though,
     *                                        because it may produce different values on different systems.
     *
     * @throws ErrorException
     * @throws TPkgCmsException_Log
     * @throws UnexpectedValueException if no unique ID could be generated for the new message
     */
    public static function AddBackEndMessage($identifierName, $message, $messageTypeId = '4', $description = '', $language = null)
    {
        if (empty($description)) {
            $description = $message;
        }
        $languageCode = self::getLanguageCodeFromArgument($language);
        $databaseConnection = self::getDatabaseConnection();
        $checkQuery = 'SELECT `id`, `description` FROM `cms_message_manager_backend_message` WHERE `name` = :messageName';
        $result = $databaseConnection->fetchAll($checkQuery, array(
            'messageName' => $identifierName,
        ));
        $data = self::createMigrationQueryData('cms_message_manager_backend_message', $languageCode);
        $fields = array(
                'description' => $description,
                'message' => $message,
                'cms_config_id' => '1',
                'cms_message_manager_message_type_id' => $messageTypeId,
        );
        if (count($result) > 0) {
            $row = $result[0];
            $data->setFields($fields);
            $data->setWhereEquals(array(
                'id' => $row['id'],
            ));
            self::update(__LINE__, $data);
        } else { // not found... insert it
            $fields['name'] = $identifierName;
            $fields['id'] = self::createUnusedRecordId('cms_message_manager_backend_message');
            $data->setFields($fields);
            self::insert(__LINE__, $data);
        }
    }

    /**
     * @see TCMSLogChange::AddBackEndMessage()
     *
     * @param string|int|null $language
     *
     * @return string|null
     */
    private static function getLanguageCodeFromArgument($language)
    {
        $languageCode = null;
        if (null === $language) {
            $baseLanguage = TdbCmsConfig::GetInstance()->GetFieldTranslationBaseLanguage();
            $languageCode = $baseLanguage->fieldIso6391;
        } else {
            $baseLanguage = TdbCmsLanguage::GetNewInstance();
            if (is_numeric($language)) {
                $baseLanguage->Load($language);
                $languageCode = $baseLanguage->fieldIso6391;
            } else {
                $languageCode = $language;
            }
        }

        return $languageCode;
    }

    /**
     * @param string $name
     */
    public static function deleteBackEndMessage($name)
    {
        $query = 'DELETE FROM `cms_message_manager_backend_message` WHERE `name` = :name';
        self::RunQuery(__LINE__, $query, array('name' => $name));
    }

    /**
     * Returns a new record ID that is guaranteed to be unused in the given table.
     *
     * @param string $tableName The table to create an ID for. This MUST be a table with an 'id' attribute.
     *
     * @return string
     *
     * @throws UnexpectedValueException if after 10 attempts no unique ID could be generated
     */
    public static function createUnusedRecordId($tableName)
    {
        $databaseConnection = self::getDatabaseConnection();
        $quotedTableName = $databaseConnection->quoteIdentifier($tableName);
        $tries = 11;
        do {
            $id = TTools::GetUUID();
            $count = $databaseConnection->fetchColumn("SELECT count(*) FROM $quotedTableName WHERE `id` = :id", array(
                'id' => $id,
            ));
            --$tries;
        } while (intval($count) > 0 && $tries > 0);
        if (0 === $tries) {
            throw new UnexpectedValueException('TCMSLogChange::createUnusedRecordId was unable to create an unused ID after 10 attempts.');
        }

        return $id;
    }

    /**
     * changes an extension to use the auto parent extension.
     *
     * @static
     *
     * @param string $sExtensionTableName - the db table name
     * @param string $sClassName - the origianl class name
     * @param string $sListClassName - the original list class name (if set)
     * @param string $sClassSubType - (deprecated!) the path relative to classes
     * @param string $sClassType - (deprecated!) core/customer?
     */
    public static function ChangeExtensionToAutoParentClass($sExtensionTableName, $sClassName, $sListClassName, $sClassSubType = '', $sClassType = 'Core')
    {
        $sTableId = self::GetTableId($sExtensionTableName);

        $query = "SELECT * FROM `cms_tbl_extension` WHERE `cms_tbl_conf_id` = '{$sTableId}'";
        $tRes = MySqlLegacySupport::getInstance()->query($query);
        if (1 == MySqlLegacySupport::getInstance()->num_rows($tRes)) {
            $aRes = MySqlLegacySupport::getInstance()->fetch_assoc($tRes);
            if ($aRes['name'] != $sClassName || ($aRes['name_list'] != $sListClassName && !empty($aRes['name_list']))) {
                $sAutoListClass = '';
                if (!empty($sListClassName)) {
                    $sAutoListClass = $sListClassName.'AutoParent';
                }
                // WARNING - need to insert our extensions AND update the existing extension
                $query = "INSERT INTO `cms_tbl_extension`
                            SET `cms_tbl_conf_id` = '{$sTableId}',
                                `name` = '{$sClassName}', `name_list` = '{$sListClassName}',
                                `position` = '1', `virtual_item_class_name` = '{$sClassName}AutoParent', `virtual_item_class_list_name` = '{$sAutoListClass}'
                   ";
                self::_RunQuery($query, __LINE__);
            }
            // just update...

            if (empty($sListClassName)) {
                $sListClassName = $aRes['name_list'];
            }
            $sAutoListClass = '';
            if (!empty($sListClassName)) {
                $sAutoListClass = $sListClassName.'AutoParent';
            }
            $query = "UPDATE `cms_tbl_extension`
                     SET `position`= 2,
                         `virtual_item_class_name` = '{$aRes['name']}AutoParent',
                         `name_list` = '{$sListClassName}',
                         `virtual_item_class_list_name` = '{$sAutoListClass}'
                   WHERE `id` = '{$aRes['id']}'
                 ";
            self::_RunQuery($query, __LINE__);
            if ('Customer' == $aRes['type']) {
                self::addInfoMessage('The extension in <strong>'.$sExtensionTableName.'</strong> was changed to use AutoParent classes. Since this class was extended in your project,
      <strong>you must</strong> change your extension <strong>'.$aRes['name'].'</strong> to use auto classes as well!', self::INFO_MESSAGE_LEVEL_TODO);
            }
        }
    }

    /**
     * add a new extension to a table. if no $sNameOfClassAfterWhichToPosition is given, the class will be inserted in last position.
     *
     * @param string $sTableName
     * @param string $sClassName
     * @param string $sClassSubType - deprecated! handled by autoloader
     * @param string $sClassType - deprecated! handled by autoloader
     * @param string $sListClass
     * @param string $sNameOfClassAfterWhichToPosition
     */
    public static function AddExtensionAutoParentToTable($sTableName, $sClassName, $sClassSubType = '', $sClassType = '', $sListClass = '', $sNameOfClassAfterWhichToPosition = '')
    {
        $iPos = 1;
        $sTableId = self::GetTableId($sTableName);
        $query = "SELECT MAX(`position`) AS maxpos FROM `cms_tbl_extension` WHERE `cms_tbl_conf_id` = '{$sTableId}'";
        if ($aRow = MySqlLegacySupport::getInstance()->fetch_assoc(MySqlLegacySupport::getInstance()->query($query))) {
            $iPos = $aRow['maxpos'] + 1;
        }
        $sVirtualClass = '';
        if (!empty($sClassName)) {
            $sVirtualClass = $sClassName.'AutoParent';
            if (false !== strpos($sVirtualClass, '\\')) {
                $sVirtualClass = str_replace('\\', '', $sVirtualClass);
            }
        }
        $sVirtualListClass = '';
        if (!empty($sListClass)) {
            $sVirtualListClass = $sListClass.'AutoParent';
            if (false !== strpos($sVirtualListClass, '\\')) {
                $sVirtualListClass = str_replace('\\', '', $sVirtualListClass);
            }
        }
        $query = "INSERT INTO `cms_tbl_extension`
                        SET `cms_tbl_conf_id` = '{$sTableId}',
                            `name` = '".MySqlLegacySupport::getInstance()->real_escape_string($sClassName)."',
                            `name_list` = '".MySqlLegacySupport::getInstance()->real_escape_string($sListClass)."',
                            `position` = '".MySqlLegacySupport::getInstance()->real_escape_string($iPos)."',
                            `virtual_item_class_name` = '".MySqlLegacySupport::getInstance()->real_escape_string($sVirtualClass)."',
                            `virtual_item_class_list_name` = '".MySqlLegacySupport::getInstance()->real_escape_string($sVirtualListClass)."'";
        self::_RunQuery($query, __LINE__);
        if (!empty($sNameOfClassAfterWhichToPosition)) {
            self::SetExtensionPosition($sTableId, $sClassName, $sNameOfClassAfterWhichToPosition);
        }

        // rewrite the class chain
        self::getAutoclassesCacheWarmer()->updateTableByName($sTableName);
    }

    /**
     * Deletes an extension previously added with AddExtensionAutoParentToTable() from a table.
     *
     * @param string $tableName
     * @param string $className
     */
    public static function deleteExtensionAutoParentFromTable($tableName, $className)
    {
        $tableId = self::GetTableId($tableName);

        $query = 'DELETE FROM `cms_tbl_extension` WHERE `cms_tbl_conf_id` = :tableId AND `name` = :className';
        self::RunQuery(__LINE__, $query, array(
            'tableId' => $tableId,
            'className' => $className,
        ));
    }

    /**
     * moves a extens behind a given extension in cms_tbl_extension.
     *
     * @param int    $tableId
     * @param string $sExtensionName
     * @param string $sPreExtensionName - extensionname where we want to set the new extension behind
     */
    public static function SetExtensionPosition($tableId, $sExtensionName, $sPreExtensionName)
    {
        // check position of field where we want to set the new field behind
        /** @var $databaseConnection \Doctrine\DBAL\Connection */
        $databaseConnection = \ChameleonSystem\CoreBundle\ServiceLocator::get('database_connection');
        $query = 'SELECT `position` FROM `cms_tbl_extension` WHERE `name` = :preExtensionName';
        $posData = $databaseConnection->fetchArray($query, array('preExtensionName' => $sPreExtensionName));
        if (false === $posData) {
            self::addInfoMessage("unable to position extension {$sExtensionName} after {$sPreExtensionName} because {$sPreExtensionName} can not be found", self::INFO_MESSAGE_LEVEL_ERROR);

            return;
        }
        $pos = $posData[0];

        $query = 'UPDATE `cms_tbl_extension` SET `position` = `position`+1 WHERE `position` >  :positionAfter AND `cms_tbl_conf_id` = :tableConfId';
        self::RunQuery(__LINE__, $query, array('positionAfter' => $pos, 'tableConfId' => $tableId), array('positionAfter' => \PDO::PARAM_INT, 'tableConfId' => \PDO::PARAM_STR));

        $query = 'UPDATE `cms_tbl_extension` SET `position` = :newPosition WHERE `name` = :extensionName AND `cms_tbl_conf_id` = :tableConfId';
        self::RunQuery(__LINE__, $query, array('newPosition' => $pos + 1, 'tableConfId' => $tableId, 'extensionName' => $sExtensionName), array('newPosition' => \PDO::PARAM_INT, 'tableConfId' => \PDO::PARAM_STR, 'extensionName' => \PDO::PARAM_STR));
    }

    /**
     * moves a tab behind a given tab in cms_tbl_field_tab.
     *
     * @param int    $tableId
     * @param string $sTabName
     * @param string $sPreTabName - tabname where we want to set the new tab behind
     */
    public static function SetTabPosition($tableId, $sTabName, $sPreTabName)
    {
        // check position of field where we want to set the new field behind
        $query = "SELECT * FROM `cms_tbl_field_tab` WHERE `name` = '".MySqlLegacySupport::getInstance()->real_escape_string($sPreTabName)."'";
        $result = MySqlLegacySupport::getInstance()->query($query);
        $posFieldRow = MySqlLegacySupport::getInstance()->fetch_assoc($result);
        $pos = $posFieldRow['position'];

        $query = "UPDATE `cms_tbl_field_tab` SET `position` = `position`+1 WHERE `position` > {$pos} AND `cms_tbl_conf_id` = '{$tableId}'";
        self::_RunQuery($query, __LINE__);

        $query = "UPDATE `cms_tbl_field_tab` SET `position` = '".MySqlLegacySupport::getInstance()->real_escape_string(($pos + 1))."' WHERE `name` = '{$sTabName}'";
        self::_RunQuery($query, __LINE__);
    }

    /**
     * get the id of a content box by its system_name.
     *
     * @param string $sSystemName of the content box
     *
     * @return string content box id OR empty string
     *
     * @deprecated since 6.3.0 - only used for deprecated classic main menu
     */
    public static function getCmsContentBoxIdFromSystemName($sSystemName)
    {
        $return = '';
        $query = "SELECT * FROM `cms_content_box` WHERE `system_name` = '".MySqlLegacySupport::getInstance()->real_escape_string($sSystemName)."' OR `name` = '".MySqlLegacySupport::getInstance()->real_escape_string($sSystemName)."'";
        if ($oContentBox = MySqlLegacySupport::getInstance()->fetch_object(MySqlLegacySupport::getInstance()->query($query))) {
            $return = $oContentBox->id;
        } else {
            self::addInfoMessage('Unable to find cms_content_box with name/system_name "'.$sSystemName.'"', self::INFO_MESSAGE_LEVEL_ERROR);
        }

        return $return;
    }

    /**
     * @param string $sTableName
     * @param string $sFieldName
     * @param string $sSetting
     *
     * @throws InvalidArgumentException if the field was not found
     *
     * @deprecated since 6.2.0 - use TCMSLogChange:makeFieldMultilingual() and TCMSLogChange::makeFieldMonolingual() instead.
     */
    public static function FieldChangeMultiLanguageSetting($sTableName, $sFieldName, $sSetting)
    {
        if (true == $sSetting) { // non-strict comparison intended.
            self::makeFieldMultilingual($sTableName, $sFieldName);
        } else {
            self::makeFieldMonolingual($sTableName, $sFieldName);
        }
    }

    public static function UpdateVirtualNonDbClasses()
    {
        $filemanager = \ChameleonSystem\CoreBundle\ServiceLocator::get('chameleon_system_core.filemanager');
        $oAutoTableWriter = new TPkgCoreAutoClassHandler_TPkgCmsClassManager(self::getDatabaseConnection(), $filemanager);
        $oList = TdbPkgCmsClassManagerList::GetList();
        while ($oItem = $oList->Next()) {
            $oAutoTableWriter->create($oItem->fieldNameOfEntryPoint, null);
        }
    }

    /**
     * @param int    $iLine
     * @param string $sEntryPoint
     * @param string $sExitClass
     * @param string $sExitClassSubType - deprecated - handled by autoLoader
     * @param string $sExitClassType    - deprecated - handled by autoLoader
     *
     * @return TPkgCmsVirtualClassManager
     *
     * @throws ErrorException
     * @throws TPkgCmsException_Log
     */
    public static function CreateVirtualNonDbEntryPoint($iLine, $sEntryPoint, $sExitClass = '', $sExitClassSubType = '', $sExitClassType = 'Core')
    {
        $oManager = new TPkgCmsVirtualClassManager();
        if (false == $oManager->load($sEntryPoint)) {
            $query = "INSERT INTO `pkg_cms_class_manager`
                          SET `name_of_entry_point` = '".MySqlLegacySupport::getInstance()->real_escape_string($sEntryPoint)."',
                              `exit_class` = '".MySqlLegacySupport::getInstance()->real_escape_string($sExitClass)."'
                 ";
            self::_RunQuery($query, $iLine);
            if (false === TPkgCmsVirtualClassManager::GetEntryPointClassForClass($sEntryPoint, '', '', true)) {
                throw new TPkgCmsException_Log('[line '.$iLine.'] virtual class entry point not created - make sure that the virtual class manager update to the current version was run before the update tries to use it!', array(
                    'entry point' => $sEntryPoint,
                    'exitClass' => $sExitClass,
                    'called from' => $iLine,
                ));
            }
            if (false === $oManager->load($sEntryPoint)) {
                throw new TPkgCmsException_Log('[line '.$iLine.'] virtual class entry point created but unable to load - make sure that the virtual class manager update to the current version was run before the update tries to use it!', array(
                    'entry point' => $sEntryPoint,
                    'exitClass' => $sExitClass,
                    'called from' => $iLine,
                ));
            }
            $oManager->UpdateVirtualClasses();
            if (empty($sExitClass)) {
                self::DisplayErrorMessage('Warning: creating a virtual entry point without an exit point in line '.$iLine);
            }
        }

        return $oManager;
    }

    /**
     * @param string $virtualEntryPoint
     *
     * @throws ErrorException
     */
    public static function deleteVirtualEntryPoint($virtualEntryPoint)
    {
        $query = 'select id from pkg_cms_class_manager where name_of_entry_point = :nameOfEntryPoint';
        $entryPoint = self::getDatabaseConnection()->fetchAssoc($query, array('nameOfEntryPoint' => $virtualEntryPoint));
        if (false === $entryPoint) {
            throw new ErrorException("unable to delete {$virtualEntryPoint} - not found", 0, E_USER_ERROR, __FILE__, __LINE__);
        }

        $query = 'DELETE FROM pkg_cms_class_manager_extension where `pkg_cms_class_manager_id` = :classManagerId';
        self::RunQuery(__LINE__, $query, array('classManagerId' => $entryPoint['id']));

        $query = 'DELETE FROM pkg_cms_class_manager where id = :classManagerId';
        self::RunQuery(__LINE__, $query, array('classManagerId' => $entryPoint['id']));

        self::getAutoclassesCacheWarmer()->updateAllTables();
    }

    /**
     * @param int      $iLine
     * @param string   $sEntryPoint
     * @param string   $sClassName
     * @param string   $sClassSubType - deprecated - handled by autoLoader
     * @param string   $sClassType    - deprecated - handled by autoLoader
     * @param int|null $iPos
     */
    public static function AddVirtualNonDbExtension($iLine, $sEntryPoint, $sClassName, $sClassSubType = '', $sClassType = '', $iPos = null)
    {
        $oManager = self::CreateVirtualNonDbEntryPoint($iLine, $sEntryPoint);

        // class exists?
        $oExtension = TdbPkgCmsClassManagerExtension::GetNewInstance();
        if (false == $oExtension->LoadFromField('class', $sClassName)) {
            if (is_null($iPos)) {
                $iPos = 0;
                $query = "SELECT MAX(`position`) AS maxpos
                      FROM `pkg_cms_class_manager_extension`
                     WHERE `pkg_cms_class_manager_id` = '".MySqlLegacySupport::getInstance()->real_escape_string($oManager->getConfigValue('id'))."'";
                if ($aTmp = MySqlLegacySupport::getInstance()->fetch_array(MySqlLegacySupport::getInstance()->query($query))) {
                    $iPos = $aTmp['maxpos'] + 1;
                }
            }
            $query = "INSERT INTO `pkg_cms_class_manager_extension`
                          SET `pkg_cms_class_manager_id` = '".MySqlLegacySupport::getInstance()->real_escape_string($oManager->getConfigValue('id'))."',
                              `class` = '".MySqlLegacySupport::getInstance()->real_escape_string($sClassName)."',
                              `position` = '".MySqlLegacySupport::getInstance()->real_escape_string($iPos)."'
                 ";
            self::_RunQuery($query, $iLine);
            $oManager->UpdateVirtualClasses();
        }
    }

    /**
     * @param int    $iLine
     * @param string $sEntryPoint
     * @param string $sClassName
     *
     * @throws ErrorException
     */
    public static function deleteVirtualNonDbExtension($iLine, $sEntryPoint, $sClassName)
    {
        $oManager = new TPkgCmsVirtualClassManager();
        if (false === $oManager->load($sEntryPoint)) {
            throw new ErrorException("unable to find virtual class entry point {$sEntryPoint} (called from the running update in line {$iLine})", 0, E_USER_ERROR, __FILE__, __LINE__);
        }
        $oExtension = TdbPkgCmsClassManagerExtension::GetNewInstance();
        if (false === $oExtension->LoadFromFields(array('class' => $sClassName, 'pkg_cms_class_manager_id' => $oManager->getConfigValue('id')))) {
            throw new ErrorException("unable to find virtual extension {$sClassName} for {$sEntryPoint} (called from the running update in line {$iLine})", 0, E_USER_ERROR, __FILE__, __LINE__);
        }

        $connection = self::getDatabaseConnection();

        $query = 'DELETE FROM '.$connection->quoteIdentifier($oExtension->table).' WHERE id = ?';

        try {
            $connection->executeQuery($query, [
                $oExtension->id,
            ]);
        } catch (DBALException $e) {
            throw new ErrorException($e->getMessage(), 0, $e);
        }

        $oManager->UpdateVirtualClasses();
    }

    /**
     * @param string      $sFolderName
     * @param string|null $sType        - if set to empty, the system will search for the update in customer, custom core, and core
     * @param int|null    $iBuildNumber
     *
     * @deprecated since 5.7.0 - use requireBundleUpdates() instead
     */
    public static function RunUpdate($sFolderName, $sType = null, $iBuildNumber = null)
    {
        if (-1 == $iBuildNumber) {
            $iBuildNumber = null;
        }
        $oUpdateManager = &TCMSUpdateManager::GetInstance();

        // test if folder exists in vendor packages
        if (false === strpos('/', $sFolderName) && '-updates' === substr($sFolderName, -strlen('-updates'))) {
            $packagePathToCheck = array(CHAMELEON_CORE_COMPONENTS, ESONO_PACKAGES);
            $match = false;
            $packageName = null;
            foreach ($packagePathToCheck as $packagePath) {
                $globPattern = $packagePath.'/*/'.$sFolderName;
                foreach (glob($globPattern) as $file) {
                    $packageName = substr($file, strlen($packagePath.'/'), -1 * strlen($sFolderName) - 1);
                    $sFolderName = substr($file, strlen($packagePath."/{$packageName}/"));
                    $match = true;
                    break;
                }
                if ($match) {
                    break;
                }
            }
            if ($match) {
                self::requirePackage($packageName, $iBuildNumber);

                return;
            }
        }
        echo $oUpdateManager->runUpdates($sFolderName, $iBuildNumber);
    }

    /**
     * @param string $bundleName
     * @param int    $highestBuildNumber
     */
    public static function requireBundleUpdates($bundleName, $highestBuildNumber)
    {
        $oUpdateManager = &TCMSUpdateManager::GetInstance();
        echo $oUpdateManager->runUpdates($bundleName, $highestBuildNumber);
    }

    /**
     * @param string      $packageName
     * @param int|string  $iVersion
     * @param string|null $subFolder   @deprecated since 6.2.0 - no longer used
     * @param string      $vendor
     *
     * @throws TPkgCmsException_Log
     *
     * @deprecated since 6.2.0 - use requireBundleUpdates() instead.
     */
    public static function requirePackage($packageName, $iVersion, $subFolder = null, $vendor = 'chameleon-system')
    {
        $pathList = array(CHAMELEON_CORE_COMPONENTS, sprintf('%s/%s/', PATH_VENDORS, $vendor));
        $bundleDir = null;
        foreach ($pathList as $rootPath) {
            $dirName = realpath($rootPath.'/'.$packageName);
            if (false === is_dir($dirName)) {
                $dirName = realpath($rootPath.'/'.strtolower($packageName));
                if (false === is_dir($dirName)) {
                    continue; // package does not exists in rootPath
                }
            }
            $bundleDir = $dirName;
            break; // stop on first match
        }

        if (null === $bundleDir) {
            throw new TPkgCmsException_Log(sprintf('Package "%s" not found, unable to require updates.', $packageName));
        }

        $foundBundle = null;
        foreach (static::getKernel()->getBundles() as $bundle) {
            if ($bundle->getPath() === $bundleDir) {
                $foundBundle = $bundle;
                break;
            }
        }
        if (null === $foundBundle) {
            throw new TPkgCmsException_Log(sprintf('Package "%s" is not registered as a bundle. Only bundles can have updates.', $packageName));
        }

        if (is_string($iVersion) && is_numeric($iVersion)) {
            $version = (int) $iVersion;
        } else {
            $version = $iVersion;
        }
        static::requireBundleUpdates($foundBundle->getName(), $version);
    }

    /**
     * use this method to simple add field connections for fields of $sTargetTable given in $aFields for your cms_field_conf_mlt field in $sTable
     * you can specify one record or leave it null to set for all records of $sTable.
     *
     * @param string $sTable - table name where the cms_field_conf_mlt field is stored
     * @param string $sTargetTable - table name of the table from what the fields will be selected also used in fieldtyp config parameter (sShowFieldsFromTable)
     * @param array $aFields   - field names for that will be a mlt connection added
     * @param null  $sRecordId - record id if you only want to set the connections for a specific record - if null all records of $sTable will be selected
     */
    public static function SetCmsFieldConfMltField($sTable, $sTargetTable, $aFields = array(), $sRecordId = null)
    {
        $sQuery = 'SELECT `'.MySqlLegacySupport::getInstance()->real_escape_string($sTable).'`.*
                   FROM `'.MySqlLegacySupport::getInstance()->real_escape_string($sTable).'`';

        if (!is_null($sRecordId)) {
            if (self::RecordExists($sTable, 'id', $sRecordId)) {
                $sQuery .= ' WHERE `'.MySqlLegacySupport::getInstance()->real_escape_string($sTable)."`.`id` = '".MySqlLegacySupport::getInstance()->real_escape_string($sRecordId)."'";
            } else {
                $logger = self::getLogger();
                $logger->critical(
                    sprintf('UNABLE TO FIND RECORD %s for TABLE %s', $sRecordId, $sTable),
                    [
                        'sTable' => $sTable,
                        'sTargetTable' => $sTargetTable,
                        'aFields' => $aFields,
                        'sRecordId' => $sRecordId,
                    ]
                );

                trigger_error(self::escapeJSOutput("UNABLE TO FIND RECORD '{$sRecordId}' FOR TABLE [{$sTable}] in FILE [".__FILE__.'] ON LINE: '.__LINE__), E_USER_ERROR);
            }
        }

        $rResult = MySqlLegacySupport::getInstance()->query($sQuery);
        while ($aRow = MySqlLegacySupport::getInstance()->fetch_assoc($rResult)) {
            foreach ($aFields as $sFieldName) {
                $sCmsFieldConfID = self::GetTableFieldId(self::GetTableId($sTargetTable), $sFieldName);

                $query = 'SELECT `'.MySqlLegacySupport::getInstance()->real_escape_string($sTable).'_cms_field_conf_mlt`.*
                      FROM `'.MySqlLegacySupport::getInstance()->real_escape_string($sTable).'_cms_field_conf_mlt`
                     WHERE `'.MySqlLegacySupport::getInstance()->real_escape_string($sTable)."_cms_field_conf_mlt`.`source_id` = '".MySqlLegacySupport::getInstance()->real_escape_string($aRow['id'])."'
                       AND `".MySqlLegacySupport::getInstance()->real_escape_string($sTable)."_cms_field_conf_mlt`.`target_id` = '".MySqlLegacySupport::getInstance()->real_escape_string($sCmsFieldConfID)."'";
                if (0 == MySqlLegacySupport::getInstance()->num_rows(MySqlLegacySupport::getInstance()->query($query))) {
                    $sInsertQuery = 'INSERT INTO `'.MySqlLegacySupport::getInstance()->real_escape_string($sTable)."_cms_field_conf_mlt`
                                     SET `source_id` = '".MySqlLegacySupport::getInstance()->real_escape_string($aRow['id'])."',
                                         `target_id` = '".MySqlLegacySupport::getInstance()->real_escape_string($sCmsFieldConfID)."',
                                         `entry_sort` = '0'";
                    self::_RunQuery($sInsertQuery, __LINE__);
                }
            }
        }
    }

    /**
     * adds user groups to a CMS user, optional replaces the current groups.
     *
     * @param string $sUserID        - target user
     * @param array  $aUserGroupIDs  - array of CMS user group IDs, that will be added to the user
     * @param bool   $bReplaceGroups - if true the users current groups will be removed and replaced with the new ones,
     *                               else the new groups will be added
     */
    public static function SetGroupsToUser($sUserID, $aUserGroupIDs, $bReplaceGroups = false)
    {
        $oTableEditor = TTools::GetTableEditorManager('cms_user', $sUserID);
        if (is_array($aUserGroupIDs) && !is_null($oTableEditor) && is_array($oTableEditor->oTableEditor->oTable->sqlData)) {
            $oTableEditor->AllowEditByAll(true);

            if ($bReplaceGroups) { // remove current groups from user
                $sRemoveQuery = "DELETE FROM cms_user_cms_usergroup_mlt WHERE source_id = '".MySqlLegacySupport::getInstance()->real_escape_string($sUserID)."'";
                self::_RunQuery($sRemoveQuery, __LINE__);
            }

            // add new groups
            foreach ($aUserGroupIDs as $sID) {
                $oTableEditor->AddMLTConnection('cms_usergroup_mlt', $sID);
                self::addInfoMessage('added group ID: '.$sID.' to user with ID: '.$sUserID, self::INFO_MESSAGE_LEVEL_INFO);
            }
        }
    }

    /**
     * @param string $sTableName
     * @param int    $iMaxEntriesToConvert
     *
     * @throws ErrorException
     * @throws TPkgCmsException_Log
     */
    public static function convertTableToInnoDB($sTableName, $iMaxEntriesToConvert = 200000)
    {
        $databaseConnection = self::getDatabaseConnection();
        $sStatusQuery = 'SHOW TABLE STATUS LIKE '.$databaseConnection->quote($sTableName);
        $aStatus = MySqlLegacySupport::getInstance()->fetch_assoc(MySqlLegacySupport::getInstance()->query($sStatusQuery));
        if ($aStatus) {
            $sBakName = $sTableName.'_bak_'.date('Y-m-d-H:i:s');
            $quotedTableName = $databaseConnection->quoteIdentifier($sTableName);
            $quotedBakName = $databaseConnection->quoteIdentifier($sBakName);

            $sUniqQueryStart = 'SET UNIQUE_CHECKS=0;';
            self::_RunQuery($sUniqQueryStart, __LINE__);

            $sQuery = "RENAME TABLE $quotedTableName TO $quotedBakName";
            self::_RunQuery($sQuery, __LINE__);

            $sQuery = "CREATE TABLE $quotedTableName LIKE $quotedBakName";
            self::_RunQuery($sQuery, __LINE__);

            $sQuery = "ALTER TABLE $quotedTableName ENGINE = InnoDB AUTO_INCREMENT = ".MySqlLegacySupport::getInstance()->real_escape_string($aStatus['Auto_increment']);
            self::_RunQuery($sQuery, __LINE__);

            $sInsertQuery = "INSERT INTO $quotedTableName (SELECT * FROM $quotedBakName)";
            $sDropQuery = "DROP TABLE $quotedBakName";
            $sUniqQueryEnd = 'SET UNIQUE_CHECKS=1;';

            $sCountQuery = "SELECT COUNT(*) AS count FROM $quotedBakName";
            $aRow = MySqlLegacySupport::getInstance()->fetch_assoc(MySqlLegacySupport::getInstance()->query($sCountQuery));
            if ($aRow && $aRow['count'] < $iMaxEntriesToConvert) {
                self::_RunQuery($sInsertQuery, __LINE__, false);
                self::_RunQuery($sDropQuery, __LINE__);
            } else {
                $aMsgArray = array();
                $aMsgArray[] = 'Did not convert '.$sTableName.' to InnoDb, because the table has over '.$iMaxEntriesToConvert.' entries and operation can produce heavy db load and consume a lot of time.';
                $aMsgArray[] = 'A new table with the correct structure has been created, however it is still empty.';
                $aMsgArray[] = 'Please execute the following queries to remedy this:';
                $aMsgArray[] = $sUniqQueryStart;
                $aMsgArray[] = $sInsertQuery;
                $aMsgArray[] = $sDropQuery;
                $aMsgArray[] = $sUniqQueryEnd;
                self::DisplayErrorMessage(join('<br />', $aMsgArray));
            }
            self::_RunQuery($sUniqQueryEnd, __LINE__);
        }
    }

    /**
     * @param string $sTableName      - table name
     * @param string $sTabName        - Tab-Name
     * @param null   $sTabIdentifier  - if no identifier ist set, then one will be generated based on name
     * @param null   $sTabDescription
     * @param null   $sPlaceTabAfter  - if null, then tab will be placed last
     * @param null   $sTabId
     *
     * @return string|null
     */
    public static function addTabToTable(
        $sTableName,
        $sTabName,
        $sTabIdentifier = null,
        $sTabDescription = null,
        $sPlaceTabAfter = null,
        $sTabId = null
    ) {
        $iTableId = self::GetTableId($sTableName);
        $sOriginalTabName = $sTabName;

        if (null === $sTabIdentifier) {
            $sTabIdentifier = TCMSTableToClass::ConvertToClassString($sTabName);
        }
        $sTabIdentifier = MySqlLegacySupport::getInstance()->real_escape_string($sTabIdentifier);

        $sTabName = MySqlLegacySupport::getInstance()->real_escape_string($sTabName);
        if (null === $sTabDescription) {
            $sTabDescription = '';
        }
        $sTabDescription = MySqlLegacySupport::getInstance()->real_escape_string($sTabDescription);

        if (null === $sTabId) {
            $sTabId = TTools::GetUUID();
        }

        $iMaxPos = 0;
        if (null === $sPlaceTabAfter) {
            $query = "select max(`position`) AS maxpos from cms_tbl_field_tab WHERE `cms_tbl_conf_id` = '{$iTableId}'";
            $iMaxPos = 0;
            if ($aMaxPos = MySqlLegacySupport::getInstance()->fetch_assoc(MySqlLegacySupport::getInstance()->query($query))) {
                $iMaxPos = $aMaxPos['maxpos'] + 1;
            }
        }
        $query = "INSERT INTO `cms_tbl_field_tab` SET `systemname` = '{$sTabIdentifier}', `description` = '{$sTabDescription}', `cms_tbl_conf_id` = '{$iTableId}', `name` = '{$sTabName}', `position` = '{$iMaxPos}', `id`='{$sTabId}'";
        self::_RunQuery($query, __LINE__);

        if (null !== $sPlaceTabAfter) {
            self::SetTabPosition($iTableId, $sOriginalTabName, $sPlaceTabAfter);
        }

        return $sTabId;
    }

    public static function getLogger(): LoggerInterface
    {
        return  \ChameleonSystem\CoreBundle\ServiceLocator::get('monolog.logger.cms_update');
    }

    /**
     * escapes the output (e.g. queries with " + ' + \n) for js.
     *
     * @param string $str
     *
     * @return string
     */
    public static function escapeJSOutput($str)
    {
        $str = addslashes($str);
        $str = str_replace("\n", '\\n', $str);
        $str = str_replace("\r", '\\r', $str);
        $str = str_replace("\t", '\\t', $str);

        return $str;
    }

    /**
     * This method lets old updates insert shop system pages. The table "shop_system_page" has been removed in shop-165.inc.php and
     * old updates would fail when triggered afterwards.
     *
     * You should not use this method in new packages, which already know about the change.
     *
     * @param string $shop_id
     * @param string $name_internal
     * @param string $name
     * @param string $cms_tree_id
     *
     * @deprecated since 6.2.0 - no longer used.
     */
    public static function addShopSystemPage($shop_id, $name_internal, $name, $cms_tree_id = '', $id = null)
    {
        if (self::TableExists('shop_system_page')) {
            $query = "INSERT INTO `shop_system_page`
                      SET `shop_id` = '".$shop_id."',
                          `name_internal` = '".$name_internal."',
                          `name` = '".$name."',
                          `cms_tree_id` = '".$cms_tree_id."'";
            if (null !== $id) {
                $query .= ", `id`='".MySqlLegacySupport::getInstance()->real_escape_string($id)."'";
            }
            self::_RunQuery($query, __LINE__);
        } else {
            $shop = TdbShop::GetNewInstance();
            if ($shop->Load($shop_id)) {
                $portals = $shop->GetFieldCmsPortalIdList();
                foreach ($portals as $portalid) {
                    $query = "INSERT INTO `cms_portal_system_page` SET
                                `cms_portal_id` =   '".MySqlLegacySupport::getInstance()->real_escape_string($portalid)."',
                                `name_internal` =   '".MySqlLegacySupport::getInstance()->real_escape_string($name_internal)."',
                                `name` =            '".MySqlLegacySupport::getInstance()->real_escape_string($name)."',
                                `cms_tree_id` =     '".MySqlLegacySupport::getInstance()->real_escape_string($cms_tree_id)."',
                                `id` =              '".MySqlLegacySupport::getInstance()->real_escape_string(TTools::GetUUID())."'
                            ";
                    self::_RunQuery($query, __LINE__);
                }
            } else {
                self::DisplayErrorMessage('tried to insert system page for non existent shop id');
            }
        }
    }

    /**
     * @param string $moduleClassName
     *
     * @return TDbChangeLogManagerForModules
     */
    public static function getModuleManager($moduleClassName)
    {
        return new TDbChangeLogManagerForModules($moduleClassName);
    }

    /**
     * initialises a table with parent-id structure with left and right values for nested set.
     *
     * @param string $sTable
     * @param int    $iCurrentLft
     * @param string $sParentId
     * @param string $sParentIdField
     * @param string $sOrderByField
     *
     * @return int
     */
    public static function AddLftAndRgtValuesForNestedSet($sTable, $iCurrentLft = 0, $sParentId = '', $sParentIdField = 'parent_id', $sOrderByField = null)
    {
        $sQuery = 'SELECT * FROM `'.MySqlLegacySupport::getInstance()->real_escape_string($sTable).'` WHERE `'.MySqlLegacySupport::getInstance()->real_escape_string($sParentIdField)."` = '".MySqlLegacySupport::getInstance()->real_escape_string($sParentId)."'";
        if ($sOrderByField) {
            $sQuery .= ' ORDER BY `'.MySqlLegacySupport::getInstance()->real_escape_string($sOrderByField).'`';
        }
        $rRes = MySqlLegacySupport::getInstance()->query($sQuery);
        while ($aRow = MySqlLegacySupport::getInstance()->fetch_assoc($rRes)) {
            ++$iCurrentLft;
            $sUpdateSql = 'UPDATE `'.MySqlLegacySupport::getInstance()->real_escape_string($sTable)."` SET `lft` = '".MySqlLegacySupport::getInstance()->real_escape_string($iCurrentLft)."' WHERE `id` = '".MySqlLegacySupport::getInstance()->real_escape_string($aRow['id'])."'";
            self::_RunQuery($sUpdateSql, __LINE__);
            $iCurrentLft = self::AddLftAndRgtValuesForNestedSet($sTable, $iCurrentLft, $aRow['id'], $sParentIdField, $sOrderByField);
            ++$iCurrentLft;
            $sUpdateSql = 'UPDATE `'.MySqlLegacySupport::getInstance()->real_escape_string($sTable)."` SET `rgt` = '".MySqlLegacySupport::getInstance()->real_escape_string($iCurrentLft)."' WHERE `id` = '".MySqlLegacySupport::getInstance()->real_escape_string($aRow['id'])."'";
            self::_RunQuery($sUpdateSql, __LINE__);
        }

        return $iCurrentLft;
    }

    /**
     * deletes the complete table configuration and the table itself
     * also deletes related MLT tables
     * please note! does NOT delete fields connecting to this table!
     *
     * handle with care!
     *
     * @param string $sTableName
     */
    public static function deleteTable($sTableName)
    {
        $sTableID = self::GetTableId($sTableName);

        self::deleteRelatedTables($sTableName);

        $mysql = MySqlLegacySupport::getInstance();
        $quotedTableId = self::getDatabaseConnection()->quote($sTableID);

        // delete field specific rights
        $sQuery = "SELECT * FROM `cms_field_conf` WHERE `cms_tbl_conf_id` = $quotedTableId";
        $result = $mysql->query($sQuery);
        while ($row = $mysql->fetch_assoc($result)) {
            $sQuery = "DELETE FROM `cms_field_conf_cms_usergroup_mlt` WHERE `source_id` = '".$mysql->real_escape_string($row['id'])."'";
            self::_RunQuery($sQuery, __LINE__);
        }

        // delete right settings for table
        $sQuery = "DELETE FROM `cms_tbl_conf_cms_role_mlt` WHERE `source_id` = $quotedTableId";
        self::_RunQuery($sQuery, __LINE__);

        $sQuery = "DELETE FROM `cms_tbl_conf_cms_role1_mlt` WHERE `source_id` = $quotedTableId";
        self::_RunQuery($sQuery, __LINE__);

        $sQuery = "DELETE FROM `cms_tbl_conf_cms_role2_mlt` WHERE `source_id` = $quotedTableId";
        self::_RunQuery($sQuery, __LINE__);

        $sQuery = "DELETE FROM `cms_tbl_conf_cms_role3_mlt` WHERE `source_id` = $quotedTableId";
        self::_RunQuery($sQuery, __LINE__);

        $sQuery = "DELETE FROM `cms_tbl_conf_cms_role4_mlt` WHERE `source_id` = $quotedTableId";
        self::_RunQuery($sQuery, __LINE__);

        $sQuery = "DELETE FROM `cms_tbl_conf_cms_role5_mlt` WHERE `source_id` = $quotedTableId";
        self::_RunQuery($sQuery, __LINE__);

        $sQuery = "DELETE FROM `cms_tbl_conf_cms_role6_mlt` WHERE `source_id` = $quotedTableId";
        self::_RunQuery($sQuery, __LINE__);

        $sQuery = "DELETE FROM `cms_tbl_conf_cms_role7_mlt` WHERE `source_id` = $quotedTableId";
        self::_RunQuery($sQuery, __LINE__);

        // delete table settings
        $sQuery = "DELETE FROM `cms_tbl_conf_index` WHERE `cms_tbl_conf_id` = $quotedTableId";
        self::_RunQuery($sQuery, __LINE__);

        $sQuery = "DELETE FROM `cms_tbl_conf_restrictions` WHERE `cms_tbl_conf_id` = $quotedTableId";
        self::_RunQuery($sQuery, __LINE__);

        $sQuery = "DELETE FROM `cms_tbl_display_list_fields` WHERE `cms_tbl_conf_id` = $quotedTableId";
        self::_RunQuery($sQuery, __LINE__);

        $sQuery = "DELETE FROM `cms_tbl_display_orderfields` WHERE `cms_tbl_conf_id` = $quotedTableId";
        self::_RunQuery($sQuery, __LINE__);

        $sQuery = "DELETE FROM `cms_tbl_extension` WHERE `cms_tbl_conf_id` = $quotedTableId";
        self::_RunQuery($sQuery, __LINE__);

        $sQuery = "DELETE FROM `cms_tbl_list_class` WHERE `cms_tbl_conf_id` = $quotedTableId";
        self::_RunQuery($sQuery, __LINE__);

        $sQuery = "DELETE FROM `cms_tbl_field_tab` WHERE `cms_tbl_conf_id` = $quotedTableId";
        self::_RunQuery($sQuery, __LINE__);

        $sQuery = "DELETE FROM `cms_tpl_module_cms_tbl_conf_mlt` WHERE `source_id` = $quotedTableId";
        self::_RunQuery($sQuery, __LINE__);

        $sQuery = "DELETE FROM `cms_tbl_conf` WHERE `id` = $quotedTableId";
        self::_RunQuery($sQuery, __LINE__);

        // delete the fields
        $sQuery = "DELETE FROM `cms_field_conf` WHERE `cms_tbl_conf_id` = $quotedTableId";
        self::_RunQuery($sQuery, __LINE__);

        // finally drop the table
        $sQuery = 'DROP TABLE IF EXISTS `'.$mysql->real_escape_string($sTableName).'`';
        self::_RunQuery($sQuery, __LINE__);
    }

    /**
     * deletes all related tables (MLT) for $sTableName.
     *
     * @param string $sTableName
     */
    public static function deleteRelatedTables($sTableName)
    {
        $oTableConf = TdbCmsTblConf::GetNewInstance();
        $oTableConf->LoadFromField('name', $sTableName);
        $oFields = $oTableConf->GetFields($oTableConf, true);

        $mysql = MySqlLegacySupport::getInstance();

        while ($oField = $oFields->Next()) {
            $oFieldType = $oField->oDefinition->GetFieldType();
            if ('mlt' === $oFieldType->sqlData['base_type']) {
                /** @var $oField TCMSFieldLookupMultiselect */
                $sMLTTableName = $oField->GetMLTTableName();
                $sQuery = 'DROP TABLE IF EXISTS `'.$mysql->real_escape_string($sMLTTableName).'`';
                self::_RunQuery($sQuery, __LINE__);
            }
        }
    }

    /**
     * deletes a field from a table including the config.
     *
     * handle with care!
     *
     * @param string $sTableName
     * @param string $sFieldName
     */
    public static function deleteField($sTableName, $sFieldName)
    {
        $mysql = MySqlLegacySupport::getInstance();
        $sTableID = self::GetTableId($sTableName);
        $sFieldID = $mysql->real_escape_string(self::GetTableFieldId($sTableID, $sFieldName));
        $escapedTableName = $mysql->real_escape_string($sTableName);
        $escapedFieldName = $mysql->real_escape_string($sFieldName);

        $sQuery = "DELETE FROM `cms_field_conf_cms_usergroup_mlt` WHERE `source_id` = '".$sFieldID."'";
        self::_RunQuery($sQuery, __LINE__);

        $sQuery = "DELETE FROM `cms_field_conf` WHERE `id` = '".$sFieldID."'";
        self::_RunQuery($sQuery, __LINE__);

        $sQuery = "SELECT COUNT(*) FROM `information_schema`.`columns` WHERE `table_name` = '".$escapedTableName."' AND `column_name` = '".$escapedFieldName."'";
        $statement = self::_RunQuery($sQuery, __LINE__);
        $count = intval($statement->fetchColumn());
        if ($count > 0) {
            $sQuery = 'ALTER TABLE `'.$escapedTableName.'` DROP `'.$escapedFieldName.'`';
            self::_RunQuery($sQuery, __LINE__);
        }
    }

    /**
     * deletes a web module and all configuration
     * please note: fails silently if the module was not found
     * handle with care!
     *
     * @param string $sModuleClassName - the module is identified by the value of field: classname
     */
    public static function deleteWebModule($sModuleClassName)
    {
        $mysql = MySqlLegacySupport::getInstance();

        $sQuery = "SELECT * FROM `cms_tpl_module` WHERE `classname` = '".$mysql->real_escape_string($sModuleClassName)."'";
        $result = $mysql->query($sQuery);
        if (0 != $mysql->num_rows($result)) {
            $aModule = $mysql->fetch_assoc($result);
            $sModuleID = $aModule['id'];

            $sQuery = "DELETE FROM `cms_tpl_module_cms_portal_mlt` WHERE `source_id` = '".$mysql->real_escape_string($sModuleID)."'";
            self::_RunQuery($sQuery, __LINE__);

            $sQuery = "DELETE FROM `cms_tpl_module_cms_tbl_conf_mlt` WHERE `source_id` = '".$mysql->real_escape_string($sModuleID)."'";
            self::_RunQuery($sQuery, __LINE__);

            $sQuery = "DELETE FROM `cms_tpl_module_cms_usergroup_mlt` WHERE `source_id` = '".$mysql->real_escape_string($sModuleID)."'";
            self::_RunQuery($sQuery, __LINE__);

            $sQuery = "DELETE FROM `cms_tpl_module_instance` WHERE `cms_tpl_module_id` = '".$mysql->real_escape_string($sModuleID)."'";
            self::_RunQuery($sQuery, __LINE__);

            $sQuery = "DELETE FROM `cms_tpl_module` WHERE `id` = '".$mysql->real_escape_string($sModuleID)."'";
            self::_RunQuery($sQuery, __LINE__);
        }
    }

    /**
     * @return Doctrine\DBAL\Connection
     */
    public static function getDatabaseConnection()
    {
        return \ChameleonSystem\CoreBundle\ServiceLocator::get('database_connection');
    }

    /**
     * initializes lft and rgt values for all entries in the table.
     *
     * @param string $tableName
     * @param string $parentIdFieldName
     * @param string $entrySortField
     */
    public static function initializeNestedSet($tableName, $parentIdFieldName = 'parent_id', $entrySortField = 'position')
    {
        /** @var $helperServiceFactory \ChameleonSystem\CoreBundle\TableEditor\NestedSet\NestedSetHelperFactoryInterface */
        $helperServiceFactory = \ChameleonSystem\CoreBundle\ServiceLocator::get('chameleon_system_core.table_editor_nested_set_helper_factory');
        $helperService = $helperServiceFactory->createNestedSetHelper($tableName, $parentIdFieldName, $entrySortField);
        $helperService->initializeTree();
    }

    /**
     * Set field user group right for a table to show them in mlt field connection.
     * For example in variant sets article fields.
     *
     * @param string $tableName
     * @param string $userGroupSystemName (cms_admin)
     * @param array $fieldNameList if array is empty grand rights to all table fields
     *
     * @return bool|void
     */
    public static function setTableFieldExtraUserGroupRights($tableName, $userGroupSystemName, array $fieldNameList = array())
    {
        $tableId = self::GetTableId($tableName);
        if ('' == $tableId) {
            self::DisplayErrorMessage("ERROR: Unable to find table [{$tableName}]");

            return false;
        }
        $userGroupId = self::GetUserGroupIdByKey($userGroupSystemName);
        if (false === $userGroupId) {
            self::DisplayErrorMessage("ERROR: Unable to find user group [{$userGroupSystemName}]");

            return false;
        }
        if (0 == count($fieldNameList)) {
            $oTableConf = TdbCmsTblConf::GetNewInstance();
            $oTableConf->LoadFromField('name', $tableName);
            $fieldList = $oTableConf->GetFieldCmsFieldConfMltList();
            while ($field = $fieldList->Next()) {
                $fieldNameList[] = $field->fieldName;
            }
        }
        foreach ($fieldNameList as $fieldName) {
            if (false === self::FieldExists($tableName, $fieldName)) {
                self::DisplayErrorMessage("ERROR: Unable to find field [{$fieldName}] in table [{$tableName}]");
                continue;
            }
            $fieldId = self::GetTableFieldId($tableId, $fieldName);
            $tableEditor = TTools::GetTableEditorManager('cms_field_conf', $fieldId);
            $tableEditor->AllowEditByAll(true);
            $tableEditor->AddMLTConnection('cms_usergroup_mlt', $userGroupId);
            $tableEditor->AllowEditByAll(false);
        }
    }

    /**
     * @return AutoclassesRequestCacheDataAccess
     */
    private static function getAutoclassesDataAccessRequestCache()
    {
        return \ChameleonSystem\CoreBundle\ServiceLocator::get('chameleon_system_autoclasses.data_access.autoclasses_request_cache');
    }

    /**
     * @return AutoclassesCacheWarmer
     */
    private static function getAutoclassesCacheWarmer()
    {
        return \ChameleonSystem\CoreBundle\ServiceLocator::get('chameleon_system_autoclasses.cache_warmer');
    }

    /**
     * A static wrapper for the respective method in the SnippetChainModifier.
     *
     * @see \ChameleonSystem\ViewRenderer\SnippetChain\SnippetChainModifier::addToSnippetChain()
     *
     * @param string      $pathToAdd
     * @param string|null $afterThisPath
     * @param string[]    $toTheseThemes
     *
     * @throws DataAccessException
     */
    public static function addToSnippetChain($pathToAdd, $afterThisPath = null, array $toTheseThemes = array())
    {
        self::getSnippetChainModifier()->addToSnippetChain($pathToAdd, $afterThisPath, $toTheseThemes);
    }

    /**
     * A static wrapper for the respective method in the SnippetChainModifier.
     *
     * @see \ChameleonSystem\ViewRenderer\SnippetChain\SnippetChainModifier::removeFromSnippetChain()
     *
     * @param string   $pathToRemove
     * @param string[] $fromTheseThemes
     *
     * @throws DataAccessException
     */
    public static function removeFromSnippetChain($pathToRemove, array $fromTheseThemes = array())
    {
        self::getSnippetChainModifier()->removeFromSnippetChain($pathToRemove, $fromTheseThemes);
    }

    /**
     * Inserts one or more rows into the given table.
     *
     * @param int                $line
     * @param MigrationQueryData $migrationQueryData
     */
    public static function insert($line, MigrationQueryData $migrationQueryData)
    {
        self::executeUpdateOperation($line, 'chameleon_system_database_migration.query.insert', $migrationQueryData);
    }

    /**
     * Updates one or more rows in the given table.
     *
     * @param int                $line
     * @param MigrationQueryData $migrationQueryData
     */
    public static function update($line, MigrationQueryData $migrationQueryData)
    {
        self::executeUpdateOperation($line, 'chameleon_system_database_migration.query.update', $migrationQueryData);
    }

    /**
     * Deletes rows from the given table.
     *
     * @param int                $line
     * @param MigrationQueryData $migrationQueryData
     */
    public static function delete($line, MigrationQueryData $migrationQueryData)
    {
        self::executeUpdateOperation($line, 'chameleon_system_database_migration.query.delete', $migrationQueryData);
    }

    /**
     * @param int                $line
     * @param string             $serviceId
     * @param MigrationQueryData $migrationQueryData
     */
    private static function executeUpdateOperation($line, $serviceId, MigrationQueryData $migrationQueryData)
    {
        /** @var QueryInterface $operation */
        $operation = \ChameleonSystem\CoreBundle\ServiceLocator::get($serviceId);
        try {
            list($query, $queryParams) = $operation->execute($migrationQueryData);
            self::outputSuccess($line, $query, $queryParams);
            self::logSuccess($line, $query, $queryParams);
        } catch (\Exception $e) {
            self::outputError($e);
            self::logError($line, $e);
        }
    }

    /**
     * Creates a MigrationQueryData object, avoiding the PHP 5.3 limitation of not being able to call "fluent constructors".
     *
     * @param string $tableName
     * @param string $language
     *
     * @return MigrationQueryData
     */
    public static function createMigrationQueryData($tableName, $language)
    {
        return new MigrationQueryData($tableName, $language);
    }

    /**
     * @return ExpressionBuilder
     */
    public static function createExpressionBuilder()
    {
        return new ExpressionBuilder();
    }

    /**
     * @param int    $line
     * @param string $query
     * @param array  $queryParams
     */
    private static function outputSuccess($line, $query, $queryParams)
    {
        foreach ($queryParams as $key => $queryParam) {
            $queryParams[$key] = htmlentities($queryParam);
        }

        TCMSUpdateManager::GetInstance()->addSuccessQuery($query.' WITH '.print_r($queryParams, true), $line);
    }

    /**
     * @param Exception $exception
     */
    private static function outputError($exception)
    {
        TCMSUpdateManager::GetInstance()->addException($exception);
    }

    /**
     * @param int    $line
     * @param string $query
     * @param array  $queryParams
     */
    private static function logSuccess($line, $query, $queryParams)
    {
        $logger = self::getLogger();
        $logger->info(
            sprintf('Query successfully executed in line %s: %s with %s', $line, $query, print_r($queryParams, true)),
            [
                'originalQuery' => $query,
                'parameters' => $queryParams,
            ]
        );
    }

    /**
     * @param int       $line
     * @param Exception $exception
     */
    private static function logError($line, $exception)
    {
        $logger = self::getLogger();
        $logger->error(sprintf('SQL error in line %s: %s', $line, (string) $exception));
    }

    /**
     * Returns a list containing the IDs of all portals in the system.
     *
     * @return string[]
     */
    public static function getPortalIdList()
    {
        $query = 'SELECT `id` FROM `cms_portal`';
        $databaseConnection = self::getDatabaseConnection();
        $result = $databaseConnection->fetchAll($query);
        $portalIdList = array();
        foreach ($result as $row) {
            $portalIdList[] = $row['id'];
        }

        return $portalIdList;
    }

    /**
     * @param string $portalId
     *
     * @return string[]
     */
    public static function getLanguageIsoListForPortal($portalId)
    {
        $query = 'SELECT DISTINCT lang.`iso_6391`
          FROM `cms_language` AS lang
          JOIN `cms_portal_cms_language_mlt` AS mlt
          ON lang.`id` = mlt.`target_id`
          WHERE mlt.`source_id` = ?';
        $databaseConnection = self::getDatabaseConnection();
        $result = $databaseConnection->fetchAll($query, array($portalId));
        $languageList = array();
        foreach ($result as $row) {
            $languageList[] = $row['iso_6391'];
        }

        return $languageList;
    }

    /**
     * Returns the ISO 639-1 code of the system's base language.
     *
     * @return string
     */
    public static function getSystemBaseLanguageIso()
    {
        $query = 'SELECT lang.`iso_6391`
          FROM `cms_language` AS lang
          JOIN `cms_config` AS conf
          ON lang.`id` = conf.`translation_base_language_id`';
        $databaseConnection = self::getDatabaseConnection();

        return $databaseConnection->fetchColumn($query);
    }

    /**
     * @return FieldTranslationUtil
     */
    private static function getFieldTranslationUtil()
    {
        return \ChameleonSystem\CoreBundle\ServiceLocator::get('chameleon_system_core.util.field_translation');
    }

    /**
     * @return LanguageServiceInterface
     */
    private static function getLanguageService()
    {
        return \ChameleonSystem\CoreBundle\ServiceLocator::get('chameleon_system_core.language_service');
    }

    /**
     * @return MigrationRecorder
     */
    private static function getMigrationRecorder()
    {
        return \ChameleonSystem\CoreBundle\ServiceLocator::get('chameleon_system_database_migration.recorder.migration_recorder');
    }

    /**
     * @return KernelInterface
     */
    private static function getKernel()
    {
        return \ChameleonSystem\CoreBundle\ServiceLocator::get('kernel');
    }

    /**
     * @return MigrationCounterManagerInterface
     */
    private static function getMigrationCounterManager()
    {
        return \ChameleonSystem\CoreBundle\ServiceLocator::get('chameleon_system_core.counter.migration_counter_manager');
    }

    /**
     * @return MigrationRecorderStateHandler
     */
    private static function getMigrationRecorderStateHandler()
    {
        return ServiceLocator::get('chameleon_system_database_migration.recorder.migration_recorder_state_handler');
    }

    /**
     * @return SnippetChainModifier
     */
    private static function getSnippetChainModifier()
    {
        return ServiceLocator::get('chameleon_system_view_renderer.snippet_chain.snippet_chain_modifier');
    }
}
