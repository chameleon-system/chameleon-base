<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

class TCMSFieldDuration extends TCMSFieldNumber
{
    public function GetHTML()
    {
        $iSeconds = $this->_GetHTMLValue();
        $aFields = $this->IntToField($iSeconds);
        $html = '<input type="hidden" name="'.TGlobal::OutHTML($this->name).'" id="'.TGlobal::OutHTML($this->name)."\" value=\"\" />\n";
        $html .= '<input class="fieldnumber form-control form-control-sm" style="width:20px;" type="text" maxlength="2" name="'.TGlobal::OutHTML($this->name).'_hour" id="'.TGlobal::OutHTML($this->name).'_hour" value="'.TGlobal::OutHTML($aFields['hours'])."\" />:\n";
        $html .= '<input class="fieldnumber form-control form-control-sm" style="width:20px;" type="text" maxlength="2" name="'.TGlobal::OutHTML($this->name).'_min"  id="'.TGlobal::OutHTML($this->name).'_min"  value="'.TGlobal::OutHTML($aFields['minutes'])."\" onchange=\"if(parseInt(this.value)>59) this.value=59; \" />:\n";
        $html .= '<input class="fieldnumber form-control form-control-sm" style="width:20px;" type="text" maxlength="2" name="'.TGlobal::OutHTML($this->name).'_sec"  id="'.TGlobal::OutHTML($this->name).'_sec"  value="'.TGlobal::OutHTML($aFields['seconds'])."\" onchange=\"if(parseInt(this.value)>59) this.value=59;\" /> hh:mm:ss\n";

        return $html;
    }

    /**
     * renders the read only view of the field.
     *
     * @return string
     */
    public function GetReadOnly()
    {
        $iSeconds = $this->_GetHTMLValue();
        $aFields = $this->IntToField($iSeconds);
        $html = $this->_GetHiddenField();
        $html .= TGlobal::OutHTML($aFields['hours'].':'.$aFields['minutes'].':'.$aFields['seconds'].' '.ChameleonSystem\CoreBundle\ServiceLocator::get('translator')->trans('chameleon_system_core.field.time_format'));

        return $html;
    }

    /**
     * this method converts post data like datetime (3 fields with date, hours, minutes in human readable format)
     * to sql format.
     *
     * @param bool $bAsSplittetValues - if set to true the method returns an array with the normalised (empty values filled) value parts
     */
    public function ConvertPostDataToSQL($bAsSplittetValues = false)
    {
        $returnVal = false;
        $bHourPassed = (false !== $this->oTableRow->sqlData && array_key_exists($this->name.'_hour', $this->oTableRow->sqlData) && '' != $this->oTableRow->sqlData[$this->name.'_hour']);
        $bMinutesPassed = (false !== $this->oTableRow->sqlData && array_key_exists($this->name.'_min', $this->oTableRow->sqlData) && '' != $this->oTableRow->sqlData[$this->name.'_min']);
        $bSecondsPassed = (false !== $this->oTableRow->sqlData && array_key_exists($this->name.'_sec', $this->oTableRow->sqlData) && '' != $this->oTableRow->sqlData[$this->name.'_sec']);
        if (!empty($bHourPassed) && !empty($bMinutesPassed) && !empty($bSecondsPassed)) {
            if ($bAsSplittetValues) {
                $returnVal = ['hours' => $this->oTableRow->sqlData[$this->name.'_hour'], 'minutes' => $this->oTableRow->sqlData[$this->name.'_min'], 'seconds' => $this->oTableRow->sqlData[$this->name.'_sec']];
            } else {
                $returnVal = $this->FieldToInt($this->oTableRow->sqlData[$this->name.'_hour'], $this->oTableRow->sqlData[$this->name.'_min'], $this->oTableRow->sqlData[$this->name.'_sec']);
            }
        } else {
            $bCompleteDatePassed = (false !== $this->oTableRow->sqlData && array_key_exists($this->name, $this->oTableRow->sqlData) && !empty($this->oTableRow->sqlData[$this->name]));
            if ($bCompleteDatePassed) {
                if ($bAsSplittetValues) {
                    $returnVal = $this->IntToField($this->oTableRow->sqlData[$this->name]);
                } else {
                    $returnVal = $this->oTableRow->sqlData[$this->name];
                }
            }
        }

        return $returnVal;
    }

