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
use ChameleonSystem\CoreBundle\Util\InputFilterUtilInterface;
use ChameleonSystem\SecurityBundle\Service\SecurityHelperAccess;
use ChameleonSystem\SecurityBundle\Voter\CmsPermissionAttributeConstants;
use ChameleonSystem\SecurityBundle\Voter\CmsUserRoleConstants;
use Symfony\Component\Filesystem\Exception\IOExceptionInterface;

class TCMSTableEditorDocumentEndPoint extends TCMSTableEditorFiles
{
    /**
     * @return string
     */
    protected function GetTableName()
    {
        return 'cms_document';
    }

    /**
     * set public methods here that may be called from outside.
     */
    public function DefineInterface()
    {
        parent::DefineInterface();
        $this->methodCallAllowed[] = 'FetchConnections';
        $this->methodCallAllowed[] = 'downloadDocument';
    }

    /**
     * get the download file if right are correct.
     *
     * @return bool
     */
    public function downloadDocument()
    {
        if (false === TGlobal::IsCMSMode()) {
            return false;
        }
        /** @var SecurityHelperAccess $securityHelper */
        $securityHelper = ServiceLocator::get(SecurityHelperAccess::class);
        if (false === $securityHelper->isGranted(CmsUserRoleConstants::CMS_USER)) {
            return false;
        }

        /*
         * user can download document if:
         * - he is the owner
         * - OR
         * - he has one of the other permissions.
         */
        if (true === $securityHelper->isGranted(CmsPermissionAttributeConstants::TABLE_EDITOR_ACCESS, $this->oTable)) {
            $bAsDownload = ('1' == $this->getInputFilterUtil()->getFilteredInput('asDownload')) ? true : false;
            $this->oTable->downloadDocument($bAsDownload);

            return true;
        }

        return false;
    }

    /**
     * {@inheritdoc}
     */
    protected function DataIsValid($postData, $oFields = null)
    {
        $isValid = parent::DataIsValid($postData, $oFields);

        /*
        sent data:
        [Filedata] => Array
          (
              [name] => 300_movie_article.jpg
              [type] => application/octet-stream
              [tmp_name] => /tmp/php7Sv9yk
              [error] => 0
              [size] => 77105
          )
        */

        if ($isValid) {
            // Array of valid extensions
            $allowedFileTypes = TTools::GetCMSFileTypes();
            $isValid = $this->IsValidFileExtension($allowedFileTypes);
        }

        return $isValid;
    }

    /**
     * {@inheritdoc}
     */
    public function DatabaseCopy($languageCopy = false, $aOverloadedFields = [], $bCopyAllLanguages = false)
    {
        $iFileId = $this->sId;
        $oDocument = new TCMSDownloadFile();
        /* @var $oDocument TCMSDownloadFile */
        $oDocument->Load($iFileId);
        $sFile = $oDocument->GetRealPath();
        parent::DatabaseCopy($languageCopy, $aOverloadedFields, $bCopyAllLanguages);

        if (file_exists($sFile)) {
            $oDocument = new TCMSDownloadFile();
            $oDocument->Load($this->sId);
            $this->getFileManager()->copy($sFile, $oDocument->GetRealPath());
        }
    }

    /**
     * {@inheritdoc}
     */
    protected function GetTargetDirectory()
    {
        return PATH_CMS_CUSTOMER_DATA;
    }

    /**
     * {@inheritdoc}
     */
    protected function PrepareDataForSave($postData)
    {
        $postData = parent::PrepareDataForSave($postData);

        if (!is_null($this->aUploadData)) {
            $fileExtension = TTools::GetFileExtension($this->aUploadData['name']);
            $fileExtensionLength = strlen($fileExtension);
            $fileNameWithoutExtension = substr($this->aUploadData['name'], 0, -($fileExtensionLength + 1));
            if (!isset($postData['filename']) || $postData['filename'] != $fileNameWithoutExtension) {
                $postData['filename'] = $fileNameWithoutExtension;
            }
        }

        return $postData;
    }

    /**
     * returns the tree fieldname.
     *
     * @return string
     */
    protected function GetTreeFieldName()
    {
        return 'cms_document_tree_id';
    }

