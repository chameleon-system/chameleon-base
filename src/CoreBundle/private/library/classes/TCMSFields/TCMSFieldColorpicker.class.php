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
 * used to select a color.
/**/
class TCMSFieldColorpicker extends TCMSField
{
    public function GetHTML()
    {
        $value = $this->data;
        if (!empty($this->data)) {
            $value = '#'.$value;
        }
        $html = '<div class="input-group" id="colorPickerCotainer'.TGlobal::OutHTML($this->name).'">
    <span class="input-group-addon"><i></i></span>
    <input type="text" value="'.TGlobal::OutHTML($value).'" class="form-control" id="'.TGlobal::OutHTML($this->name).'" name="'.TGlobal::OutHTML($this->name).'" />
</div>
<script>
    $(function(){
        $(\'#colorPickerCotainer'.TGlobal::OutHTML($this->name).'\').colorpicker({ format: \'hex\'} );
    });
</script>';

        return $html;
    }

    public function _GetHTMLValue()
    {
        if (empty($this->data)) {
            $returnData = $this->oDefinition->sqlData['field_default_value'];
        } else {
            $returnData = $this->data;
        }

        return $returnData;
    }

    /**
     * return an array of all js, css, or other header includes that are required
     * in the cms for this field. each include should be in one line, and they
     * should always be typed the same way so that no includes are included mor than once.
     *
     * @return array
     */
    public function GetCMSHtmlHeadIncludes()
    {
        $aIncludes = array();
        $aIncludes[] = '<link href="'.URL_CMS.'/javascript/bootstrap-colorpicker/css/bootstrap-colorpicker.min.css" rel="stylesheet" type="text/css" />';
        $aIncludes[] = '<script src="'.URL_CMS.'/javascript/bootstrap-colorpicker/js/bootstrap-colorpicker.min.js" type="text/JavaScript"></script>';

        return $aIncludes;
    }

    public static function isFirstInstance()
    {
        static $isFirst;
        if (!$isFirst && true !== $isFirst && false !== $isFirst) {
            $isFirst = false;

            return true;
        }

        return $isFirst;
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
            $pattern = '/^#[0-9a-fA-F]{6}$/';
            if ($this->HasContent() && !preg_match($pattern, $this->data)) {
                $bDataIsValid = false;
                $oMessageManager = TCMSMessageManager::GetInstance();
                $sConsumerName = TCMSTableEditorManager::MESSAGE_MANAGER_CONSUMER;
                $sFieldTitle = $this->oDefinition->GetName();
                $oMessageManager->AddMessage($sConsumerName, 'TABLEEDITOR_FIELD_COLOR_NOT_VALID', array('sFieldName' => $this->name, 'sFieldTitle' => $sFieldTitle));
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

    /**
     * this method converts post data like datetime (3 fields with date, hours, minutes in human readable format)
     * to sql format.
     *
     * @return string
     */
    public function ConvertPostDataToSQL()
    {
        $this->data = str_replace('#', '', $this->data);

        return $this->data;
    }
}
