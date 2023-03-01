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
 * std varchar text field (max 255 chars).
/**/
class TCMSFieldUID extends TCMSField
{
    // todo - doctrine transformation

    public function GetHTML()
    {
        parent::GetHTML();
        $html = $this->_GetHiddenField().'<div class="form-content-simple">'.$this->_GetHTMLValue().'</div>';

        return $html;
    }

    public function _GetHTMLValue()
    {
        $html = parent::_GetHTMLValue();
        $html = TGlobal::OutHTML($html);

        return $html;
    }

    /**
     * this method converts post data like datetime (3 fields with date, hours, minutes in human readable format)
     * to sql format.
     *
     * @return mixed
     */
    public function ConvertPostDataToSQL()
    {
        $sReturnVal = trim($this->data);
        if (empty($sReturnVal)) {
            $sReturnVal = $this->oDefinition->GetUIDForTable($this->sTableName);
        }

        return $sReturnVal;
    }
}