    public function _GetHiddenField()
    {
        $iSeconds = $this->_GetHTMLValue();
        $aFields = $this->IntToField($iSeconds);

        $html = '<input type="hidden" name="'.TGlobal::OutHTML($this->name).'" id="'.TGlobal::OutHTML($this->name).'" value="'.TGlobal::OutHTML($this->data)."\" />\n";
        $html .= '<input type="hidden" name="'.TGlobal::OutHTML($this->name).'_hour" id="'.TGlobal::OutHTML($this->name).'_hour" value="'.TGlobal::OutHTML($aFields['hours'])."\" />\n";
        $html .= '<input type="hidden" name="'.TGlobal::OutHTML($this->name).'_min" id="'.TGlobal::OutHTML($this->name).'_min" value="'.TGlobal::OutHTML($aFields['minutes'])."\" />\n";
        $html .= '<input type="hidden" name="'.TGlobal::OutHTML($this->name).'_sec" id="'.TGlobal::OutHTML($this->name).'_sec" value="'.TGlobal::OutHTML($aFields['seconds'])."\" />\n";

        return $html;
    }

    /**
     * converts the fields hours, minutes & seconds to seconds.
     *
     * @param int $iFieldHour
     * @param int $iFieldMin
     * @param int $iFieldSec
     */
    public function FieldToInt($iFieldHour, $iFieldMin, $iFieldSec)
    {
        $iHourSeconds = $iFieldHour * 3600;
        $iMinutesSeconds = $iFieldMin * 60;
        $iSeconds = $iHourSeconds + $iMinutesSeconds + $iFieldSec;

        return $iSeconds;
    }

    /**
     * converts Integer to Duration (hh:mm:ss).
     *
     * @param int $iSeconds
     *
     * @return array - (hours, minutes, seconds)
     */
    public function IntToField($iSeconds)
    {
        $aFields = [];
        if (0 == $iSeconds) {
            $aFields['seconds'] = 0;
            $aFields['hours'] = 0;
            $aFields['minutes'] = 0;
        } else {
            $iMinutes = $iSeconds / 60;
            $aFields['seconds'] = $iSeconds % 60;
            $iHours = $iMinutes / 60;
            $aFields['hours'] = $iHours % 100;
            $aFields['minutes'] = $iMinutes % 60;
        }

        return $aFields;
    }

    /**
     * checks if field is mandatory and if field content is valid
     * overwrite this method to add your field based validation
     * you need to add a message to TCMSMessageManager for handling error messages
     * <code>
     * <?php
     *   $oMessageManager = TCMSMessageManager::GetInstance();
     *   $sConsumerName = TCMSTableEditorManager::MESSAGE_MANAGER_CONSUMER;
     *   $oMessageManager->AddMessage($sConsumerName,'TABLEEDITOR_FIELD_IS_MANDATORY');
     * ?>
     * </code>.
     *
     * @return bool - returns false if field is mandatory and field content is empty or data is not valid
     */
    public function DataIsValid()
    {
        $bDataIsValid = parent::DataIsValid();
        if ($bDataIsValid && $this->HasContent()) {
            $aData = $this->ConvertPostDataToSQL(true);
            $iHour = trim($aData['hours']);
            if (!is_numeric($iHour) || $iHour > 24 || $iHour < 0) {
                $bDataIsValid = false;
            }
            $iMinutes = trim($aData['minutes']);
            if (!is_numeric($iMinutes) || $iMinutes > 59 || $iMinutes < 0) {
                $bDataIsValid = false;
            }
            $iSeconds = trim($aData['seconds']);
            if (!is_numeric($iSeconds) || $iSeconds > 59 || $iSeconds < 0) {
                $bDataIsValid = false;
            }
            if (!$bDataIsValid) {
                $oMessageManager = TCMSMessageManager::GetInstance();
                $sConsumerName = TCMSTableEditorManager::MESSAGE_MANAGER_CONSUMER;
                $sFieldTitle = $this->oDefinition->GetName();
                $oMessageManager->AddMessage($sConsumerName, 'TABLEEDITOR_FIELD_DURATION_NOT_VALID', ['sFieldName' => $this->name, 'sFieldTitle' => $sFieldTitle]);
            }
        }

        return $bDataIsValid;
    }

    /**
     * returns true if field data is not empty
     * overwrite this method for mlt and property fields.
     *
     * @return bool
     */
    public function HasContent()
    {
        $bHasContent = false;
        $sValue = $this->ConvertPostDataToSQL();
        if ('' != $sValue) {
            $bHasContent = true;
        }

        return $bHasContent;
    }
}
