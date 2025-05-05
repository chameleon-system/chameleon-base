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
 * MLT field
 * shows a list of all available languages based on the languages that are configured in CMS config table
 * for field based translation.
 *
 * /**/
class TCMSFieldLookupMultiselectCheckboxesPossibleLanguages extends TCMSFieldLookupMultiselectCheckboxes
{
    protected function GetMLTRecordRestrictions()
    {
        $sRestrictions = parent::GetMLTRecordRestrictions();

        $oCMSConfig = TdbCmsConfig::GetInstance();
        $oPossibleLanguageList = $oCMSConfig->GetFieldCmsLanguageList();
        $sIDs = $oPossibleLanguageList->GetIdList('id', true);
        if (!empty($sIDs)) {
            $sIDs .= ',';
        }
        $sIDs .= "'".$oCMSConfig->fieldTranslationBaseLanguageId."'";
        if (!empty($sIDs)) {
            $sRestrictions .= ' AND `cms_language`.`id`  IN ('.$sIDs.')';
        }

        return $sRestrictions;
    }
}
