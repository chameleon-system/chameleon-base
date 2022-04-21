<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use ChameleonSystem\CoreBundle\Service\PortalDomainServiceInterface;
use ChameleonSystem\CoreBundle\ServiceLocator;
use ChameleonSystem\ExtranetBundle\Interfaces\ExtranetUserProviderInterface;
use esono\pkgCmsCache\CacheInterface;
use Psr\Log\LoggerInterface;

/**
 * an extension of the TCMSRecord that allows inserting, updating and deleting records
 * through the frontend.
 *
 * if a user is logged in as an extranet user, and the table has the fields: data_extranet_user_id,
 * datecreated, lastmodified then these will be updated automatically.
 *
 * If the record has an owner, then only the owner will be allowed to change the record, unless
 * a change is allowed by all (by calling AllowEditByAll(true)
 *
/**/
class TCMSRecordWritable extends TCMSRecord
{
    /**
     * if set to true then all user checks will be ignored.
     *
     * @var bool
     */
    protected $bAllowEditByAll = false;

    /**
     * the owner of the record... if there is one.
     *
     * @var TDataExtranetUser
     */
    protected $oOwner = null;

    /**
     * set to false if you want to prevent the class from triggering cache changes.
     *
     * @var bool
     */
    protected $bChangesTriggerCacheChange = true;
    /**
     * if this object is the property of another object, then cache changes will also
     * call a cache change on the owning object. often this is not necessary - you can change this value
     * to disable the parent reset.
     *
     * @var bool
     */
    protected $bChangesTriggerCacheChangeOnParentTable = true;

    public function SetChangeTriggerCacheChange($bTriggerCacheChange)
    {
        $this->bChangesTriggerCacheChange = $bTriggerCacheChange;
    }

    public function GetChangeTriggerCacheChange()
    {
        return $this->bChangesTriggerCacheChange;
    }

    public function SetChangeTriggerCacheChangeOnParentTable($bChangesTriggerCacheChangeOnParentTable)
    {
        $this->bChangesTriggerCacheChangeOnParentTable = $bChangesTriggerCacheChangeOnParentTable;
    }

    public function GetChangeTriggerCacheChangeOnParentTable()
    {
        return $this->bChangesTriggerCacheChangeOnParentTable;
    }

    /**
     * if set to true then all user checks will be ignored.
     *
     * @param bool $bAllow
     */
    public function AllowEditByAll($bAllow = true)
    {
        $this->bAllowEditByAll = $bAllow;
    }

    /**
     * returns true if editing is allowed for everyone.
     *
     * @return bool
     */
    public function HasEditByAllPermission()
    {
        return $this->bAllowEditByAll;
    }

