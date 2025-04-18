<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use ChameleonSystem\CmsBackendBundle\BackendSession\BackendSessionInterface;
use ChameleonSystem\CoreBundle\Interfaces\FlashMessageServiceInterface;
use ChameleonSystem\CoreBundle\ServiceLocator;
use ChameleonSystem\SecurityBundle\Service\SecurityHelperAccess;
use ChameleonSystem\SecurityBundle\Voter\CmsPermissionAttributeConstants;
use Doctrine\DBAL\Connection;

/**
 * manages the TableEditor classes.
 */
class TCMSTableEditorManager
{
    /**
     * definition of the table.
     *
     * @var TdbCmsTblConf|null
     */
    public $oTableConf;

    /**
     * object manages the table operations.
     *
     * @var TCMSTableEditor|null
     */
    public $oTableEditor;

    /**
     * record id.
     *
     * @var string|null
     */
    public $sId;

    /**
     * table conf id.
     *
     * @var string
     */
    public $sTableId;

    /**
     * Enter description here...
     *
     * @var string
     */
    public $sRestriction;

    /**
     * Enter description here...
     *
     * @var string
     */
    public $sRestrictionField;

    /**
     * if set to true, any delete checks are ignored for the item.
     *
     * @var bool
     */
    protected $bAllowDeleteByAll = false;

    /**
     * if set to true, no user access rights will be checked.
     *
     * @var bool
     */
    protected $bAllowEditByAll = false;

    /**
     * set to true via AllowEditByWebUser() method if you want to save user records from a web module.
     *
     * @var bool
     */
    protected $bAllowEditByWebUser = false;

    /**
     * TCMSMessageManager consumer spot name.
     */
    public const MESSAGE_MANAGER_CONSUMER = 'MTTableEditorMessages';
    /**
     * @var Connection
     */
    private $databaseConnection;

    /**
     * use this to change the "allow edit by all" setting.
     *
     * @param bool $bSetting
     */
    public function AllowEditByAll($bSetting)
    {
        $this->bAllowEditByAll = $bSetting;
        if (null !== $this->oTableEditor) {
            $this->oTableEditor->AllowEditByAll($bSetting);
        }
    }

    /**
     * use this method to allow external delete calls without checking user rights (as may be required when
     * an external delete is called).
     *
     * @param bool $bAllowDeleteByAll
     */
    public function AllowDeleteByAll($bAllowDeleteByAll = true)
    {
        $this->bAllowDeleteByAll = $bAllowDeleteByAll;
        if (null !== $this->oTableEditor) {
            $this->oTableEditor->AllowDeleteByAll($bAllowDeleteByAll);
        }
    }

    /**
     * set to true if you want to save user records from a web module
     * you know what you are doing, right?
     *
     * @param bool $bAllowEditByWebUser
     */
    public function AllowEditByWebUser($bAllowEditByWebUser)
    {
        $this->bAllowEditByWebUser = $bAllowEditByWebUser;
        if (null !== $this->oTableEditor) {
            $this->oTableEditor->AllowEditByWebUser($bAllowEditByWebUser);
        }
    }

    /**
     * if true, fields in hidden mode are also updated.
     *
     * @param bool $bForceHiddenFieldWriteOnSave
     */
    public function ForceHiddenFieldWriteOnSave($bForceHiddenFieldWriteOnSave = true)
    {
        if (null !== $this->oTableEditor) {
            $this->oTableEditor->ForceHiddenFieldWriteOnSave($bForceHiddenFieldWriteOnSave);
        }
    }

    /**
     * initalises the table editor object.
     *
     * @param string $sTableId
     * @param string|null $sId
     * @param string|null $sLanguageID - overwrites the user language and loads the record in this language instead
     *
     * @return bool - returns false if the record doesn't exist
     */
    public function Init($sTableId, $sId = null, $sLanguageID = null)
    {
        $this->sTableId = $sTableId;
        $this->sId = $sId;

        $this->oTableConf = TdbCmsTblConf::GetNewInstance();
        if (null === $sLanguageID) {
            /** @var BackendSessionInterface $backendSession */
            $backendSession = ServiceLocator::get('chameleon_system_cms_backend.backend_session');

            $this->oTableConf->SetLanguage($backendSession->getCurrentEditLanguageId());
        } else {
            $this->oTableConf->SetLanguage($sLanguageID);
        }

        $this->oTableConf->Load($this->sTableId);

        // check if record exists
        $record = new TCMSRecord();
        $record->table = $this->oTableConf->sqlData['name'];
        $recordFound = null !== $sId && true === $record->LoadWithCaching($sId);
        unset($record);

        if (null !== $sId && false === $recordFound) {
            return false;
        }

        $this->oTableEditor = $this->TableEditorFactory();
        $this->oTableEditor->AllowEditByAll($this->bAllowEditByAll);
        $this->oTableEditor->AllowEditByWebUser($this->bAllowEditByWebUser);
        $this->oTableEditor->sRestriction = $this->sRestriction;
        $this->oTableEditor->sRestrictionField = $this->sRestrictionField;
        $this->oTableEditor->oTableConf->SetLanguage($this->oTableConf->GetLanguage());

        return $recordFound;
    }

