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
/**/
class TCMSTableEditor_PkgComment extends TCMSTableEditor
{
    /**
     * {@inheritDoc}
     */
    protected function PostSaveHook(&$oFields, &$oPostTable)
    {
        parent::PostSaveHook($oFields, $oPostTable);
        if (property_exists($oPostTable, 'sqlData') && is_array($oPostTable->sqlData)) {
            if (array_key_exists('pkg_comment_id', $oPostTable->sqlData) && !empty($oPostTable->sqlData['pkg_comment_id'])) {
                TCacheManager::PerformeTableChange('pkg_comment', $oPostTable->sqlData['pkg_comment_id']);
            }
            if (array_key_exists('pkg_comment_type_id', $oPostTable->sqlData) && !empty($oPostTable->sqlData['pkg_comment_type_id']) && array_key_exists('item_id', $oPostTable->sqlData) && !empty($oPostTable->sqlData['item_id'])) {
                $sTableName = TdbPkgCommentType::GetCommentTypeTableName($oPostTable->sqlData['pkg_comment_type_id']);
                if (!empty($sTableName)) {
                    TCacheManager::PerformeTableChange($sTableName, $oPostTable->sqlData['item_id']);
                }
            }
        }
    }

    /**
     * fetches short record data for processing after an ajaxSave
     * is returned by Save method
     * id and name is always available in the returned object
     * overwrite this method to add custom return data.
     *
     * @param array $postData
     *
     * @return TCMSstdClass
     */
    public function GetObjectShortInfo($postData = array())
    {
        $oRecordData = parent::GetObjectShortInfo($postData);

        if (array_key_exists('item_id', $postData)) {
            $oRecordData->fieldItemId = $postData['item_id'];
        }

        return $oRecordData;
    }
}