    /**
     * Save active data... create new record if no id present.
     *
     * @return string|false - id on success... else false
     */
    public function Save()
    {
        if ($this->AllowEdit()) {
            $query = '';
            $bIsNew = false;
            if (is_null($this->id) || empty($this->id)) {
                $this->id = null;
                $bIsNew = true;
                $query = 'INSERT INTO `'.MySqlLegacySupport::getInstance()->real_escape_string($this->table).'` ';
                // also add create stamp
                if (!array_key_exists('datecreated', $this->sqlData) || empty($this->sqlData['datecreated'])) {
                    $this->sqlData['datecreated'] = date('Y-m-d H:i:s');
                }
            } else {
                $query = 'UPDATE `'.MySqlLegacySupport::getInstance()->real_escape_string($this->table).'` ';
            }

            $this->PreSaveHook($bIsNew);

            reset($this->sqlData);
            $oTableConf = &$this->GetTableConf();
            $oFields = &$oTableConf->GetFields($this, true, true);
            $this->sqlData['lastmodified'] = date('Y-m-d H:i:s');

            $aQueryFieldStrings = array();
            /** @var $oField TCMSField */
            while ($oField = $oFields->Next()) {
                if (true === $oField->oDefinition->isVirtualField()) {
                    continue;
                }
                if ('id' != $oField->oDefinition->sqlData['name'] && !$oField->oDefinition->IsCheckboxList()) { // do not parse check box fields!
                    $sVal = '';
                    if (array_key_exists($oField->oDefinition->sqlData['name'], $this->sqlData)) {
                        $sVal = $this->sqlData[$oField->oDefinition->sqlData['name']];
                        $sVal = $oField->ConvertDataToFieldBasedData($sVal);
                    } else {
                        $sVal = $oField->GetSQL();
                    }
                    if (!is_array($sVal) && false !== $sVal) {
                        $aQueryFieldStrings[] = '`'.MySqlLegacySupport::getInstance()->real_escape_string($oField->oDefinition->sqlData['name'])."` = '".MySqlLegacySupport::getInstance()->real_escape_string($sVal)."'";
                    }
                }
            }
            // special case "cmsident" - the field is not in the oFields list, but should be added if it was passed
            if (array_key_exists('cmsident', $this->sqlData) && !empty($this->sqlData['cmsident'])) {
                $aQueryFieldStrings[] = "`cmsident` = '".MySqlLegacySupport::getInstance()->real_escape_string($this->sqlData['cmsident'])."'";
            }
            $query .= ' SET '.implode(', ', $aQueryFieldStrings);
            if (!is_null($this->id)) {
                $query .= " WHERE `id` = '".MySqlLegacySupport::getInstance()->real_escape_string($this->id)."' ";
            }

            $smysqlerror = '';
            if ($bIsNew) {
                // need to create an id.. try to insert untill we have a free id. We will try at most 3 times
                $iMaxTry = 3;
                $bWasInserted = false;
                do {
                    $uid = TTools::GetUUID();
                    $sInsertQuery = $query.", `id`='".MySqlLegacySupport::getInstance()->real_escape_string($uid)."'";
                    MySqlLegacySupport::getInstance()->query($sInsertQuery);
                    $smysqlerror = MySqlLegacySupport::getInstance()->error();
                    if (!empty($smysqlerror)) {
                        $errNr = MySqlLegacySupport::getInstance()->errno();
                        if (1062 != $errNr && 23000 != $errNr) {
                            TTools::WriteLogEntry('unable to save record - error:  ['.$smysqlerror.'] using query ['.$sInsertQuery.'] - object ['.$this->id.']: '.print_r($this->sqlData, true), 1, __FILE__, __LINE__);
                            $iMaxTry = 0;
                        } else {
                            $smysqlerror = '';
                        }
                    } else {
                        if (!array_key_exists('cmsident', $this->sqlData) || empty($this->sqlData['cmsident'])) {
                            $this->sqlData['cmsident'] = MySqlLegacySupport::getInstance()->insert_id();
                        }
                        $bWasInserted = true;
                        $this->id = $uid;
                    }
                    --$iMaxTry;
                } while ($iMaxTry > 0 && !$bWasInserted);
            } else {
                MySqlLegacySupport::getInstance()->query($query);
                $smysqlerror = MySqlLegacySupport::getInstance()->error();
            }

            if (!empty($smysqlerror)) {
                TTools::WriteLogEntry('unable to save record - error:  ['.$smysqlerror.'] using query ['.$query.'] - object ['.$this->id.']: '.print_r($this->sqlData, true), 1, __FILE__, __LINE__);

                return false;
            }
            if (is_null($this->id)) {
                $this->id = MySqlLegacySupport::getInstance()->insert_id();
                $this->sqlData['id'] = $this->id;
            } else {
                $this->sqlData['id'] = $this->id;
            }
            if ($bIsNew) {
                $this->PostInsertHook();
            } else {
                $this->PostSaveHook();
            }
        } else {
            $this->OnInvalidAction();
        }
        // update cache
        $this->UpdateCacheTrigger($this->id);

        return $this->id;
    }