    /**
     * allow an external object to call a method on the encapsulated TCMSTableEditor object.
     *
     * @param string $sFunctionName - function name
     * @param array $aParameters
     */
    public function HandleExternalFunctionCall($sFunctionName, $aParameters = [])
    {
        // check if the function exists
        if (!method_exists($this->oTableEditor, $sFunctionName)) {
            trigger_error('Error: Attempting to call a method ['.$sFunctionName.'] on a TCMSTableEditor object that does not exist in that class', E_USER_ERROR);
        } else {
            $this->oTableEditor->DefineInterface();
            if (false === array_search($sFunctionName, $this->oTableEditor->methodCallAllowed)) {
                trigger_error('Error: Attempting to call a method ['.$sFunctionName.'] on a TCMSTableEditor object that is not allowed to be called from outside', E_USER_ERROR);
            }
        }
        $iParameterCounter = count($aParameters);
        // notice: no handling of arrays more than 2 elements at the moment!
        if ($iParameterCounter > 0) {
            if (1 == $iParameterCounter) {
                return $this->oTableEditor->$sFunctionName($aParameters[0]);
            }
            if (2 == $iParameterCounter) {
                return $this->oTableEditor->$sFunctionName($aParameters[0], $aParameters[1]);
            }
            trigger_error('Error: Attempting to call a method ['.$sFunctionName.'] on a TCMSTableEditor object that it can not handle the parameter array which has more than 2 elements', E_USER_ERROR);

            return false;
        } else {
            return $this->oTableEditor->$sFunctionName();
        }
    }

    /**
     * Load the table editor class that is configured in cms_tbl_conf.
     *
     * @return TCMSTableEditor
     */
    public function TableEditorFactory()
    {
        // check if table editor is extended
        if (!empty($this->oTableConf->sqlData['table_editor_class'])) {
            $sClassName = $this->oTableConf->sqlData['table_editor_class'];
            $oTableEditor = new $sClassName();
        } else {
            $oTableEditor = new TCMSTableEditor();
        }
        $oTableEditor->setDatabaseConnection($this->getDatabaseConnection());
        $oTableEditor->AllowEditByAll($this->bAllowEditByAll);
        $oTableEditor->Init($this->sTableId, $this->sId);

        return $oTableEditor;
    }

    /**
     * saves a record.
     *
     * @param array $postData
     * @param bool $bDataIsInSQLForm
     *
     * @return TCMSstdClass|false
     */
    public function Save($postData, $bDataIsInSQLForm = false)
    {
        $oRecordData = false;
        if ($this->oTableEditor->AllowEdit($postData) && !$this->IsRecordLocked()) {
            $oRecordData = $this->oTableEditor->Save($postData, $bDataIsInSQLForm);
            $this->sId = $this->oTableEditor->sId;
            $this->RefreshLock();
        }

        return $oRecordData;
    }

    /**
     * save only one field of a record.
     *
     * @param string $sFieldName
     * @param scalar $sFieldContent
     * @param bool $bTriggerPostSaveHook
     *
     * @return bool
     */
    public function SaveField($sFieldName, $sFieldContent, $bTriggerPostSaveHook = false)
    {
        $bSaved = false;
        if ($this->oTableEditor->AllowEdit([$sFieldName => $sFieldContent]) && !$this->IsRecordLocked()) {
            $this->oTableEditor->SaveField($sFieldName, $sFieldContent, $bTriggerPostSaveHook);
            $this->RefreshLock();
            $bSaved = true;
        }

        return $bSaved;
    }