    /**
     * {@inheritdoc}
     */
    protected function PostSaveHook($oFields, $oPostTable)
    {
        parent::PostSaveHook($oFields, $oPostTable);

        if (array_key_exists('filesize', $oPostTable->sqlData)) {
            $this->SaveField('filesize', $oPostTable->sqlData['filesize']);
        }

        // save height and filesize after update because they are hidden fields and were not saved yet
        //      $oFields->GoToStart();
        if (!is_null($this->sId)) {
            $oDownloadFile = new TCMSDownloadFile();
            /** @var $oDownloadFile TCMSDownloadFile */
            $documentID = $this->sId;
            $oDownloadFile->Load($documentID);
            if ('0' == $oDownloadFile->sqlData['private']) {
                if (CMS_USE_SEO_HANDLER_FOR_PUBLIC_DOWNLOADS === true) {
                    $oDownloadFile->RemovePublicSymLink();
                } else {
                    $oDownloadFile->CreatePublicSymLink();
                }
            } else {
                $oDownloadFile->RemovePublicSymLink();
            }
            $sSeoName = $oDownloadFile->GetTargetFileName(true);
            $oTableManager = TTools::GetTableEditorManager($oDownloadFile->table, $oDownloadFile->id);
            $oTableManager->AllowEditByAll(true);
            $oTableManager->SaveField('seo_name', $sSeoName);

            $this->getCacheService()->callTrigger('cms_document', $documentID);
        }
    }

    /**
     * saves only one field of a record (like the edit-on-click WYSIWYG).
     *
     * @param string $sFieldName the fieldname to save to
     * @param string $sFieldContent the content to save
     * @param bool $bTriggerPostSaveHook - if set to true, the PostSaveHook method will be called at the end of the call
     *
     * @return TCMSstdClass
     */
    public function SaveField($sFieldName, $sFieldContent, $bTriggerPostSaveHook = false)
    {
        $oDocument = TdbCmsDocument::GetNewInstance($this->sId);
        if ('0' == $oDocument->sqlData['private']) {
            $oDocument->RemovePublicSymLink();
        }

        $sReturnValue = parent::SaveField($sFieldName, $sFieldContent, $bTriggerPostSaveHook);

        $oDocument = TdbCmsDocument::GetNewInstance($this->sId); // reload because the document type may be changed
        if ('0' == $oDocument->sqlData['private']) {
            if (CMS_USE_SEO_HANDLER_FOR_PUBLIC_DOWNLOADS === true) {
                $oDocument->RemovePublicSymLink();
            } else {
                $oDocument->CreatePublicSymLink();
            }
        }

        return $sReturnValue;
    }

    /**
     * remove all references to the current record in all tables (including mlt tables).
     */
    protected function DeleteRecordReferences()
    {
        $this->RemoveDownloadFromWysiwygFields();
        parent::DeleteRecordReferences();
    }

    /**
     * is called only from Delete method and calls all delete relevant methods
     * executes the final SQL Delete Query.
     *
     * @return bool
     */
    protected function DeleteExecute()
    {
        $bDeleteSuccess = false;
        $oDownload = new TCMSDownloadFile();
        /** @var $oDownload TCMSDownloadFile */
        if ($oDownload->Load($this->sId)) {
            if ('0' == $oDownload->sqlData['private']) {
                $oDownload->RemovePublicSymLink();
            }
        }
        if ($this->DeleteFile($this->sId)) {
            $bDeleteSuccess = parent::DeleteExecute();
        }

        return $bDeleteSuccess;
    }

    /**
     * returns an multidimensional array of all fields with matching values in all
     * tables that contain the document given by the document id $fileID.
     *
     * @param string $fileID - an image id (from the table cms_document)
     * @param array $aTableBlackList - if set true, it means
     *
     * @return array
     */
    public function FetchConnections($fileID, $aTableBlackList = null)
    {
        $aDownloadRefFromWysiwygFields = $this->GetDownloadRefFromWysiwygFields($aTableBlackList);
        $aMltConnectedRecordReferences = $this->GetMltConnectedRecordReferences($aTableBlackList);

        return array_merge_recursive($aDownloadRefFromWysiwygFields, $aMltConnectedRecordReferences);
    }