    /**
     * save one or more fields. uses sql, does no conversion. the data passed via assoc array in aFields
     * will be loaded OVER the existing data (fields not passed are untouched)
     * all the post save/post insert hooks will be called. note: you may not pass cmsident or id.
     *
     * @param array $aFields
     *
     * @return string|false - the id of the saved record or false on error
     */
    public function SaveFieldsFast($aFields)
    {
        if ($this->AllowEdit()) {
            if (array_key_exists('cmsident', $aFields)) {
                unset($aFields['cmsident']);
            }
            if (array_key_exists('id', $aFields)) {
                unset($aFields['id']);
            }
            $query = '';
            $bIsNew = false;
            if (is_null($this->id) || empty($this->id)) {
                $this->id = null;
                $bIsNew = true;
                $query = 'INSERT INTO `'.MySqlLegacySupport::getInstance()->real_escape_string($this->table).'` ';
                // also add create stamp
                if (!array_key_exists('datecreated', $this->sqlData) || empty($this->sqlData['datecreated'])) {
                    $this->sqlData['datecreated'] = date('Y-m-d H:i:s');
                }
            } else {
                $query = 'UPDATE `'.MySqlLegacySupport::getInstance()->real_escape_string($this->table).'` ';
            }

            $this->PreSaveHook($bIsNew);

            $bIsFirst = true;
            /** @var $oField TCMSField */
            foreach ($aFields as $sFieldName => $sVal) {
                if ($bIsFirst) {
                    $query .= ' SET ';
                    $bIsFirst = false;
                } else {
                    $query .= ', ';
                }
                $query .= '`'.MySqlLegacySupport::getInstance()->real_escape_string($sFieldName)."` = '".MySqlLegacySupport::getInstance()->real_escape_string($sVal)."'";
                $this->sqlData[$sFieldName] = $sVal;
            }
            if (!is_null($this->id)) {
                $query .= " WHERE `id` = '".MySqlLegacySupport::getInstance()->real_escape_string($this->id)."' ";
            }
            // load data so the post load hook is called
            $this->LoadFromRow($this->sqlData);

            $smysqlerror = '';
            if ($bIsNew) {
                // need to create an id.. try to insert until we have a free id. We will try at most 3 times
                $iMaxTry = 3;
                $bWasInserted = false;
                do {
                    $uid = TTools::GetUUID();
                    $sInsertQuery = $query.", `id`='".MySqlLegacySupport::getInstance()->real_escape_string($uid)."'";
                    MySqlLegacySupport::getInstance()->query($sInsertQuery);
                    $smysqlerror = MySqlLegacySupport::getInstance()->error();
                    if (!empty($smysqlerror)) {
                        $errNr = MySqlLegacySupport::getInstance()->errno();
                        if (1062 != $errNr) {
                            TTools::WriteLogEntry('unable to save record - error:  ['.$smysqlerror.'] using query ['.$sInsertQuery.'] - object ['.$this->id.']: '.print_r($this->sqlData, true), 1, __FILE__, __LINE__);
                            $iMaxTry = 0;
                        } else {
                            $smysqlerror = '';
                        }
                    } else {
                        $bWasInserted = true;
                        $this->id = $uid;
                    }
                    --$iMaxTry;
                } while ($iMaxTry > 0 && !$bWasInserted);
            } else {
                MySqlLegacySupport::getInstance()->query($query);
                $smysqlerror = MySqlLegacySupport::getInstance()->error();
            }

            if (!empty($smysqlerror)) {
                TTools::WriteLogEntry('unable to save record - error:  ['.$smysqlerror.'] using query ['.$query.'] - object ['.$this->id.']: '.print_r($this->sqlData, true), 1, __FILE__, __LINE__);

                return false;
            }
            if (is_null($this->id)) {
                $this->id = MySqlLegacySupport::getInstance()->insert_id();
                $this->sqlData['id'] = $this->id;
            } else {
                $this->sqlData['id'] = $this->id;
            }
            if ($bIsNew) {
                $this->PostInsertHook();
            } else {
                $this->PostSaveHook();
            }
        } else {
            $this->OnInvalidAction();
        }
        // update cache
        $this->UpdateCacheTrigger($this->id);

        return $this->id;
    }

    protected function PostInsertHook()
    {
    }

    /**
     * is called before the item is saved. $this->sqlData will hold the new data
     * while the original is still in the database.
     *
     * @param bool $bIsInsert - set to true if this is an insert
     */
    protected function PreSaveHook($bIsInsert)
    {
    }

    protected function PostSaveHook()
    {
    }

    protected function PostDeleteHook()
    {
    }

    protected function PreDeleteHook()
    {
    }

    public function Delete()
    {
        $aOldData = $this->sqlData;
        if ($this->AllowDelete()) {
            $this->PreDeleteHook();
            $oTableConf = &$this->GetTableConf();
            /** @var $oEditor TCMSTableEditorManager */
            $oEditor = new TCMSTableEditorManager();
            $oEditor->Init($oTableConf->id, null);
            $oEditor->AllowDeleteByAll(true);

            $bCurrentCacheSetting = $this->getCache()->isActive();
            if ($bCurrentCacheSetting) {
                $sClassName = TCMSTableToClass::GetClassName(TCMSTableToClass::PREFIX_CLASS, $this->table);
                if (class_exists($sClassName) && method_exists($sClassName, 'isFrontendAutoCacheClearEnabled')) {
                    if (false === $sClassName::isFrontendAutoCacheClearEnabled()) {
                        $this->getCache()->disable();
                    }
                }
            }
            $oEditor->Delete($this->id);
            if (true === $bCurrentCacheSetting) {
                $this->getCache()->enable();
            }
            $oEditor->AllowDeleteByAll(false);
            $this->id = null;
            $this->sqlData = false;
            $this->PostDeleteHook();
        } else {
            $this->OnInvalidAction();
        }

        $this->UpdateCacheTrigger($this->id, $aOldData);
    }

