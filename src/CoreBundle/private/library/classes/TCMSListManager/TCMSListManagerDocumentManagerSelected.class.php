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
 * /**/
class TCMSListManagerDocumentManagerSelected extends TCMSListManagerFullGroupTable
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
     * add MLT connection check.
     */
    public function GetCustomRestriction()
    {
        $query = '';

        $oGlobal = TGlobal::instance();

        if (!is_null($this->sRestrictionField) && !is_null($this->sRestriction) && $oGlobal->UserDataExists('fieldName')) {
            if ('_mlt' == substr($this->sRestrictionField, -4)) {
                $fieldName = $oGlobal->GetUserData('fieldName');
                if (!is_null($this->fieldCount)) {
                    $mltTable = substr($this->sRestrictionField, 0, -4).'_'.$fieldName.'_'.$this->oTableConf->sqlData['name'].$this->fieldCount.'_mlt';
                } else {
                    $mltTable = substr($this->sRestrictionField, 0, -4).'_'.$fieldName.'_'.$this->oTableConf->sqlData['name'].'_mlt';
                }

                $MLTquery = 'SELECT * FROM `'.MySqlLegacySupport::getInstance()->real_escape_string($mltTable)."` WHERE `source_id` = '".MySqlLegacySupport::getInstance()->real_escape_string($this->sRestriction)."'";
                $MLTResult = MySqlLegacySupport::getInstance()->query($MLTquery);
                $sqlError = MySqlLegacySupport::getInstance()->error();
                if (!empty($sqlError)) {
                    trigger_error('SQL Error: '.$sqlError, E_USER_WARNING);
                }
                $aIDList = [];
                while ($row = MySqlLegacySupport::getInstance()->fetch_assoc($MLTResult)) {
                    $aIDList[] = $row['target_id'];
                }

                if (count($aIDList) > 0) {
                    $databaseConnection = $this->getDatabaseConnection();
                    $idListString = implode(',', array_map([$databaseConnection, 'quote'], $aIDList));
                    $query .= $this->CreateRestriction('id', "  IN ($idListString)");
                } else {
                    $query .= '1=0';
                }
            } else {
                $query = parent::GetCustomRestriction();
            }
        }

        return $query;
    }

    /**
     * add additional fields.
     */
    public function AddFields()
    {
        parent::AddFields();

        $sNameSQL = $this->oTableConf->GetNameColumn();
        $this->tableObj->RemoveHeaderField($sNameSQL);
        $this->tableObj->RemoveColumn('title');

        $this->tableObj->RemoveHeaderField('cmsident');
        $this->tableObj->RemoveColumn('cmsident');

        $jsParas = ['id'];
        ++$this->columnCount;
        $this->tableObj->AddHeaderField(['id' => '#'], 'left', null, 1, false);
        $this->tableObj->AddColumn('id', 'left', [$this, 'CallBackDocumentAssignedSelectBox'], null, 1);

        ++$this->columnCount;
        $this->tableObj->AddHeaderField(['cms_filetype_id' => ChameleonSystem\CoreBundle\ServiceLocator::get('translator')->trans('chameleon_system_core.list_document.file_type')], 'left', null, 1, false);
        $this->tableObj->AddColumn('cms_filetype_id', 'left', [$this, 'CallBackGenerateDownloadLink'], null, 1);

        ++$this->columnCount;
        $this->tableObj->AddHeaderField(['name' => ChameleonSystem\CoreBundle\ServiceLocator::get('translator')->trans('chameleon_system_core.list_document.title')], 'left', null, 1, false);
        $this->tableObj->AddColumn('name', 'left', null, $jsParas, 1);
        $this->tableObj->searchFields['`cms_document`.`name`'] = 'full';

        ++$this->columnCount;
        $this->tableObj->AddHeaderField(['filename' => ChameleonSystem\CoreBundle\ServiceLocator::get('translator')->trans('chameleon_system_core.list_document.file_name')], 'left', null, 1, false);
        $this->tableObj->AddColumn('filename', 'left', [$this, 'CallBackFilenameShort'], $jsParas, 1);
        $this->tableObj->searchFields['`cms_document`.`filename`'] = 'full';

        ++$this->columnCount;
        $this->tableObj->AddHeaderField(['filesize' => ChameleonSystem\CoreBundle\ServiceLocator::get('translator')->trans('chameleon_system_core.list_document.file_size')], 'left', null, 1, false);
        $this->tableObj->AddColumn('filesize', 'left', [$this, 'CallBackHumanRedableFileSize'], $jsParas, 1);
    }

    protected function AddRowPrefixFields()
    {
    }

    public function GetHtmlHeadIncludes()
    {
        $aIncludes = parent::GetHtmlHeadIncludes();
        $aIncludes[] = '<link rel="stylesheet" href="'.TGlobal::GetStaticURLToWebLib('/css/cms_user_style/main.css').'" type="text/css" media="screen" />';

        return $aIncludes;
    }

    /**
     * returns a checkbox field for assigned document file selection with javascript onlick.
     *
     * @param string $id
     * @param array $row
     *
     * @return string
     */
    public function CallBackDocumentAssignedSelectBox($id, $row)
    {
        return "<input type=\"checkbox\" name=\"functionSelection[]\" value=\"{$id}\" onclick=\"parent.ChangeAssignedFileSelection('{$id}')\" />";
    }

    /**
     * returns a filetype icon that is linked with the download file.
     *
     * @param string $id
     * @param array $row
     *
     * @return string
     */
    public function CallBackGenerateDownloadLink($id, $row)
    {
        $oFile = new TCMSDownloadFile();
        /* @var $oFile TCMSDownloadFile */
        $oFile->Load($row['id']);
        $sDownloadLink = $oFile->getDownloadHtmlTag(false, true, true);

        return $sDownloadLink;
    }

    /**
     * @return Connection
     */
    private function getDatabaseConnection()
    {
        return ChameleonSystem\CoreBundle\ServiceLocator::get('database_connection');
    }
}
