<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

function gcf_ExportFieldName($fieldname, $row, $fieldName)
{
    $profileID = $row['cms_export_profiles_id'];

    $query = "SELECT * FROM `cms_export_profiles` WHERE `id` = '".MySqlLegacySupport::getInstance()->real_escape_string($profileID)."'";
    $result = MySqlLegacySupport::getInstance()->query($query);
    $profileRow = MySqlLegacySupport::getInstance()->fetch_assoc($result);

    $tableID = $profileRow['cms_tbl_conf_id'];

    $fieldQuery = "SELECT * FROM `cms_field_conf` WHERE `name` = '".MySqlLegacySupport::getInstance()->real_escape_string($fieldname)."' AND `cms_tbl_conf_id` = '".MySqlLegacySupport::getInstance()->real_escape_string($tableID)."'";

    $result2 = MySqlLegacySupport::getInstance()->query($fieldQuery);
    $fieldRow = MySqlLegacySupport::getInstance()->fetch_assoc($result2);

    return TGlobal::OutHTML($fieldRow['translation']);
}