    protected function OnInvalidAction()
    {
        $user = self::getExtranetUserProvider()->getActiveUser();

        $logger = $this->getSecurityLogger();
        $logger->warning(
            sprintf(
                'Trying to write a record %s in %s that does not belong to the current user %s. AUTO-LOGOUT and REDIRECT executed.',
                $this->id,
                $this->table,
                $user->id ?? ''
            )
        );

        $user->Logout();

        $portal = $this->getPortalDomainService()->getActivePortal();
        if (null !== $portal) {
            $this->getRedirect()->redirect(self::getPageService()->getLinkToPortalHomePageAbsolute(array(), $portal));
        }
    }

    public function LoadFromRow($aRow, $bConvertFieldsToSQL = false)
    {
        if ($bConvertFieldsToSQL) {
            $aRow = $this->ConvertToSQLFormat($aRow);
        }
        parent::LoadFromRow($aRow);

        $oGlobal = TGlobal::instance();
        if (!$oGlobal->IsCMSMode()) {
            // if we have an id field, then we load the owner info
            if (!is_null($this->id) && !empty($this->id)) {
                $query = 'SELECT * FROM `'.MySqlLegacySupport::getInstance()->real_escape_string($this->table)."`
                     WHERE `id` = '".MySqlLegacySupport::getInstance()->real_escape_string($this->id)."'";
                if ($tmp = MySqlLegacySupport::getInstance()->fetch_assoc(MySqlLegacySupport::getInstance()->query($query))) {
                    if (array_key_exists('data_extranet_user_id', $tmp)) {
                        if (!empty($tmp['data_extranet_user_id']) && !empty($tmp['data_extranet_user_id'])) {
                            $this->sqlData['data_extranet_user_id'] = $tmp['data_extranet_user_id'];
                        }
                    }
                }
            }
        }
    }

    public function ConvertToSQLFormat($aData = null)
    {
        if (is_null($aData)) {
            $aData = &$this->sqlData;
        }
        // convert to sql
        $oTableConf = &$this->GetTableConf();
        $oFields = &$oTableConf->GetFieldDefinitions();
        /** @var $oField TCMSFieldDefinition */
        while ($oField = $oFields->Next()) {
            if (true === $oField->isVirtualField()) {
                continue;
            }
            if ('id' != $oField->sqlData['name'] && array_key_exists($oField->sqlData['name'], $aData)) {
                $aData[$oField->sqlData['name']] = $this->ConvertFieldToSQL($oField, $aData[$oField->sqlData['name']]);
            }
        }

        return $aData;
    }

    /**
     * Convert fields (like Date, etc...).
     *
     * @param TCMSFieldDefinition $oField
     * @param string              $sVal
     *
     * @return string
     */
    protected function ConvertFieldToSQL(&$oField, $sVal)
    {
        $oFieldType = $oField->GetFieldType();
        $oLocal = &TCMSLocal::GetActive();

        switch ($oFieldType->sqlData['mysql_type']) {
            case 'DATE':
            case 'DATETIME':
                $sVal = $oLocal->StringToDate($sVal);
                break;
            case 'INT':
            case 'DECIMAL':
                $sVal = $oLocal->StringToNumber($sVal);
                break;
            default:
                break;
        }

        return $sVal;
    }

    /**
     * return true if the current extranet user owns the record
     * if the record has no owner, then the owner will be set.
     *
     * @return bool
     */
    public function IsOwner()
    {
        // need an extranet id to be the owner
        $bIsOwner = false;
        $user = self::getExtranetUserProvider()->getActiveUser();
        if ($user->IsLoggedIn()) {
            $iCurrentUserId = ''; // user id of the table

            $sUserTableName = $user->table;
            $iLoggedInUserId = $user->id;

            if ($this->table != $sUserTableName && array_key_exists('data_extranet_user_id', $this->sqlData)) {
                $iCurrentUserId = $this->sqlData['data_extranet_user_id'];
            } elseif ($this->table == $sUserTableName) {
                $iCurrentUserId = $this->id;
            }

            if (empty($iCurrentUserId)) {
                if ($this->table != $sUserTableName) {
                    $this->sqlData['data_extranet_user_id'] = $iLoggedInUserId;
                }
            }
            $bIsOwner = ($iCurrentUserId == $iLoggedInUserId);
        }

        return $bIsOwner;
    }

