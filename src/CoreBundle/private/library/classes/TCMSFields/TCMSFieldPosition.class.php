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
class TCMSFieldPosition extends TCMSField
{
    /**
     * conf of the table holding the position field.
     *
     * @var TCMSTableConf
     */
    protected $oTableConf = null;

    public function GetHTML()
    {
        $this->GetTableConf();
        $oGlobal = TGlobal::instance();

        // check for custom restriction in fieldtype config
        $restrictionField = $this->oDefinition->GetFieldtypeConfigKey('restrictionfield');

        if (!isset($restrictionField) || is_null($restrictionField) || empty($restrictionField) || false == $restrictionField) {
            $restrictionField = $oGlobal->GetUserData('sRestrictionField');
            $restrictionValue = $oGlobal->GetUserData('sRestriction');
        } elseif (array_key_exists($restrictionField, $this->oTableRow->sqlData)) {
            $restrictionValue = $this->oTableRow->sqlData[$restrictionField];
        } else {
            $restrictionValue = '';
        }

        $html = TCMSRender::DrawButton(TGlobal::Translate('chameleon_system_core.field_position.change'), "javascript:loadPositionList('".TGlobal::OutJS($this->oTableConf->id)."','".TGlobal::OutJS($this->sTableName)."','".TGlobal::OutJS($this->name)."','".TGlobal::OutJS($this->recordId)."','".TGlobal::OutJS($restrictionValue)."','".TGlobal::OutJS($restrictionField)."');", 'fas fa-sort');
        $html .= '<span id="'.TGlobal::OutHTML($this->name).'_dummy"></span>
      <input type="hidden" id="'.TGlobal::OutHTML($this->name).'" name="'.TGlobal::OutHTML($this->name).'" value="'.TGlobal::OutHTML($this->data)."\" />\n";

        return $html;
    }

    protected function GetTableConf()
    {
        if (is_null($this->oTableConf)) {
            $this->oTableConf = &$this->oTableRow->GetTableConf();
        }
    }

    public function GetSQL()
    {
        $returnVal = false;
        if ($this->data < 1) {
            $this->GetTableConf();
            // force position = max pos +1. even if this is a property table then this should work...
            $databaseConnection = $this->getDatabaseConnection();
            $quotedName = $databaseConnection->quoteIdentifier($this->name);
            $quotedSqlDataName = $databaseConnection->quoteIdentifier($this->oTableConf->sqlData['name']);
            $query = "SELECT MAX($quotedName) AS maxpos FROM $quotedSqlDataName";
            $result = MySqlLegacySupport::getInstance()->query($query);
            if ($tmp = MySqlLegacySupport::getInstance()->fetch_assoc($result)) {
                $this->data = $tmp['maxpos'] + 1;
            }

            $returnVal = $this->data;
        }

        if (false === $returnVal && $this->data > 0) {
            $returnVal = $this->data;
        }

        return $returnVal;
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
            $pattern = "/^\d+$/";
            if ($this->HasContent() && !preg_match($pattern, $this->data)) {
                $bDataIsValid = false;
                $oMessageManager = TCMSMessageManager::GetInstance();
                $sConsumerName = TCMSTableEditorManager::MESSAGE_MANAGER_CONSUMER;
                $sFieldTitle = $this->oDefinition->GetName();
                $oMessageManager->AddMessage($sConsumerName, 'TABLEEDITOR_FIELD_SORT_NOT_VALID', array('sFieldName' => $this->name, 'sFieldTitle' => $sFieldTitle));
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
        if ('' != $this->data) {
            $bHasContent = true;
        }

        return $bHasContent;
    }
}