    /**
     * creates a new empty record.
     *
     * @return TCMSStdClass|null
     */
    public function Insert()
    {
        $oReturnData = null;
        if (!$this->hasNewPermission()) {
            return $oReturnData;
        }
        $oReturnData = $this->oTableEditor->Insert();
        if (($oReturnData && !is_null($oReturnData->id)) || (!$oReturnData && isset($this->oTableEditor) && isset($this->oTableEditor->oTable))) {
            $this->sId = $this->oTableEditor->sId;
            $this->RefreshLock();

            if ('_mlt' === substr($this->sRestrictionField, -4)) {
                $sourceTable = substr($this->sRestrictionField, 0, -4);

                $targetTable = $this->oTableConf->sqlData['name'];
                $MLTTable = $sourceTable.'_'.$targetTable.'_mlt';
                $mltQuery = 'INSERT INTO `'.MySqlLegacySupport::getInstance()->real_escape_string($MLTTable)."` SET `source_id` ='".MySqlLegacySupport::getInstance()->real_escape_string($this->sRestriction)."', `target_id` = '".MySqlLegacySupport::getInstance()->real_escape_string($this->sId)."'";
                MySqlLegacySupport::getInstance()->query($mltQuery);
            }
        }

        return $oReturnData;
    }

    /**
     * delete record with all relevant connected sub records by ID.
     *
     * @param string $id - null by default
     *
     * @return bool
     */
    public function Delete($id = null)
    {
        $bReturnVal = false;
        if ($this->IsAllowedDelete()) {
            if (null !== $id) {
                $this->sId = $id;
                $this->oTableEditor->Init($this->sTableId, $this->sId); // reinit for new id
            }
            $flashMessageService = $this->getFlashMessageService();
            $sConsumerName = self::MESSAGE_MANAGER_CONSUMER;

            if (TTools::RecordExists($this->oTableEditor->oTableConf->sqlData['name'], 'id', $this->sId)) {
                $sRecordName = $this->oTableEditor->oTable->GetName();

                $this->oTableEditor->Delete($this->sId);
                $this->sId = $this->oTableEditor->sId;
                $flashMessageService->AddMessage(
                    $sConsumerName,
                    'TABLEEDITOR_DELETE_RECORD_SUCCESS',
                    ['id' => $this->sId, 'name' => $sRecordName]
                );
                $bReturnVal = true;
            } else {
                $flashMessageService->AddMessage(
                    $sConsumerName,
                    'TABLEEDITOR_DELETE_RECORD_DOES_NOT_EXIST',
                    ['id' => $this->sId, 'name' => 'Unknown']
                );
            }
        }

        return $bReturnVal;
    }

    /**
     * returns false if record is locked, the delete right is missing
     * or record is marked in delete blacklist.
     *
     * @return bool
     */
    protected function IsAllowedDelete()
    {
        $bAllowDelete = true;
        if ($bAllowDelete) {
            $bAllowDelete = !$this->IsRecordLocked();
        }
        if ($bAllowDelete) {
            $bAllowDelete = $this->hasDeletePermission();
        }

        return $bAllowDelete;
    }

    /**
     * inserts a copy record of current postdata.
     *
     * @param array $postData
     *
     * @return TCMSstdClass
     */
    public function Copy($postData)
    {
        $returnVal = false;
        if (!$this->IsRecordLocked() && $this->hasNewPermission()) {
            $returnVal = $this->oTableEditor->Copy($postData);
            $this->sId = $this->oTableEditor->sId;
            $this->RefreshLock();
        }

        return $returnVal;
    }

    /**
     * inserts a copy of the record based on the data from the database instead of postdata.
     *
     * @param bool $languageCopy
     * @param array $aOverloadedFields fields to copy with given value
     * @param bool $bCopyAllLanguages Set to true if you want top copy alle language fields
     *
     * @return TCMSstdClass|bool
     */
    public function DatabaseCopy($languageCopy = false, $aOverloadedFields = [], $bCopyAllLanguages = true)
    {
        $returnVal = false;

        if (!$this->IsRecordLocked()) {
            if (($languageCopy && $this->hasNewLanguagePermission()) || (!$languageCopy && $this->hasNewPermission())) {
                $returnVal = $this->oTableEditor->DatabaseCopy($languageCopy, $aOverloadedFields, $bCopyAllLanguages);
                $this->sId = $this->oTableEditor->sId;
                $this->RefreshLock();
            }
        }

        return $returnVal;
    }

    /**
     * @return bool
     */
    protected function hasNewPermission()
    {
        if (true === $this->bAllowEditByAll) {
            return true;
        }
        /** @var SecurityHelperAccess $securityHelper */
        $securityHelper = ServiceLocator::get(SecurityHelperAccess::class);

        return $securityHelper->isGranted(CmsPermissionAttributeConstants::TABLE_EDITOR_NEW, $this->oTableConf->sqlData['name']);
    }