    /**
     * return the owner of the record.. if there is one
     * else return null.
     *
     * @return TdbDataExtranetUser
     */
    public function &GetOwner()
    {
        if (is_null($this->oOwner)) {
            if (array_key_exists('data_extranet_user_id', $this->sqlData)) {
                $this->oOwner = TdbDataExtranetUser::GetNewInstance();
                $this->oOwner->Load($this->sqlData['data_extranet_user_id']);
            }
        }

        return $this->oOwner;
    }

    /**
     * return true if the current user has permission to edit the record.
     *
     * @return bool
     */
    public function AllowEdit()
    {
        if ($this->bAllowEditByAll) {
            return true;
        }
        $bAllowEdit = $this->IsOwner();
        if (!$bAllowEdit) {
            $oUser = TdbDataExtranetUser::GetInstance();
            if (null !== $oUser && $oUser->IsLoggedIn()) {
                $bIsAdmin = (array_key_exists('isadmin', $oUser->sqlData) && '1' == $oUser->sqlData['isadmin']);
                if ($bIsAdmin) {
                    $bAllowEdit = true;
                }
            }
        }
        if (false === $bAllowEdit) {
            // if the record does NOT have a data_extranet_user_id then we allow edit - since the record belongs to no user at all
            if (false === TTools::FieldExists($this->table, 'data_extranet_user_id', false)) {
                $bAllowEdit = true;
            }
        }

        return $bAllowEdit;
    }

    /**
     * return true if the current extranet user has permission to delete the record.
     *
     * @return bool
     */
    public function AllowDelete()
    {
        if ($this->bAllowEditByAll) {
            return true;
        }
        $bAllowDelete = $this->IsOwner();
        if (!$bAllowDelete) {
            $oUser = TdbDataExtranetUser::GetInstance();
            $bIsAdmin = (array_key_exists('isadmin', $oUser->sqlData) && '1' == $oUser->sqlData['isadmin']);
            if ($bIsAdmin) {
                $bAllowDelete = true;
            }
        }

        return $bAllowDelete;
    }

    /**
     * update mlt with new values.
     *
     * @param string $sFieldName       - the mlt field name
     * @param array  $aTargetKeyIdList - new target ids
     */
    public function UpdateMLT($sFieldName, $aTargetKeyIdList, $bNoDelete = false)
    {
        if ($this->AllowEdit()) {
            $sMLTTable = MySqlLegacySupport::getInstance()->real_escape_string($this->table.'_'.$sFieldName);
            if (!$bNoDelete) {
                $query = "DELETE FROM `{$sMLTTable}` WHERE `source_id`= '".MySqlLegacySupport::getInstance()->real_escape_string($this->id)."'";
                MySqlLegacySupport::getInstance()->query($query);
            }
            $query = "INSERT INTO `{$sMLTTable}` (`source_id`, `target_id`) VALUES ";

            $iEscapedId = MySqlLegacySupport::getInstance()->real_escape_string($this->id);
            $aQItems = array();
            foreach ($aTargetKeyIdList as $iTargetId) {
                $aQItems[] = "('{$iEscapedId}','".MySqlLegacySupport::getInstance()->real_escape_string($iTargetId)."')";
                if ($bNoDelete) {
                    $DeleteQuery = "DELETE FROM `{$sMLTTable}` WHERE `source_id`= '".MySqlLegacySupport::getInstance()->real_escape_string($this->id)."' AND `target_id` = '".MySqlLegacySupport::getInstance()->real_escape_string($iTargetId)."'";
                    MySqlLegacySupport::getInstance()->query($DeleteQuery);
                }
            }
            $query .= implode(', ', $aQItems);
            MySqlLegacySupport::getInstance()->query($query);

            $this->getCache()->callTrigger($this->table.'_'.$sFieldName);
            $this->UpdateCacheTrigger($this->id);
        }
    }

