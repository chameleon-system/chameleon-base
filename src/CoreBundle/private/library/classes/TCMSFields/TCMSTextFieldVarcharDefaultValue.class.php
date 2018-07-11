<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

class TCMSTextFieldVarcharDefaultValue extends TCMSFieldVarchar
{
    public function GetHTML()
    {
        $sHTML = '<div style="display:none" id="OldDefaultValue">'.$this->_GetHTMLValue().'</div>';
        $sHTML .= '
        <label>
          <input id="UpdateRecordsWithOldDefaultValue" type="checkbox" name="UpdateRecordsWithOldDefaultValue" value="1" />
          '.TGlobal::OutHTML(TGlobal::Translate('chameleon_system_core.field_varchar_default_value.update_all', array('%sOldValue%' => $this->_GetHTMLValue()))).'
        </label>
      ';

        $sHTML .= parent::GetHTML();

        return $sHTML;
    }
}