    /**
     * gets download references in all existing wysiwyg fields.
     */
    protected function GetDownloadRefFromWysiwygFields($aTableBlackList = null)
    {
        $aDownloadRefFromWysiwygFields = [];
        $sMaskTableInBlackList = '';
        if (!empty($aTableBlackList)) {
            $databaseConnection = $this->getDatabaseConnection();
            $tableBlacklistString = implode(',', array_map([$databaseConnection, 'quote'], $aTableBlackList));
            $sMaskTableInBlackList = " AND  `cms_tbl_conf`.`name` NOT IN ($tableBlacklistString)";
        }

        $Select = "SELECT `cms_field_conf`.`name` AS fieldname , `cms_tbl_conf`.`name` AS tablename FROM `cms_field_type`
            INNER JOIN `cms_field_conf`  ON `cms_field_conf`.`cms_field_type_id` = `cms_field_type`.`id`
            INNER JOIN `cms_tbl_conf`  ON `cms_tbl_conf`.`id` = `cms_field_conf`.`cms_tbl_conf_id`
                 WHERE `cms_field_type`.`constname` = 'CMSFIELD_WYSIWYG_LIGHT' OR `cms_field_type`.`constname` = 'CMSFIELD_WYSIWYG'".$sMaskTableInBlackList;
        $res = MySqlLegacySupport::getInstance()->query($Select);
        while ($aSelectRow = MySqlLegacySupport::getInstance()->fetch_assoc($res)) {
            if (TCMSRecord::TableExists($aSelectRow['tablename']) && TCMSRecord::FieldExists($aSelectRow['tablename'], $aSelectRow['fieldname'])) {
                $oTableRecordList = $this->QueryRecordsWithWysiwygDownload($aSelectRow['tablename'], $aSelectRow['fieldname']);
                if (is_object($oTableRecordList) && $oTableRecordList->Length() > 0) {
                    $aDownloadRefFromWysiwygFields[$aSelectRow['tablename']][$aSelectRow['fieldname']] = $oTableRecordList;
                }
            }
        }

        return $aDownloadRefFromWysiwygFields;
    }

    /**
     * Remove download references in all existing wysiwyg fields.
     */
    protected function RemoveDownloadFromWysiwygFields()
    {
        $aDownloadRefFromWysiwygFields = $this->GetDownloadRefFromWysiwygFields();
        if (!empty($aDownloadRefFromWysiwygFields) && count($aDownloadRefFromWysiwygFields) > 0) {
            foreach ($aDownloadRefFromWysiwygFields as $tableName => $aFields) {
                foreach ($aFields as $field => $value) {
                    /** @var $oTableRecordList TCMSRecordList */
                    $oTableRecordList = $value;
                    /* @var $oRecord TCMSRecord */
                    while ($oTableRecord = $oTableRecordList->Next()) {
                        $sWysiwygText = $this->RemoveDownloadFromWysiwygText($oTableRecord->sqlData[$field]);
                        if (strlen($oTableRecord->sqlData[$field]) != strlen($sWysiwygText)) {
                            $oTableRecordEditorManager = TTools::GetTableEditorManager($tableName, $oTableRecord->id);
                            $oTableRecordEditorManager->SaveField($field, $sWysiwygText);
                        }
                    }
                }
            }
        }
    }