    /**
     * deletes one or all images of an image field (all if iPos == null).
     *
     * @param string $sFieldName
     * @param int    $iPos
     */
    public function DeleteImages($sFieldName, $iPos = null)
    {
        if ('' === (string) $sFieldName) {
            return;
        }

        if (false === isset($this->sqlData[$sFieldName])) {
            return;
        }

        if ('' === $this->sqlData[$sFieldName]) {
            return;
        }

        if (false === $this->AllowEdit()) {
            return;
        }

        $images = explode(',', $this->sqlData[$sFieldName]);
        if (false === is_array($images)) {
            $images = array($this->sqlData[$sFieldName]);
        }

        $fieldTableConf = $this->GetTableConf();
        $fieldConf = $fieldTableConf->GetFieldDefinition($sFieldName);
        $defaultImageIds = explode(',', $fieldConf->sqlData['field_default_value']);
        if (!is_array($defaultImageIds)) {
            $defaultImageIds = array($defaultImageIds);
        }

        $mediaTableConf = TdbCmsTblConf::GetNewInstance();
        $mediaTableConf->LoadFromField('name', 'cms_media');

        foreach ($images as $imagePosition => $imageId) {
            if (null !== $iPos && $imagePosition !== $iPos) {
                continue;
            }

            if ('' === $imageId) {
                continue;
            }

            // ignore template IDs
            if (true === is_numeric($imageId) && (int) $imageId < 1000) {
                continue;
            }

            $tableEditor = new TCMSTableEditorManager();
            $tableEditor->Init($mediaTableConf->id, $imageId);
            $tableEditor->AllowDeleteByAll(true);
            $tableEditor->Delete($imageId);
            $tableEditor->AllowDeleteByAll(false);

            if (array_key_exists($imagePosition, $defaultImageIds)) {
                $images[$imagePosition] = $defaultImageIds[$imagePosition];
            } else {
                $images[$imagePosition] = 1;
            }
        }
        $recordData = $this->sqlData;
        $recordData[$sFieldName] = implode(',', $images);
        $this->LoadFromRow($recordData);
        $this->Save();
    }

    /**
     * saves an image in the image database. NOTICE: if an image exists an that location,
     * it will NOT be deleted! If you want to remove it from the database, call DeleteImages first.
     *
     *   [Filedata] => Array
     *     (
     *         [name] => 300_movie_article.jpg
     *         [type] => application/octet-stream
     *         [tmp_name] => /tmp/php7Sv9yk
     *         [error] => 0
     *         [size] => 77105
     *     )
     *
     *
     * @param array  $aFileData     - the $_FILE data
     * @param string $sFileName     - the file name for the image
     * @param string $sFieldName    - the field to update
     * @param int    $iPos          - the position in the field
     * @param bool   $isNotUpload   - set to true if you want to save images without uploading them (ie when unpacking a zip)
     * @param bool   $bKeepOriginal - set to true if you want to keep the original file
     *
     * @return bool
     */
    public function UpdateImage($aFileData, $sFileName, $sFieldName, $iMediaCategoryId, $iPos = 0, $isNotUpload = false, $bKeepOriginal = false)
    {
        $bUpdated = false;
        if ($this->AllowEdit()) {
            $aImagePlacing = explode(',', $this->sqlData[$sFieldName]);
            if (!is_array($aImagePlacing)) {
                $aImagePlacing = array($aImagePlacing);
            }
            if (!array_key_exists($iPos, $aImagePlacing)) {
                $aImagePlacing[$iPos] = 0;
            }

            // now upload the file
            $oMediaTableConf = new TCMSTableConf();
            $oMediaTableConf->LoadFromField('name', 'cms_media');

            $oMediaManagerEditor = new TCMSTableEditorMedia();
            $oMediaManagerEditor->Init($oMediaTableConf->id);
            $oMediaManagerEditor->SetUploadData($aFileData, $isNotUpload);

            $aData = array('name' => $sFileName, 'description' => $sFileName, 'metatags' => $sFileName, 'cms_media_tree_id' => $iMediaCategoryId);
            // make a copy of the file...

            $filemanager = $this->getFileManager();

            $sUid = md5(uniqid(rand(), true));
            if ($bKeepOriginal) {
                $filemanager->copy($aFileData['tmp_name'], $aFileData['tmp_name'].$sUid);
            }
            $oMediaManagerEditor->Save($aData);
            if ($bKeepOriginal) {
                $filemanager->move($aFileData['tmp_name'].$sUid, $aFileData['tmp_name']);
            }
            $aImagePlacing[$iPos] = $oMediaManagerEditor->sId;

            $aTmpData = $this->sqlData;
            $aTmpData[$sFieldName] = implode(',', $aImagePlacing);
            $this->LoadFromRow($aTmpData);
            $this->Save();
            $bUpdated = true;
        }

        return $bUpdated;
    }

