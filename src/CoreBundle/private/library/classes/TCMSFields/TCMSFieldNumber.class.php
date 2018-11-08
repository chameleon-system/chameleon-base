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
 * a number (int).
/**/
class TCMSFieldNumber extends TCMSFieldVarchar
{
    public function GetHTML()
    {
        $html = '<input class="fieldnumber form-control input-sm" type="text" id="'.TGlobal::OutHTML($this->name).'" name="'.TGlobal::OutHTML($this->name).'" value="'.TGlobal::OutHTML($this->data).'" />';

        return $html;
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
        if ($bDataIsValid) {
            $iMaxFieldLength = $this->_GetFieldWidth();
            if (is_null($iMaxFieldLength) || empty($iMaxFieldLength)) {
                $iMaxFieldLength = 20;
            }
            $pattern = '/^(\\d{0,'.$iMaxFieldLength.'}|(-\\d{0,'.$iMaxFieldLength.'}))$/';
            if ($this->HasContent() && !preg_match($pattern, $this->data)) {
                $bDataIsValid = false;
                $oMessageManager = TCMSMessageManager::GetInstance();
                $sConsumerName = TCMSTableEditorManager::MESSAGE_MANAGER_CONSUMER;
                $sFieldTitle = $this->oDefinition->GetName();
                $oMessageManager->AddMessage($sConsumerName, 'TABLEEDITOR_FIELD_NUMBER_NOT_VALID', array('sFieldName' => $this->name, 'sFieldTitle' => $sFieldTitle));
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
        if ('' !== $this->data) {
            $bHasContent = true;
        }

        return $bHasContent;
    }

    /**
     * returns the length of a field
     * sets field max-width and field CSS width.
     *
     * @return int
     */
    public function _GetFieldWidth()
    {
        $iFieldMaxLength = parent::_GetFieldWidth();

        if (is_null($iFieldMaxLength) || empty($iFieldMaxLength)) {
            $query = 'DESCRIBE `'.MySqlLegacySupport::getInstance()->real_escape_string($this->sTableName).'`';
            $result = MySqlLegacySupport::getInstance()->query($query);
            if (MySqlLegacySupport::getInstance()->num_rows($result) > 0) {
                while ($aField = MySqlLegacySupport::getInstance()->fetch_assoc($result)) {
                    if ($aField['Field'] == $this->name) {
                        $sFieldType = $aField['Type'];
                        if ('int' == substr($sFieldType, 0, 3)) {
                            $iFieldMaxLengthTmp = intval(substr($sFieldType, 4, -1));
                            if (!empty($iFieldMaxLengthTmp)) {
                                $iFieldMaxLength = $iFieldMaxLengthTmp;
                            }
                        }
                    }
                }
            }
        }

        return $iFieldMaxLength;
    }
}