    /**
     * @return bool
     */
    protected function hasNewLanguagePermission()
    {
        if (true === $this->bAllowEditByAll) {
            return true;
        }

        /** @var SecurityHelperAccess $securityHelper */
        $securityHelper = ServiceLocator::get(SecurityHelperAccess::class);

        return $securityHelper->isGranted(CmsPermissionAttributeConstants::TABLE_EDITOR_NEW_LANGUAGE, $this->oTableConf->sqlData['name']);
    }

    /**
     * @return bool
     */
    protected function hasDeletePermission()
    {
        if (true === $this->bAllowDeleteByAll) {
            return true;
        }

        /** @var SecurityHelperAccess $securityHelper */
        $securityHelper = ServiceLocator::get(SecurityHelperAccess::class);

        return $securityHelper->isGranted(CmsPermissionAttributeConstants::TABLE_EDITOR_DELETE, $this->oTableConf->sqlData['name']);
    }

    /**
     * removes one connection from mlt.
     *
     * @param string $sFieldName mlt fieldname (connected table name)
     * @param string $iConnectedID the connected record id that will be removed
     */
    public function RemoveMLTConnection($sFieldName, $iConnectedID)
    {
        if (!empty($sFieldName) && !empty($iConnectedID)) {
            if (!$this->IsRecordLocked()) {
                $this->oTableEditor->RemoveMLTConnection($sFieldName, $iConnectedID);
                $this->RefreshLock();
            }
        }
    }

    /**
     * adds one connection to mlt.
     *
     * @param string $sFieldName - mlt fieldname (connected table name)
     * @param int $iConnectedID - the connected record id that will be added
     */
    public function AddMLTConnection($sFieldName, $iConnectedID)
    {
        if (!empty($sFieldName) && !empty($iConnectedID)) {
            if (!$this->IsRecordLocked()) {
                $this->oTableEditor->AddMLTConnection($sFieldName, $iConnectedID);
                $this->RefreshLock();
            }
        }
    }

    /**
     * Set new order position and updates order position in all other
     * connected connections behind the new position.
     *
     * @param string $sFieldName
     * @param string $sConnectedId
     * @param int $iPosition
     */
    public function updateMLTSortOrder($sFieldName, $sConnectedId, $iPosition)
    {
        if (!empty($sFieldName) && !empty($sConnectedId)) {
            if (!$this->IsRecordLocked()) {
                $this->oTableEditor->updateMLTSortOrder($sFieldName, $sConnectedId, $iPosition);
                $this->RefreshLock();
            }
        }
    }

    /**
     * inserts or refreshes the lock for the current record.
     *
     * @return TCMSstdClass
     */
    public function RefreshLock()
    {
        return $this->oTableEditor->RefreshLock();
    }

    /**
     * removes the edit lock for the current record and user.
     */
    public function RemoveLock()
    {
        $this->oTableEditor->RemoveLock();
    }

    /**
     * checks if record is currently locked by other editor.
     *
     * @return TdbCmsLock - returns mixed - false if no lock was found
     *                    and lock record if found
     */
    public function IsRecordLocked()
    {
        return $this->oTableEditor->IsRecordLocked();
    }

    /**
     * returns true if record is locked by another user, a transaction or edit rights are missing.
     *
     * @return bool
     */
    public function IsRecordInReadOnlyMode()
    {
        return $this->oTableEditor->IsRecordInReadOnlyMode();
    }

    /**
     * called for every mlt table related to the main table when the main table is
     * deleted. $tableName holds the name of the mlt table.
     *
     * @param string $tableName
     */
    public function _PostDeleteRelationTableEntry($tableName)
    {
        $this->oTableEditor->_PostDeleteRelationTableEntry($tableName);
    }

    /**
     * if method is missing in factory class, call method in oTableEditor object.
     */
    public function __call($name, $args)
    {
        if (method_exists($this->oTableEditor, $name)) {
            return call_user_func_array([$this->oTableEditor, $name], $args);
        }
    }

    /**
     * if property is missing in factory class, get property from oTableEditor object.
     */
    public function __get($name)
    {
        if (property_exists($this->oTableEditor, $name)) {
            return $this->oTableEditor->$name;
        }
    }

    /**
     * if property is missing in factory class, sets property in oTableEditor object.
     */
    public function __set($name, $value)
    {
        if (property_exists($this->oTableEditor, $name)) {
            $this->oTableEditor->$name = $value;
        }
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

    private function getFlashMessageService(): FlashMessageServiceInterface
    {
        return ServiceLocator::get('chameleon_system_core.flash_messages');
    }
}