    /**
     * upload a file and connect via mlt.
     *
     * $fileData needs to be in the form of $_FILES
     * [aFiledata] => Array
     *     (
     *         [name] => 300_movie_article.jpg
     *         [type] => application/octet-stream
     *         [tmp_name] => /tmp/php7Sv9yk
     *         [error] => 0
     *         [size] => 77105
     *     )
     *
     * @param array  $aFileData            - file data
     * @param string $sFileName            - file name to use
     * @param string $sFieldName           - field name to which we want to save the data
     * @param int    $iDocumentCategoryId  - category into which we upload
     * @param bool   $bIsPrivate           - private or public file
     * @param string $sDescription         - optional description
     * @param bool   $bFileIsNotFromUpload - set this to true if the file was not stored via default post file upload (e.g. local FTP file, or from URL)
     * @param string $sDocumentID          - if a cms_document ID is set the document will be replaced/updated instead of inserted
     *
     * @return bool
     */
    public function UploadCMSDocument($aFileData, $sFileName, $sFieldName, $iDocumentCategoryId, $bIsPrivate = true, $sDescription = null, $bFileIsNotFromUpload = false, $sDocumentID = null)
    {
        $bUploadOK = false;
        if (is_array($aFileData) && $this->AllowEdit() && !is_null($this->id)) {
            if (0 === $aFileData['error']) {
                $oDocumentManagerConf = new TCMSTableConf();
                $oDocumentManagerConf->LoadFromField('name', 'cms_document');

                $oDocumentManagerEditor = new TCMSTableEditorDocument();
                $oDocumentManagerEditor->Init($oDocumentManagerConf->id, $sDocumentID);
                $oDocumentManagerEditor->SetUploadData($aFileData, $bFileIsNotFromUpload);

                $sPrivate = '1';
                if (!$bIsPrivate) {
                    $sPrivate = '0';
                }

                if (is_null($sDescription)) {
                    $sDescription = $sFileName;
                }
                $aData = array('id' => $sDocumentID, 'name' => $sFileName, 'description' => $sDescription, 'cms_document_tree_id' => $iDocumentCategoryId, 'private' => $sPrivate);
                $oDocumentManagerEditor->Save($aData);
                if (!is_null($oDocumentManagerEditor->sId)) {
                    $bUploadOK = true;

                    // check field type. if this is not a download type, we assume it to be a lookup type and update the field value directly
                    $oTableConf = &$this->GetTableConf();
                    $oFieldConf = $oTableConf->GetFieldDefinition($sFieldName);
                    $oFieldType = $oFieldConf->GetFieldType();
                    if ('CMSFIELD_DOCUMENTS' == $oFieldType->sqlData['constname']) {
                        $sMLT = MySqlLegacySupport::getInstance()->real_escape_string($this->table.'_'.$sFieldName.'_cms_document_mlt');
                        // check if document is connected already
                        $query = "SELECT * FROM `{$sMLT}` WHERE
              `source_id` = '".MySqlLegacySupport::getInstance()->real_escape_string($this->id)."'
              AND `target_id` = '".MySqlLegacySupport::getInstance()->real_escape_string($oDocumentManagerEditor->sId)."'";
                        $result = MySqlLegacySupport::getInstance()->query($query);

                        if (0 == MySqlLegacySupport::getInstance()->num_rows($result)) {
                            // now connect to record
                            $query = "INSERT INTO `{$sMLT}`
                                  SET `source_id` = '".MySqlLegacySupport::getInstance()->real_escape_string($this->id)."',
                                      `target_id` = '".MySqlLegacySupport::getInstance()->real_escape_string($oDocumentManagerEditor->sId)."'
                         ";
                            MySqlLegacySupport::getInstance()->query($query);
                        }
                    } else {
                        $aData = $this->sqlData;
                        $aData[$sFieldName] = $oDocumentManagerEditor->sId;
                        $this->LoadFromRow($aData);
                        $this->Save();
                    }
                }
            }
        }

        return $bUploadOK;
    }