    /**
     * Gets record references from records connected with mlt.
     *
     * @return array $oTableRecordList
     */
    public function GetMltConnectedRecordReferences($aTableBlackList = null)
    {
        $aMltConnectedRecordReferences = [];
        $sMaskTableInBlackList = '';
        if (!empty($aTableBlackList)) {
            $databaseConnection = $this->getDatabaseConnection();
            $tableBlacklistString = implode(',', array_map([$databaseConnection, 'quote'], $aTableBlackList));
            $sMaskTableInBlackList = " AND  `cms_tbl_conf`.`name` NOT IN ($tableBlacklistString)";
        }
        $Select = "SELECT `cms_field_conf`.`name` AS fieldname , `cms_tbl_conf`.`name` AS tablename FROM `cms_field_type`
            INNER JOIN `cms_field_conf`  ON `cms_field_conf`.`cms_field_type_id` = `cms_field_type`.`id`
            INNER JOIN `cms_tbl_conf`  ON `cms_tbl_conf`.`id` = `cms_field_conf`.`cms_tbl_conf_id`
                 WHERE `cms_field_type`.`constname` = 'CMSFIELD_DOCUMENTS'".$sMaskTableInBlackList;
        $res = MySqlLegacySupport::getInstance()->query($Select);
        while ($aSelectRow = MySqlLegacySupport::getInstance()->fetch_assoc($res)) {
            $sMltTableName = $aSelectRow['tablename'].'_'.$aSelectRow['fieldname'].'_cms_document_mlt';
            if (TCMSRecord::TableExists($aSelectRow['tablename']) && TCMSRecord::TableExists($sMltTableName)) {
                $oRecordList = $this->QueryMltReferencesRecordList($aSelectRow['tablename'], $sMltTableName);
                if (is_object($oRecordList) && $oRecordList->Length() > 0) {
                    $aMltConnectedRecordReferences[$aSelectRow['tablename']][$aSelectRow['fieldname']] = $oRecordList;
                }
            }
        }

        return $aMltConnectedRecordReferences;
    }

    /**
     * Query record references from mlt table.
     *
     * @param string $sTableName
     * @param string $sMltTableName
     *
     * @return TCMSRecordList $oTableRecordList
     */
    protected function QueryMltReferencesRecordList($sTableName, $sMltTableName)
    {
        $databaseConnection = $this->getDatabaseConnection();
        $quotedTableName = $databaseConnection->quoteIdentifier($sTableName);
        $quotedMltTableName = $databaseConnection->quoteIdentifier($sMltTableName);
        $quotedId = $databaseConnection->quote($this->sId);
        $Select = "SELECT $quotedTableName.*
                   FROM $quotedMltTableName
                   INNER JOIN $quotedTableName ON $quotedTableName.`id` = $quotedMltTableName.`source_id`
                   WHERE $quotedMltTableName.`target_id` = $quotedId";
        $sClassName = TCMSTableToClass::GetClassName(TCMSTableToClass::PREFIX_CLASS, $sTableName).'List';

        return call_user_func([$sClassName, 'GetList'], $Select);
    }

    /**
     * Get the cache key used to id the object in cache.
     *
     * @param string $sTableName
     * @param string $sFieldName
     *
     * @return string
     */
    protected static function GetCacheGetKey($sTableName, $sFieldName)
    {
        return ServiceLocator::get('chameleon_system_core.cache')->getKey([$sTableName => $sFieldName]);
    }

    /**
     * Returns cache trigger for the object.
     *
     * @param string $sTableName
     *
     * @return array{table: string, id: string}
     */
    public function GetCacheTrigger($sTableName)
    {
        $aTrigger = [];
        $aTrigger[] = ['table' => $sTableName, 'id' => ''];

        return $aTrigger;
    }

    /**
     * Delete record references from records connected with mlt.
     */
    public function DeleteMltConnectedRecordReferences()
    {
        $aMltConnectedRecordReferences = $this->GetMltConnectedRecordReferences();
        if (!empty($aMltConnectedRecordReferences) && count($aMltConnectedRecordReferences) > 0) {
            foreach ($aMltConnectedRecordReferences as $tableName => $aFields) {
                foreach ($aFields as $field => $value) {
                    /** @var $oTableRecordList TCMSRecordList */
                    $oTableRecordList = $value;
                    /** @var $oRecord TCMSRecord */
                    while ($oRecord = $oTableRecordList->Next()) {
                        $oTableEditor = TTools::GetTableEditorManager($tableName, $oRecord->id);
                        $oTableEditor->RemoveMLTConnection($field, $this->sId);
                        $sMltTableName = $tableName.'_'.$field.'_cms_document_mlt';
                        $oTableEditor->_PostDeleteRelationTableEntry($sMltTableName);
                    }
                }
            }
        }
    }

