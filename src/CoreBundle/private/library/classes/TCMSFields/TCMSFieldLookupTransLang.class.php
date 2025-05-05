<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use ChameleonSystem\CoreBundle\ServiceLocator;
use ChameleonSystem\SecurityBundle\Service\SecurityHelperAccess;

/**
 * lookup.
 * /**/
class TCMSFieldLookupTransLang extends TCMSFieldLookup
{
    public function GetOptions()
    {
        /** @var SecurityHelperAccess $securityHelper */
        $securityHelper = ServiceLocator::get(SecurityHelperAccess::class);
        $languages = $securityHelper->getUser()?->getAvailableEditLanguages();
        if (null === $languages) {
            $languages = [];
        }

        $languageList = implode(
            ', ',
            array_map(fn (string $languageId) => $this->getDatabaseConnection()->quote($languageId),
                array_keys($languages))
        );

        $this->options = [];
        $tblName = mb_substr($this->name, 0, -3);
        $oTableConf = new TCMSTableConf();
        $oTableConf->LoadFromField('name', $tblName);
        $sNameField = $oTableConf->GetNameColumn();

        if (!empty($languageList)) {
            $query = 'SELECT * FROM '.$this->getDatabaseConnection()->quoteIdentifier($tblName).' WHERE `id` IN ('.$languageList.') ORDER BY '.$this->getDatabaseConnection()->quoteIdentifier($sNameField);
        } else {
            $query = 'SELECT * FROM '.$this->getDatabaseConnection()->quoteIdentifier($tblName).' ORDER BY '.$this->getDatabaseConnection()->quoteIdentifier($sNameField);
        }
        $oTable = new TCMSRecordList('TCMSRecord', $tblName, $query);
        $this->allowEmptySelection = true; // add the "please choose" option
        while ($oRow = $oTable->Next()) {
            $this->options[$oRow->id] = $oRow->GetName();
        }
    }
}