    /**
     * import one file from URL to document manager and connect via mlt.
     *
     * @param string      $sFileURL
     * @param string      $sFileName           - file name to use
     * @param string      $sFieldName          - field name to which we want to save the data
     * @param int         $iDocumentCategoryId - category into which we upload
     * @param bool        $bIsPrivate          - private or public file
     * @param string      $sDescription        - optional description
     * @param string|null $sDocumentID         - if a cms_document ID is set the document will be replaced/updated instead of inserted
     *
     * @internal param array $aFileData - file data
     *
     * @return bool
     */
    public function UploadCMSDocumentFromURL($sFileURL = '', $sFileName, $sFieldName, $iDocumentCategoryId, $bIsPrivate = true, $sDescription = null, $sDocumentID = null)
    {
        $bUploadOK = false;
        if (!empty($sFileURL) && $this->AllowEdit() && !is_null($this->id) && TTools::isOnline($sFileURL)) {
            $sFileNameFromURL = '';
            // get filename from url
            if ($aURLParts = parse_url($sFileURL)) {
                if (array_key_exists('path', $aURLParts) && !empty($aURLParts['path'])) {
                    $aPathParts = explode('/', $aURLParts['path']);
                    $sFileNameFromURL = $aPathParts[count($aPathParts) - 1];
                }
            }

            $sFileContent = file_get_contents($sFileURL);
            if (!empty($sFileContent)) {
                $sTempDir = CMS_TMP_DIR;
                $sTmpFileName = tempnam($sTempDir, 'chameleonFileImport_');
                $fileManager = $this->getFileManager();

                if ($handle = $fileManager->fopen($sTmpFileName, 'wb')) {
                    $bFileImportSuccess = false;
                    if ($fileManager->fwrite($handle, $sFileContent)) {
                        $fileManager->fclose($handle);
                        $bFileImportSuccess = true;
                    }

                    if ($bFileImportSuccess) {
                        $aFileData = array();
                        $aFileData['name'] = $sFileNameFromURL;
                        $aFileData['type'] = 'application/octet-stream';
                        $aFileData['tmp_name'] = $sTmpFileName;
                        $aFileData['error'] = 0;
                        $aFileData['size'] = filesize($sTmpFileName);

                        $bUploadOK = $this->UploadCMSDocument($aFileData, $sFileName, $sFieldName, $iDocumentCategoryId, $bIsPrivate, $sDescription, true, $sDocumentID);
                    }
                }
            }
        }

        return $bUploadOK;
    }

    protected function UpdateCacheTrigger($sRecordId, $aOldData = array())
    {
        $sClassName = TCMSTableToClass::GetClassName(TCMSTableToClass::PREFIX_CLASS, $this->table);
        if (class_exists($sClassName) && method_exists($sClassName, 'isFrontendAutoCacheClearEnabled')) {
            if (false === $sClassName::isFrontendAutoCacheClearEnabled()) {
                return false;
            }
        }

        // clear cache of record
        if (false === $this->GetChangeTriggerCacheChange()) {
            return false;
        }

        $cache = $this->getCache();

        $cache->callTrigger($this->table, $sRecordId);

        if (false === $this->GetChangeTriggerCacheChangeOnParentTable()) {
            return false;
        }

        // if the records has parent fields, we need to trigger a cache clear on all the owning records
        $oTableConf = &$this->GetTableConf();
        $oParentFields = $oTableConf->GetFieldDefinitions(array('CMSFIELD_PROPERTY_PARENT_ID'));
        while ($oParentField = $oParentFields->Next()) {
            $oParentFieldObject = $oTableConf->GetField($oParentField->sqlData['name'], $this);
            $sConnectedTable = $oParentFieldObject->GetConnectedTableName();
            if (array_key_exists($oParentField->sqlData['name'], $aOldData)) {
                if (!empty($aOldData[$oParentField->sqlData['name']])) {
                    $cache->callTrigger($sConnectedTable, $aOldData[$oParentField->sqlData['name']]);
                }
            }
            if (!empty($this->sqlData[$oParentField->sqlData['name']])) {
                $cache->callTrigger($sConnectedTable, $this->sqlData[$oParentField->sqlData['name']]);
            }
        }
    }

    /**
     * Method is static because otherwise the compiler wouldn't allow static methods with the same name in subclassese.
     *
     * @return ExtranetUserProviderInterface
     */
    private static function getExtranetUserProvider()
    {
        return ServiceLocator::get('chameleon_system_extranet.extranet_user_provider');
    }

    /**
     * @return IPkgCmsFileManager
     */
    private function getFileManager()
    {
        return ServiceLocator::get('chameleon_system_core.filemanager');
    }

    /**
     * @return PortalDomainServiceInterface
     */
    private function getPortalDomainService()
    {
        return ServiceLocator::get('chameleon_system_core.portal_domain_service');
    }

    /**
     * @return ICmsCoreRedirect
     */
    private function getRedirect()
    {
        return ServiceLocator::get('chameleon_system_core.redirect');
    }

    private function getSecurityLogger(): LoggerInterface
    {
        return ServiceLocator::get('monolog.logger.security');
    }

    private function getCache(): CacheInterface
    {
        return ServiceLocator::get('chameleon_system_core.cache');
    }
}
