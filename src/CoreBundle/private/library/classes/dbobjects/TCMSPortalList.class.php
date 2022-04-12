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
 * holds a list of all portals of the webpage.
 *
 * @extends TCMSRecordList<TCMSRecord>
 */
class TCMSPortalList extends TCMSRecordList
{
    public $_aNodeIds = null;

    public function __construct()
    {
        $sTableObject = 'TCMSRecord';
        $sTableName = 'cms_portal';
        $sQuery = 'SELECT * FROM `cms_portal` ORDER BY `name`';
        parent::__construct($sTableObject, $sTableName, $sQuery);
    }

    /**
     * @deprecated Named constructors are deprecated and will be removed with PHP8. When calling from a parent, please use `parent::__construct` instead.
     * @see self::__construct
     */
    public function TCMSPortalList()
    {
        $this->callConstructorAndLogDeprecation(func_get_args());
    }

    public function GetTreeNodes()
    {
        if (is_null($this->_aNodeIds)) {
            // the record position must remain unchanged... so we need to get the
            // current record pos
            $tmpPointer = $this->getItemPointer();
            $this->GoToStart();
            $this->_aNodeIds = array();
            while ($oItem = &$this->Next()) {
                $oTreeNode = &$oItem->GetTreeNode();
                if (!is_null($oTreeNode)) {
                    $this->_aNodeIds[] = $oTreeNode->id;
                }
            }
            if (0 == $tmpPointer) {
                $this->GoToStart();
            } else {
                $this->setItemPointer($tmpPointer);
            }
        }

        return $this->_aNodeIds;
    }
}
