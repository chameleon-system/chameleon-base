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

/**
 * extends the standard listing so that a preview image is shown, and if the
 * class is called with the right parameters it will show an assign button to
 * assign an image from the list to the calling record.
/**/
class TCMSListManagerDocumentManager extends TCMSListManagerFullGroupTable
{
    /**
     * overwrite table config.
     */
    public function CreateTableObj()
    {
        parent::CreateTableObj();
        $this->tableObj->showRecordCount = 20;
    }

    /**
     * we need this to overwrite the standard function column.
     */
    public function _AddFunctionColumn()
    {
    }

    /**
     * returns the name of the javascript function to be called when the user clicks on a
     * record within the table.
     *
     * @return string
     */
    public function _GetRecordClickJavaScriptFunctionName()
    {
        return 'parent.editFileDetails';
    }

    /**
     * add additional fields.
     */
    public function AddFields()
    {
        parent::AddFields();

        $sLanguageIdent = '';
        $oCmsUser = TCMSUser::GetActiveUser();
        $oEditLang = $oCmsUser->GetCurrentEditLanguageObject();
        $oCmsConfig = TCMSConfig::GetInstance();
        $oDefaultLang = $oCmsConfig->GetFieldTranslationBaseLanguage();
        if ($oDefaultLang && $oDefaultLang->fieldIso6391 != $oEditLang->fieldIso6391) {
            $sLanguageIdent = $oEditLang->fieldIso6391;
        }

        $sNameSQL = $this->oTableConf->GetNameColumn();
        $this->tableObj->RemoveHeaderField($sNameSQL);
        $this->tableObj->RemoveColumn('title');

        $this->tableObj->RemoveHeaderField('cmsident');
        $this->tableObj->RemoveColumn('cmsident');

        $jsParas = array('id');
        ++$this->columnCount;
        $this->tableObj->AddHeaderField(array('id' => '#'), 'left', null, 1, false);
        $this->tableObj->AddColumn('id', 'left', array($this, 'CallBackDocumentSelectBox'), null, 1);

        ++$this->columnCount;
        $this->tableObj->AddHeaderField(array('cms_filetype_id' => TGlobal::Translate('chameleon_system_core.list_document.file_type')), 'left', null, 1, false);
        $this->tableObj->AddColumn('cms_filetype_id', 'left', array($this, 'CallBackGenerateDownloadLink'), null, 1);

        ++$this->columnCount;
        $this->tableObj->AddHeaderField(array('name' => TGlobal::Translate('chameleon_system_core.list_document.title')), 'left', null, 1, false);
        $sFieldNameTransformed = 'name';

        if (!empty($sLanguageIdent) && TdbCmsDocument::CMSFieldIsTranslated('name')) {
            $sFieldNameTransformed = $sFieldNameTransformed.'__'.$sLanguageIdent;
        }
        $this->tableObj->AddColumn(array('name' => '`cms_document`.`name`'), 'left', null, $jsParas, 1, null, null, null, 'name');
        $databaseConnection = $this->getDatabaseConnection();
        $quotedFieldNameTransformed = $databaseConnection->quoteIdentifier($sFieldNameTransformed);
        $this->tableObj->searchFields["`cms_document`.$quotedFieldNameTransformed"] = 'full'; // allow searching in this field

        ++$this->columnCount;
        $this->tableObj->AddHeaderField(array('filename' => TGlobal::Translate('chameleon_system_core.list_document.file_name')), 'left', null, 1, false);
        $this->tableObj->AddColumn('filename', 'left', array($this, 'CallBackFilenameShort'), $jsParas, 1);
        $this->tableObj->searchFields['`cms_document`.`filename`'] = 'full'; // allow searching in this field

        ++$this->columnCount;
        $this->tableObj->AddHeaderField(array('filesize' => TGlobal::Translate('chameleon_system_core.list_document.file_size')), 'left', null, 1, false);
        $this->tableObj->AddColumn('filesize', 'left', array($this, 'CallBackHumanRedableFileSize'), $jsParas, 1);
    }

    /**
     * any custom restrictions can be added to the query by overwriting this function.
     *
     * @param string $query
     */
    public function GetCustomRestriction()
    {
        $query = parent::GetCustomRestriction();

        $oGlobal = TGlobal::instance();

        if ($oGlobal->UserDataExists('mltTable') && $oGlobal->UserDataExists('recordID')) {
            $mltTable = $oGlobal->GetUserData('mltTable');
            $recordID = $oGlobal->GetUserData('recordID');

            $MLTquery = 'SELECT * FROM `'.MySqlLegacySupport::getInstance()->real_escape_string($mltTable)."` WHERE `source_id` = '".MySqlLegacySupport::getInstance()->real_escape_string($recordID)."'";
            $MLTResult = MySqlLegacySupport::getInstance()->query($MLTquery);
            $aIDList = array();

            if (MySqlLegacySupport::getInstance()->num_rows($MLTResult) > 0) {
                if (!empty($query)) {
                    $query .= ' AND ';
                }

                while ($row = MySqlLegacySupport::getInstance()->fetch_assoc($MLTResult)) {
                    $aIDList[] = $row['target_id'];
                }

                if (count($aIDList) > 0) {
                    $databaseConnection = $this->getDatabaseConnection();
                    $idListString = implode(',', array_map(array($databaseConnection, 'quote'), $aIDList));
                    $query .= $this->CreateRestriction('id', " NOT IN ($idListString)");
                }
            }
        }

        return $query;
    }

    public function GetHtmlHeadIncludes()
    {
        $aIncludes = parent::GetHtmlHeadIncludes();
        $aIncludes[] = '<link rel="stylesheet" href="'.TGlobal::GetStaticURLToWebLib('/css/cms_user_style/main.css').'" type="text/css" media="screen" />';

        return $aIncludes;
    }

    protected function AddRowPrefixFields()
    {
    }

    /**
     * returns a filetype icon that is linked with the download file.
     *
     * @param string $id
     * @param array  $row
     *
     * @return string
     */
    public function CallBackGenerateDownloadLink($id, $row)
    {
        $oFile = new TCMSDownloadFile();
        /** @var $oFile TCMSDownloadFile */
        $oFile->Load($row['id']);
        $sDownloadLink = $oFile->getDownloadHtmlTag(false, true, true);

        return $sDownloadLink;
    }

    /**
     * returns a checkbox field for document file selection with javascript onlick.
     *
     * @param string $id
     * @param array  $row
     *
     * @return string
     */
    public function CallBackDocumentSelectBox($id, $row)
    {
        return "<input type=\"checkbox\" name=\"functionSelection[]\" value=\"{$id}\" onclick=\"parent.ChangeFileSelection('{$id}')\" />";
    }

    /**
     * @return Connection
     */
    private function getDatabaseConnection()
    {
        return \ChameleonSystem\CoreBundle\ServiceLocator::get('database_connection');
    }
}
