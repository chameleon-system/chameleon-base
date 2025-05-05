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

class TCMSListManagerMediaSelector extends TCMSListManagerImagedatabase
{
    /**
     * default image for current position.
     *
     * @var TCMSImage
     */
    protected $oDefaultImage;

    /**
     * TGlobal object.
     *
     * @var TGlobal
     */
    protected $oGlobal;

    /**
     * @param TCMSImage $oImage - the default image for the current selected position
     */
    public function Init($oImage)
    {
        $this->oGlobal = TGlobal::instance();
        $this->oDefaultImage = $oImage;

        $tableConf = TdbCmsTblConf::GetNewInstance();
        $tableConf->LoadFromField('name', 'cms_media');

        parent::Init($tableConf);
    }

    /**
     * {@inheritdoc}
     */
    public function CreateTableObj()
    {
        parent::CreateTableObj();

        $this->tableObj->searchBoxContent = '';
    }

    /**
     * {@inheritdoc}
     */
    public function _AddFunctionColumn()
    {
    }

    /**
     * {@inheritdoc}
     */
    public function _GetRecordClickJavaScriptFunctionName()
    {
        return 'parent._SetImage';
    }

    /**
     * returns the cache parameters needed for identification of the right cache object.
     *
     * @return array
     */
    public function GetCacheParameters()
    {
        $aCacheParameters = parent::GetCacheParameters();

        if (!is_null($this->sRestriction)) {
            $oImage = new TCMSImage();
            /* @var $oImage TCMSImage */
            $oImage->Load($this->sRestriction);
            if (!empty($oImage->aData['width']) && $oImage->aData['width'] > 0) {
                $aCacheParameters['width'] = $oImage->aData['width'];
            }

            if (!empty($oImage->aData['height']) && $oImage->aData['height'] > 0) {
                $aCacheParameters['height'] = $oImage->aData['height'];
            }
        } else {
            // cut down the result to the size of the default image
            // we need to check for allowed filetypes later!

            $aCacheParameters['width'] = $this->GetMaxWidth();
            $aCacheParameters['height'] = $this->GetMaxHeight();
        }

        $sAllowedFileTypes = $this->GetAllowedFileTypes();
        if (!empty($sAllowedFileTypes)) {
            $aCacheParameters['sAllowedFileTypes'] = $sAllowedFileTypes;
        }

        return $aCacheParameters;
    }

    /**
     * restrict the list to show only images with given dimensions.
     */
    public function GetCustomRestriction()
    {
        $query = parent::GetCustomRestriction();

        $cms_media_tree_id = $this->oGlobal->GetUserData('cms_media_tree_id');

        if (!empty($cms_media_tree_id)) {
            if (!empty($query)) {
                $query .= ' AND ';
            }
            $query .= '`'.MySqlLegacySupport::getInstance()->real_escape_string($this->oTableConf->sqlData['name'])."`.`cms_media_tree_id` = '".MySqlLegacySupport::getInstance()->real_escape_string($cms_media_tree_id)."'";
        } elseif (!empty($this->tableObj->_postData['cms_media_tree_id'])) {
            $cms_media_tree_id = $this->tableObj->_postData['cms_media_tree_id'];
            if (!empty($query)) {
                $query .= ' AND ';
            }
            $query .= '`'.MySqlLegacySupport::getInstance()->real_escape_string($this->oTableConf->sqlData['name'])."`.`cms_media_tree_id` = '".MySqlLegacySupport::getInstance()->real_escape_string($cms_media_tree_id)."'";
        }

        $aAllowedFileTypeIDs = $this->GetAllowedFileTypeIDs();
        if (count($aAllowedFileTypeIDs) > 0) {
            $databaseConnection = $this->getDatabaseConnection();
            $sAllowedFileTypeSearchString = implode(',', array_map([$databaseConnection, 'quote'], $aAllowedFileTypeIDs));

            if (!empty($query)) {
                $query .= ' AND ';
            }
            $query .= '`'.MySqlLegacySupport::getInstance()->real_escape_string($this->oTableConf->sqlData['name'])."`.`cms_filetype_id` IN ($sAllowedFileTypeSearchString)";
        }

        if (!is_null($this->sRestriction)) {
            $oImage = new TCMSImage();
            /* @var $oImage TCMSImage */
            $oImage->Load($this->sRestriction);
            if (!empty($oImage->aData['width']) && $oImage->aData['width'] > 0) {
                $query .= '`'.MySqlLegacySupport::getInstance()->real_escape_string($this->oTableConf->sqlData['name'])."`.`width` = '".MySqlLegacySupport::getInstance()->real_escape_string($oImage->aData['width'])."'";
            }

            if (!empty($oImage->aData['height']) && $oImage->aData['height'] > 0) {
                if (!empty($query)) {
                    $query .= ' AND ';
                }
                $query .= '`'.MySqlLegacySupport::getInstance()->real_escape_string($this->oTableConf->sqlData['name'])."`.`height` = '".MySqlLegacySupport::getInstance()->real_escape_string($oImage->aData['height'])."'";
            }
        } else {
            // filter the result by the image size
            $iMaxWidth = $this->GetMaxWidth();
            if ($iMaxWidth > 0) {
                if (!empty($query)) {
                    $query .= ' AND ';
                }
                $query .= '`'.MySqlLegacySupport::getInstance()->real_escape_string($this->oTableConf->sqlData['name'])."`.`width` = '".MySqlLegacySupport::getInstance()->real_escape_string($iMaxWidth)."'";
            }
            $iMaxHeight = $this->GetMaxHeight();
            if ($iMaxHeight > 0) {
                if (!empty($query)) {
                    $query .= ' AND ';
                }
                $query .= '`'.MySqlLegacySupport::getInstance()->real_escape_string($this->oTableConf->sqlData['name'])."`.`height` = '".MySqlLegacySupport::getInstance()->real_escape_string($iMaxHeight)."'";
            }
        }

        return $query;
    }

