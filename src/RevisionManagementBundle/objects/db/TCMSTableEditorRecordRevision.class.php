<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

class TCMSTableEditorRecordRevision extends TCMSTableEditor
{
    /**
     * deletes the record and all language childs; updates all references to this record.
     *
     * @param int $sId
     */
    public function Delete($sId = null)
    {
        if (!is_null($sId)) {
            $this->sId = $sId;

            parent::Delete($sId);

            // delete all childs
            $query = "DELETE FROM `cms_record_revision` WHERE `cms_record_revision_id` = '".MySqlLegacySupport::getInstance()->real_escape_string($sId)."'";
            MySqlLegacySupport::getInstance()->query($query);
        }
    }
}
