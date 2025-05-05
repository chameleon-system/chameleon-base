<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

function gcf_Language($langID, $row, $fieldName)
{
    $name = 'keine Sprache gesetzt';
    if (!empty($langID)) {
        $oLanguage = new TCMSRecord();
        /* @var $oLanguage TCMSRecord */
        $oLanguage->table = 'cms_language';
        $oLanguage->Load($langID);
        $name = TGlobal::OutHTML($oLanguage->GetName());
    }

    return $name;
}