    /**
     * loads max image width from default image or get/post variable "imageWidth".
     *
     * @return int
     */
    protected function GetMaxWidth()
    {
        $iWidth = 0;

        if ($this->oGlobal->UserDataExists('imageWidth')) {
            $iWidth = $this->oGlobal->GetUserData('imageWidth');
        } else {
            if (isset($this->oDefaultImage->aData) && is_array($this->oDefaultImage->aData)) {
                if (array_key_exists('width', $this->oDefaultImage->aData) && $this->oDefaultImage->aData['width'] > 0) {
                    $iWidth = $this->oDefaultImage->aData['width'];
                }
            }
        }

        return $iWidth;
    }

    /**
     * Retrieves max image height from GET/POST variable "imageHeight".
     *
     * @return int
     */
    protected function GetMaxHeight()
    {
        $iHeight = 0;

        if ($this->oGlobal->UserDataExists('imageHeight')) {
            $iHeight = $this->oGlobal->GetUserData('imageHeight');
        }

        return $iHeight;
    }

    /**
     * loads allowed media filetypes from default image or get/post variable sAllowedFileTypes.
     *
     * @return string - comma seperated list of file endings (lowecase)
     */
    protected function GetAllowedFileTypes()
    {
        $sAllowedFileTypes = '';

        if ($this->oGlobal->UserDataExists('sAllowedFileTypes')) {
            $sAllowedFileTypes = strtolower($this->oGlobal->GetUserData('sAllowedFileTypes'));
        } else {
            if (isset($this->oDefaultImage->aData) && is_array($this->oDefaultImage->aData)) {
                if (array_key_exists('filetypes', $this->oDefaultImage->aData) && !empty($this->oDefaultImage->aData['filetypes'])) {
                    $sAllowedFileTypes = strtolower($this->oDefaultImage->aData['filetypes']);
                    $sAllowedFileTypes = str_replace('jpeg', 'jpg', $sAllowedFileTypes);
                }
            }
        }

        return $sAllowedFileTypes;
    }

    /**
     * returns array of allowed filetypes based on default image
     * or get/post variable sAllowedFileTypes.
     *
     * @return array
     */
    protected function GetAllowedFileTypeIDs()
    {
        $aAllowedFileTypeIDs = [];

        $sAllowedFileTypes = $this->GetAllowedFileTypes();
        if (!empty($sAllowedFileTypes)) {
            $aAllowedFileTypes = explode(',', $sAllowedFileTypes);
            foreach ($aAllowedFileTypes as $sFileEnding) {
                $oCmsFiletype = TdbCmsFiletype::GetNewInstance();
                /** @var $oCmsFiletype TdbCmsFiletype */
                if ($oCmsFiletype->LoadFromField('file_extension', $sFileEnding)) {
                    $aAllowedFileTypeIDs[] = $oCmsFiletype->id;
                }
            }
        }

        return $aAllowedFileTypeIDs;
    }

    /**
     * {@inheritdoc}
     */
    public function AddTableGrouping($columnCount = '')
    {
        $groupField = '`cms_media_tree`.`name`';
        $list_group_field_column = 'category';

        $this->tableObj->showGroupSelector = false;
        $this->tableObj->AddGroupField([$list_group_field_column => $groupField], 'left', null, null, $this->columnCount);
        $this->tableObj->showAllGroupsText = '['.ChameleonSystem\CoreBundle\ServiceLocator::get('translator')->trans('chameleon_system_core.list.group_show_all').']';
        $tmpArray = [$list_group_field_column => 'ASC'];
        $this->tableObj->orderList = array_merge($tmpArray, $this->tableObj->orderList);
    }

    /**
     * @return Connection
     */
    private function getDatabaseConnection()
    {
        return ChameleonSystem\CoreBundle\ServiceLocator::get('database_connection');
    }
}
