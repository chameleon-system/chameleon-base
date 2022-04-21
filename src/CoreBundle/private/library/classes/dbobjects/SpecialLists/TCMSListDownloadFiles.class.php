<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * special record list to handle downloads.
 *
 * @extends TCMSRecordList<TCMSDownloadFile>
 */
class TCMSListDownloadFiles extends TCMSRecordList
{
    /**
     * name of the source table.
     *
     * @var string|null
     */
    protected $_sSourceTableName = null;

    /**
     * @var string|null
     */
    protected $_sRecordID = null;

    /**
     * @var string|null
     */
    protected $_sourceField = null;

    /**
     * @TODO This property never seems to get written to.
     * @var mixed|null
     */
    protected $allowedFileTypes = null;

    public function __construct()
    {
        parent::__construct($sTableObject = 'TCMSDownloadFile', 'cms_document');
    }

    /**
     * @deprecated Named constructors are deprecated and will be removed with PHP8. When calling from a parent, please use `parent::__construct` instead.
     * @see self::__construct
     */
    public function TCMSListDownloadFiles()
    {
        $this->callConstructorAndLogDeprecation(func_get_args());
    }

    /**
     * @param string|null $sourceTable
     * @param string|null $sourceField
     * @param string|null $sourceRecordID
     * @return void
     */
    public function Init($sourceTable, $sourceField, $sourceRecordID)
    {
        $this->_sSourceTableName = $sourceTable;
        $this->_sourceField = $sourceField;
        $this->_sRecordID = $sourceRecordID;
    }

    /**
     * {@inheritdoc}
     */
    public function Load($sQuery = null, array $queryParameters = array(), array $queryParameterTypes = array())
    {
        if (is_null($sQuery)) {
            $sQuery = $this->_GetQuery();
        }
        parent::Load($sQuery, $queryParameters, $queryParameterTypes);
    }

    protected function _GetQuery()
    {
        $query = null;
        if (!is_null($this->_sSourceTableName) && !is_null($this->_sRecordID)) {
            $mltTable = $this->_sSourceTableName.'_'.$this->_sourceField.'_cms_document_mlt';
            if (TCMSRecord::TableExists($mltTable)) {
                if (!is_null($this->allowedFileTypes) && is_array($this->allowedFileTypes) && count($this->allowedFileTypes) > 0) {
                    $fileExtensionsString = '';
                    foreach ($this->allowedFileTypes as $fileExtension) {
                        $fileExtensionsString .= "'".$fileExtension."',";
                    }
                    $fileExtensionsString = substr($fileExtensionsString, 0, -1);

                    $query = 'SELECT `'.MySqlLegacySupport::getInstance()->real_escape_string($this->sTableName).'`.*
                      FROM `'.MySqlLegacySupport::getInstance()->real_escape_string($this->sTableName).'`
                INNER JOIN `'.MySqlLegacySupport::getInstance()->real_escape_string($mltTable).'` ON `'.MySqlLegacySupport::getInstance()->real_escape_string($this->sTableName).'`.`id` = `'.MySqlLegacySupport::getInstance()->real_escape_string($mltTable).'`.`target_id`
                LEFT JOIN `cms_filetype` ON `'.MySqlLegacySupport::getInstance()->real_escape_string($this->sTableName).'`.`cms_filetype_id` = `cms_filetype`.`id`
                     WHERE `'.MySqlLegacySupport::getInstance()->real_escape_string($mltTable)."`.`source_id` = '".MySqlLegacySupport::getInstance()->real_escape_string($this->_sRecordID)."'
                           AND `cms_filetype`.`file_extension` IN (".$fileExtensionsString.')
                  ORDER BY `'.MySqlLegacySupport::getInstance()->real_escape_string($this->sTableName).'`.`name`
                   ';
                } else {
                    $query = 'SELECT `'.MySqlLegacySupport::getInstance()->real_escape_string($this->sTableName).'`.*
                      FROM `'.MySqlLegacySupport::getInstance()->real_escape_string($this->sTableName).'`
                INNER JOIN `'.MySqlLegacySupport::getInstance()->real_escape_string($mltTable).'` ON `'.MySqlLegacySupport::getInstance()->real_escape_string($this->sTableName).'`.`id` = `'.MySqlLegacySupport::getInstance()->real_escape_string($mltTable).'`.`target_id`
                     WHERE `'.MySqlLegacySupport::getInstance()->real_escape_string($mltTable)."`.`source_id` = '".MySqlLegacySupport::getInstance()->real_escape_string($this->_sRecordID)."'
                  ORDER BY `".MySqlLegacySupport::getInstance()->real_escape_string($this->sTableName).'`.`name`
                   ';
                }
            }
        }

        return $query;
    }
}
