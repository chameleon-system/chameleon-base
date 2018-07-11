<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

class TCMSFieldLookupFieldTypes extends TCMSFieldLookup
{
    protected $sFieldHelpTextHTML = '';

    public function GetHTML()
    {
        $this->GetOptions();
        $html = "<script type=\"text/javascript\">
      function showFieldTypeHelp(fieldTypeID) {
        var fieldID = '#fieldTypeHelp' + fieldTypeID;
        var helpText = $(fieldID).html();
        if(helpText == '') {
          $('#".TGlobal::OutJS($this->name)."helpContainer').html('&nbsp;');
        } else {
          $('#".TGlobal::OutJS($this->name)."helpContainer').html(helpText);
        }
      }

      $(document).ready(function(){
        showFieldTypeHelp('".$this->data."');
      });

      </script>";

        $html .= '<div class="row">
        <div class="col-md-6">
        <select name="'.TGlobal::OutHTML($this->name).'" id="'.TGlobal::OutHTML($this->name)."\" class=\"form-control input-sm\" onkeyup=\"showFieldTypeHelp(this.options[this.selectedIndex].value)\" onchange=\"showFieldTypeHelp(this.options[this.selectedIndex].value)\">\n";
        foreach ($this->options as $key => $value) {
            $selected = '';
            if (0 == strcmp($this->data, $key)) {
                $selected = 'selected="selected"';
            }
            $html .= '<option value="'.TGlobal::OutHTML($key)."\" {$selected} onmouseover=\"showFieldTypeHelp(this.value)\">".TGlobal::OutHTML($value)."</option>\n";
        }
        $html .= '</select>
        </div>';

        $html .= '<div id="'.TGlobal::OutHTML($this->name)."helpContainer\" class=\"helpText col-md-6\"></div>
        </div>\n";

        $html .= $this->sFieldHelpTextHTML;

        return $html;
    }

    public function GetOptions()
    {
        $tblName = $this->GetConnectedTableName();
        $listClass = TCMSTableToClass::GetClassName(TCMSTableToClass::PREFIX_CLASS, $tblName).'List';
        $this->options = array();
        $query = $this->GetOptionsQuery();

        /** @var TCMSRecordList $sourceList */
        $sourceList = call_user_func(array($listClass, 'GetList'), $query);

        while ($oRow = $sourceList->Next()) {
            $name = $oRow->GetName();
            if (!empty($name)) {
                $this->options[$oRow->id] = $oRow->GetName();
            }

            $this->sFieldHelpTextHTML .= '<div id="fieldTypeHelp'.$oRow->id.'" style="display: none;">'.$oRow->GetTextField('help_text').'</div>'."\n";
        }
    }
}
