<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use ChameleonSystem\AutoclassesBundle\TableConfExport\DataModelParts;

class TCMSFieldTime extends TCMSFieldNumber
{
    public function getDoctrineDataModelParts(string $namespace, array $tableNamespaceMapping): DataModelParts
    {
        throw new Exception('TCMSFieldTime not implemented yet');
    }

    /**
     * {@inheritdoc}
     */
    public function GetHTML()
    {
        $sTime = $this->_GetHTMLValue();
        $aFields = $this->TimeToField($sTime);
        $hour = $aFields[0];
        $minutes = $aFields[1];
        $seconds = $aFields[2];
        $html = '<input type="hidden" name="'.TGlobal::OutHTML($this->name).'" id="'.TGlobal::OutHTML($this->name).'" value="'.TGlobal::OutHTML($this->data)."\" />\n";
        $html .= '<select id="'.TGlobal::OutHTML($this->name).'_hour" name="'.TGlobal::OutHTML($this->name)."_hour\" class=\"form-control form-control-sm\" style=\"width: 65px; display: inline;\">\n";

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
      <select id="'.TGlobal::OutHTML($this->name).'_min"  name="'.TGlobal::OutHTML($this->name)."_min\" class=\"form-control form-control-sm\" style=\"width: 65px; display: inline;\">\n";

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
      </select>';
        $html .= '
      </select> :
      <select id="'.TGlobal::OutHTML($this->name).'_sec"  name="'.TGlobal::OutHTML($this->name)."_sec\" class=\"form-control form-control-sm\" style=\"width: 65px; display: inline;\">\n";

        for ($i = 0; $i <= 59; ++$i) {
            $minTmp = $i;
            if (1 == strlen($i)) {
                $minTmp = '0'.$i;
            }
            $selected = '';
            if ($minTmp == $seconds) {
                $selected = ' selected="selected"';
            }

            $html .= "<option value=\"{$minTmp}\"{$selected}>{$minTmp}</option>\n";
        }

        $html .= '
      </select>';

        return $html;
    }

    /**
     * {@inheritdoc}
     */
    public function GetReadOnly()
    {
        $sTime = $this->_GetHTMLValue();
        $aFields = $this->TimeToField($sTime);
        $html = $this->_GetHiddenField();

        $translator = $this->getTranslator();
        $html .= TGlobal::OutHTML($aFields[0].':'.$aFields[1].':'.$aFields[2].' '.$translator->trans('chameleon_system_core.field.time_format', [], 'admin'));

        return $html;
    }

    /**
     * this method converts post data like datetime (3 fields with date, hours, minutes in human readable format)
     * to sql format.
     *
     * @return string
     */
    public function ConvertPostDataToSQL()
    {
        $oGlobal = TGlobal::instance();
        if ($oGlobal->UserDataExists($this->name.'_hour')) {
            $iHour = $oGlobal->GetUserData($this->name.'_hour');
        } else {
            $iHour = 0;
        }
        if ($oGlobal->UserDataExists($this->name.'_min')) {
            $iMinutes = $oGlobal->GetUserData($this->name.'_min');
        } else {
            $iMinutes = 0;
        }
        if ($oGlobal->UserDataExists($this->name.'_sec')) {
            $iSeconds = $oGlobal->GetUserData($this->name.'_sec');
        } else {
            $iSeconds = 0;
        }

        return $this->FieldToTime($iHour, $iMinutes, $iSeconds);
    }

    /**
     * {@inheritdoc}
     */
    protected function _GetHiddenField()
    {
        $sTime = $this->_GetHTMLValue();
        $aFields = $this->TimeToField($sTime);

        $html = '<input type="hidden" name="'.TGlobal::OutHTML($this->name).'" id="'.TGlobal::OutHTML($this->name).'" value="'.TGlobal::OutHTML($this->data)."\" />\n";
        $html .= '<input type="hidden" name="'.TGlobal::OutHTML($this->name).'_hour" id="'.TGlobal::OutHTML($this->name).'_hour" value="'.TGlobal::OutHTML($aFields[0])."\" />\n";
        $html .= '<input type="hidden" name="'.TGlobal::OutHTML($this->name).'_min" id="'.TGlobal::OutHTML($this->name).'_min" value="'.TGlobal::OutHTML($aFields[1])."\" />\n";
        $html .= '<input type="hidden" name="'.TGlobal::OutHTML($this->name).'_sec" id="'.TGlobal::OutHTML($this->name).'_sec" value="'.TGlobal::OutHTML($aFields[2])."\" />\n";

        return $html;
    }

    /**
     * converts the fields hours, minutes & seconds to duration (hh:mm:ss).
     *
     * @param int $iFieldHour
     * @param int $iFieldMin
     * @param int $iFieldSec
     *
     * @return string
     */
    protected function FieldToTime($iFieldHour, $iFieldMin, $iFieldSec)
    {
        return "$iFieldHour:$iFieldMin:$iFieldSec";
    }

    /**
     * converts duration (hh:mm:ss) to array (hours, minutes, seconds).
     *
     * @param string $sTime
     *
     * @return array - (hours, minutes, seconds)
     */
    protected function TimeToField($sTime)
    {
        return explode(':', $sTime);
    }

    /**
     * @return TranslatorInterface
     */
    private function getTranslator()
    {
        return ChameleonSystem\CoreBundle\ServiceLocator::get('translator');
    }
}
