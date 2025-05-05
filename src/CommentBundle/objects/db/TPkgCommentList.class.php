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
 * ;.
 * /**/
class TPkgCommentList extends TPkgCommentListAutoParent
{
    /**
     * return number of comments for an item.
     *
     * @return int $count
     */
    public function GetNrOfComments()
    {
        return $this->Length();
    }

    /**
     * factory returning an element for the list - return a child of that class (if the fields class, class_subtype and class_type have been set).
     *
     * @param array $aData
     */
    protected function _NewElement($aData): TdbPkgComment
    {
        $oElement = parent::_NewElement($aData);
        if ($oElement) {
            $oCommentType = $oElement->GetFieldPkgCommentType();
            if ($oCommentType && !empty($oCommentType->sqlData['pkg_comment_class_name'])) {
                $aRow = $oElement->sqlData;
                $sClassName = $oCommentType->sqlData['pkg_comment_class_name'];
                $oElement = new $sClassName();
                $oElement->LoadFromRow($aRow);
            }
        }

        return $oElement;
    }
}