    /**
     * Query all records from the given table and field where download items were added.
     *
     * @param string $sTableName
     * @param string $sFieldName
     *
     * @return TCMSRecordList $oTableRecordList
     */
    protected function QueryRecordsWithWysiwygDownload($sTableName, $sFieldName)
    {
        $databaseConnection = $this->getDatabaseConnection();
        $quotedTableName = $databaseConnection->quoteIdentifier($sTableName);
        $quotedFieldName = $databaseConnection->quoteIdentifier($sFieldName);
        $quotedId = $databaseConnection->quote($this->sId);
        $quotedId = trim($quotedId, "'");
        $Select = "SELECT *
                   FROM $quotedTableName
                   WHERE
                    (
                      ($quotedFieldName LIKE '%class=\"cmsdownloaditem%' OR $quotedFieldName LIKE '%class=\"wysiwyg_cmsdownloaditem%')
                      AND ($quotedFieldName LIKE '%cmsdocument=\"$quotedId\"%' OR $quotedFieldName LIKE '%cmsdocument_$quotedId%')
                    )
                   OR $quotedFieldName LIKE '%[\{$quotedId,dl%'";
        $sClassName = TCMSTableToClass::GetClassName(TCMSTableToClass::PREFIX_CLASS, $sTableName).'List';

        return call_user_func([$sClassName, 'GetList'], $Select);
    }

    /**
     * Remove downloaditem from wysiwyg text.
     *
     * @param string $sWysiwygText
     *
     * @return string $sWysiwygText
     */
    protected function RemoveDownloadFromWysiwygText($sWysiwygText)
    {
        // parse downloads for wysiwyg with style <div><span class="cmsdownloaditem"><a title="title" target="_blank" href="...">Title</a></span>[63 kb]</div>
        $matchString = "/<span([^>]+?)cmsdocument=[\"]([^'\"]+?)[\"]([^>]*?)><a([^>]+?)href=['\"]([^'\"]*?)['\"]([^>]*?)>([^<]*?)<\\/a>\\s*<span([^>]*?)>([^<]*?)<\\/span><\\/span>/si";
        $sWysiwygText = preg_replace_callback($matchString, [$this, '_callback_download_link_processor'], $sWysiwygText);
        // parse downloads for wysiwyg downloads with style <span class="wysiwyg_cmsdownloaditem cmsdocument_13">[ico]title[kb]</span>
        $matchStringForNewDownloadLinks = '#<span class="(wysiwyg_cmsdownloaditem)\scmsdocument_(.+?)">(.*\\s*.*\\s*.*)</span>#';
        $sWysiwygText = preg_replace_callback($matchStringForNewDownloadLinks, [$this, '_callback_download_link_processor'], $sWysiwygText);

        return $sWysiwygText;
    }

    /**
     * callback method that replaces CMS download links with frontend HTML.
     *
     * @param array $aMatch
     *
     * @return string
     */
    public function _callback_download_link_processor($aMatch)
    {
        $bNeedRemove = false;
        $sResult = $aMatch[0];
        $DownloadLinkVersion1 = 'cmsdocument_'.$this->sId;
        $DownloadLinkVersion2 = 'cmsdocument="'.$this->sId.'"';
        if ('wysiwyg_cmsdownloaditem' == $aMatch[1]) {
            if (false !== strpos($aMatch[0], $DownloadLinkVersion1)) {
                $bNeedRemove = true;
            }
        } else {
            if (false !== strpos($aMatch[0], $DownloadLinkVersion2)) {
                $bNeedRemove = true;
            }
        }
        if ($bNeedRemove) {
            $sResult = '';
        }

        return $sResult;
    }

    /**
     * Parse downloads for wysiwyg with style <div><span class="cmsdownloaditem"><a title="title" target="_blank" href="...">Title</a></span>[63 kb]</div>.
     *
     * @param array $aMatch
     *
     * @return string
     */
    protected function DownloadParserVersion1($aMatch)
    {
        $sResult = '';
        if (false !== strpos($aMatch[0], 'cmsdocument_'.$this->sId)) {
            $sResult = $aMatch[0];
        }

        return $sResult;
    }

