<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

class TCMSFieldDateTimeNow extends TCMSFieldDateTime
{
    /**
     * view path for frontend.
     */
    protected $sViewPath = 'TCMSFields/views/TCMSFieldDateTimeNow';

    public function GetHTML()
    {
        // info: the date is converted to german format only at the moment
        $aDateParts = explode(' ', $this->_GetHTMLValue());
        $date = $aDateParts[0];
        if ('0000-00-00' == $date) {
            $date = ConvertDate(date('Y-m-d'), 'sql2g');
            $hour = date('H');
            $minutes = date('i');
        } else {
            $date = ConvertDate($date, 'sql2g');
            $aTimeParts = explode(':', $aDateParts[1]);
            $hour = $aTimeParts[0];
            $minutes = $aTimeParts[1];
        }

        $html = '<input type="hidden" name="'.TGlobal::OutHTML($this->name).'" value="" />
      <input type="text" id="'.TGlobal::OutHTML($this->name).'_date" name="'.TGlobal::OutHTML($this->name).'_date" value="'.TGlobal::OutHTML($date).'" style="width: 80px;" />
      &nbsp;&nbsp;
      <select id="'.TGlobal::OutHTML($this->name).'_hour"  name="'.TGlobal::OutHTML($this->name)."_hour\" class=\"form-control input-sm\" style=\"width: 65px; display: inline;\">\n";

        for ($i = 0; $i <= 23; ++$i) {
            $hourTmp = $i;
            if (1 == strlen($i)) {
                $hourTmp = '0'.$i;
            }
            $selected = '';
            if ($hourTmp == $hour) {
                $selected = ' selected="selected"';
            }

            $html .= "<option value=\"{$hourTmp}\"{$selected}>{$hourTmp}</option>\n";
        }

        $html .= '
      </select> :
      <select id="'.TGlobal::OutHTML($this->name).'_min"  name="'.TGlobal::OutHTML($this->name)."_min\" class=\"form-control input-sm\" style=\"width: 65px; display: inline;\">\n";

        for ($i = 0; $i <= 59; ++$i) {
            $minTmp = $i;
            if (1 == strlen($i)) {
                $minTmp = '0'.$i;
            }
            $selected = '';
            if ($minTmp == $minutes) {
                $selected = ' selected="selected"';
            }

            $html .= "<option value=\"{$minTmp}\"{$selected}>{$minTmp}</option>\n";
        }

        $html .= '
      </select>
       '.TGlobal::Translate('chameleon_system_core.field_date_time.time')."
      <script type=\"text/javascript\">
      \$(document).ready(function() {
          \$('#".TGlobal::OutJS($this->name)."_date').datepicker();
          \$(function(\$){
            \$('#".TGlobal::OutJS($this->name)."_date').mask('99.99.9999');
          });
      });
      </script>";

        return $html;
    }

    public function GetReadOnly()
    {
        $currentDate = $this->_GetHTMLValue();
        if ('0000-00-00 00:00:00' == $currentDate) {
            $html = date('Y-m-d H:i:s');
        } else {
            $aDateParts = explode(' ', $currentDate);
            $date = $aDateParts[0];
            if ('0000-00-00' == $date) {
                $date = '';
            } else {
                $date = ConvertDate($date, 'sql2g');
            }

            $aTimeParts = explode(':', $aDateParts[1]);
            $hour = $aTimeParts[0];
            $minutes = $aTimeParts[1];

            $html = $this->_GetHiddenField();
            $html .= TGlobal::OutHTML($date.' '.$hour.':'.$minutes.' '.TGlobal::Translate('chameleon_system_core.field_date_time.time'));
        }

        return $html;
    }
}
