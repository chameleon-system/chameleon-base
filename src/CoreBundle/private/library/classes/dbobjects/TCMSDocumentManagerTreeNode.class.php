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
    public $oChildren = null;

    public function __construct($id = null)
    {
        parent::__construct('cms_document_tree', $id);
    }

    /**
     * @deprecated Named constructors are deprecated and will be removed with PHP8. When calling from a parent, please use `parent::__construct` instead.
     * @see self::__construct
     */
    public function TCMSDocumentManagerTreeNode()
    {
        $this->callConstructorAndLogDeprecation(func_get_args());
    }


    /**
     * {@inheritdoc}
     */
    public function &GetChildren($includeHidden = false, $languageId = null)
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