    /**
     * Parse downloads for wysiwyg downloads with style <span class="wysiwyg_cmsdownloaditem cmsdocument_13">[ico]title[kb]</span>
     * If no [ico] or [kb] or no title function returns link without fileicon or file size or title.
     *
     * @param array $aMatch
     *
     * @return string
     */
    protected function DownloadParserVersion2($aMatch)
    {
        $sResult = '';
        $itemId = $aMatch[2];
        $oItem = new TCMSDownloadFile(); /* @var $oItem TCMSDownloadFile */
        if ($oItem->Load($itemId)) {
            $bHideSize = true;
            $bHideName = false;
            $bHideIcon = true;
            $sLinkName = '';
            if (!empty($aMatch[3])) {
                if (preg_match("#^(\[ico\])?(.*\\s*.*\\s*.*)(\[kb\])?$#", $aMatch[3], $aSubMatch)) {
                    $iLen = strlen($aSubMatch[0]);
                    $iStart = strpos($aSubMatch[0], '[ico]');
                    if (false !== strpos($aSubMatch[0], '[ico]')) {
                        $iStart = strpos($aSubMatch[0], '[ico]') + 5;
                        $bHideIcon = false;
                    }
                    if (false !== strpos($aSubMatch[0], '[kb]')) {
                        $iLen = $iLen - 4;
                        $bHideSize = false;
                    }

                    $sLinkName = substr($aSubMatch[0], $iStart, $iLen - $iStart);
                    if ('' == trim($sLinkName)) {
                        $bHideName = true;
                    }
                }
            } else {
                $bHideName = true;
            }
            if ($sLinkName != $oItem->GetName()) {
                $sResult = $oItem->getDownloadHtmlTag(false, $bHideName, $bHideSize, $bHideIcon, $sLinkName);
            } else {
                $sResult = $oItem->getDownloadHtmlTag(false, $bHideName, $bHideSize, $bHideIcon);
            }
        } else {
            $sResult = $aMatch[0];
        }

        return $sResult;
    }

    /**
     * returns an iterator with the menuitems for the current table. if you want to add your own
     * items, overwrite the GetCustomMenuItems (NOT GetMenuItems)
     * the iterator will always be reset to start.
     *
     * @return TIterator
     */
    public function GetMenuItems()
    {
        parent::GetMenuItems();

        $this->oMenuItems->RemoveItem('sItemKey', 'save');

        /** @var SecurityHelperAccess $securityHelper */
        $securityHelper = ServiceLocator::get(SecurityHelperAccess::class);

        $tableInUserGroup = $securityHelper->isGranted(CmsPermissionAttributeConstants::TABLE_EDITOR_ACCESS, $this->oTableConf->sqlData['cms_usergroup_id']);
        if ($tableInUserGroup) {
            // edit
            if ($securityHelper->isGranted(CmsPermissionAttributeConstants::TABLE_EDITOR_EDIT, $this->oTableConf->sqlData['name'])) {
                $oMenuItem = new TCMSTableEditorMenuItem();
                $oMenuItem->sDisplayName = ServiceLocator::get('translator')->trans('chameleon_system_core.action.save_and_return');
                $oMenuItem->sIcon = 'far fa-save';
                $oMenuItem->sOnClick = 'SaveViaAjaxCustomCallback(postSaveHook, true);';
                $this->oMenuItems->AddItem($oMenuItem);
            }

            // usage
            $oMenuItem = new TCMSTableEditorMenuItem();
            $oMenuItem->sDisplayName = ServiceLocator::get('translator')->trans('chameleon_system_core.table_editor_document.action_use');
            $oMenuItem->sItemKey = 'usage';
            $oMenuItem->sIcon = 'far fa-list-alt';
            $oMenuItem->sOnClick = "GetUsages('".$this->oTable->id."', 'document');";
            $this->oMenuItems->AddItem($oMenuItem);
            // now add custom items
            $this->GetCustomMenuItems();
        }

        $this->oMenuItems->GoToStart();

        return $this->oMenuItems;
    }

