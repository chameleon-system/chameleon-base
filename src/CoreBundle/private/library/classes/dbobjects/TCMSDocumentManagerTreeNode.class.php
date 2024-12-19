<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

class TCMSDocumentManagerTreeNode extends TCMSTreeNode
{
    /**
     * children of node.
     *
     * @var TdbCmsDocumentTreeList
     */
    public $oChildren;

    public function __construct($id = null)
    {
        parent::__construct($id, 'cms_document_tree');
    }

    /**
     * {@inheritdoc}
     */
    public function GetChildren($includeHidden = false, $languageId = null)
    {
        if (null !== $this->oChildren) {
            return $this->oChildren;
        }
        $databaseConnection = $this->getDatabaseConnection();
        $quotedTableName = $databaseConnection->quoteIdentifier($this->table);
        $quotedId = $databaseConnection->quote($this->id);
        $query = "SELECT `cms_document_tree`.*
                  FROM $quotedTableName
                 WHERE `cms_document_tree`.`parent_id` = $quotedId
                 ORDER BY `cms_document_tree`.entry_sort";

        $this->oChildren = TdbCmsDocumentTreeList::GetList($query, $languageId);

        return $this->oChildren;
    }

    public function GetFilesInDirectory()
    {
        $oFileList = false;
        if (!is_null($this->id)) {
            $query = "SELECT `cms_document`.*
                    FROM `cms_document`
                   WHERE `cms_document`.`cms_document_tree_id` = '".MySqlLegacySupport::getInstance()->real_escape_string($this->id)."'
                   ORDER BY `cms_document`.`name`
                   ";
            $oFileList = TdbCmsDocumentList::GetList($query);
        }

        return $oFileList;
    }
}
