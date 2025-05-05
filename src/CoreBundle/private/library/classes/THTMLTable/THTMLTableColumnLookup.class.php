<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

class THTMLTableColumnLookup extends THTMLTableColumn
{
    public const SELF_FIELD_DEF = 'THTMLTableColumnLookup,THTMLTable,Core';

    /**
     * method used to format the given value. overwrite this method for every column type you write.
     *
     * @param string $sValue
     * @param TCMSRecord $oTableRow
     *
     * @return string
     */
    protected function FormatValue($sValue, $oTableRow)
    {
        $oLookup = $oTableRow->GetLookup($this->sColumnAlias);

        return TGlobal::OutHTML($oLookup->GetName());
    }

    /**
     * returns a css class used for the column.
     *
     * @return string
     */
    public function GetColumnFormatCSSClass()
    {
        return 'THTMLTableColumnLookup';
    }
}