    /**
     * {@inheritdoc}
     */
    public function GetObjectShortInfo($postData = [])
    {
        $oRecordData = parent::GetObjectShortInfo();

        $oDownloadFile = new TCMSDownloadFile();
        /* @var $oDownloadFile TCMSDownloadFile */
        $oDownloadFile->Load($this->sId);
        $sDownLoadFileHTML = $oDownloadFile->getDownloadHtmlTag();
        $oRecordData->downloadHTML = $sDownLoadFileHTML;

        return $oRecordData;
    }

    /**
     * {@inheritdoc}
     */
    public function GetHtmlHeadIncludes()
    {
        $aIncludes = parent::GetHtmlHeadIncludes();

        $aIncludes[] = "
      <script language=\"Javascript\" type=\"text/javascript\">
        function postSaveHook(data,statusText) {
          SaveViaAjaxCallback(data,statusText);
          if (data != false) {
            parent.reloadFilesList();
            parent.reloadSelectedFilesList();
            if(typeof window.parent.editDocument == 'function' || typeof window.parent.editDocument == 'object') {
              var assignedDocumentHTML = '<div id=\"documentManager_' + parent._fieldName  + '_' + data.id + '\">';
              assignedDocumentHTML += '<table class=\"table table-striped\"><tr>';
              assignedDocumentHTML += '<td>';
              assignedDocumentHTML += data.downloadHTML;
              assignedDocumentHTML += '</td>';
              assignedDocumentHTML += '<td>';
              assignedDocumentHTML += '<button class=\"btn btn-danger btn-sm\" type=\"button\" onclick=\"if(confirm(\'".ServiceLocator::get('translator')->trans('chameleon_system_core.table_editor_document.action_remove_confirm')."?\')){removeDocument(parent._fieldName,data.id,parent._recordID,parent._tableID)};\"><i class=\"far fa-trash-alt mr-2\"></i>".ServiceLocator::get('translator')->trans('chameleon_system_core.table_editor_document.action_remove')."';
              assignedDocumentHTML += '</td>';
              assignedDocumentHTML += '</tr>';
              assignedDocumentHTML += '</table>';
              assignedDocumentHTML += '</div>';

              window.parent.editDocument(parent._fieldName,data.id,assignedDocumentHTML);
            }
          }
        }
      </script>
      ";
        $aIncludes[] = '<link href="'.TGlobal::GetPathTheme().'/css/table.css" rel="stylesheet" type="text/css" />'; // we need this for the list of usages

        return $aIncludes;
    }

    /**
     * deletes a document from harddisk.
     *
     * @param string $fileID
     * @param bool $bWorkflowOnly - deprecated - remove only the file from workflow temp directory
     *
     * @return bool
     */
    protected function DeleteFile($fileID, $bWorkflowOnly = false)
    {
        // check for filetype
        $checkFileTypeQuery = "
          SELECT FT.*
            FROM cms_document AS DF
       LEFT JOIN cms_filetype AS FT ON DF.cms_filetype_id = FT.id
           WHERE DF.id = '".MySqlLegacySupport::getInstance()->real_escape_string($fileID)."'
        ";

        $bDeleteSuccess = true;
        if ($checkFileTypeResult = MySqlLegacySupport::getInstance()->query($checkFileTypeQuery)) {
            $checkFileTypeRow = MySqlLegacySupport::getInstance()->fetch_assoc($checkFileTypeResult);
            $filename = $fileID.'.'.$checkFileTypeRow['file_extension'];

            $filePath = PATH_CMS_CUSTOMER_DATA.'/'.$filename;
            if (file_exists($filePath)) {
                try {
                    $this->getFileManager()->remove($filePath);
                    $bDeleteSuccess = true;
                } catch (IOExceptionInterface $exception) {
                    $bDeleteSuccess = false;
                }
            }
        }

        return $bDeleteSuccess;
    }

    /**
     * @return InputFilterUtilInterface
     */
    private function getInputFilterUtil()
    {
        return ServiceLocator::get('chameleon_system_core.util.input_filter');
    }
}
