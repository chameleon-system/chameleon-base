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
 * Used to select a color.
 */
class TCMSFieldColorpicker extends TCMSField
{
    public function GetHTML()
    {
        $value = $this->data;
        if (!empty($this->data)) {
            $value = '#'.$value;
        }

        return '<div class="input-group input-group-sm" id="colorPickerContainer'.TGlobal::OutHTML($this->name).'">
    <span class="input-group-append">
        <span class="input-group-text colorpicker-input-addon"><i></i></span>
    </span>    
    <input type="text" value="'.TGlobal::OutHTML($value).'" class="form-control" id="'.TGlobal::OutHTML($this->name).'" name="'.TGlobal::OutHTML($this->name).'" />
</div>
';
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
     * {@inheritdoc}
     */
    public function GetCMSHtmlHeadIncludes()
    {
        $includes = parent::GetCMSHtmlHeadIncludes();
        $includes[] = '<link href="'.TGlobal::GetStaticURLToWebLib('/javascript/bootstrap-colorpicker-3.0.3/css/bootstrap-colorpicker.min.css').'" media="screen" rel="stylesheet" type="text/css" />';

        return $includes;
    }

    /**
     * {@inheritdoc}
     */
    public function GetCMSHtmlFooterIncludes()
    {
        $includes = parent::GetCMSHtmlFooterIncludes();
        $includes[] = '<script src="'.TGlobal::GetStaticURLToWebLib('/javascript/bootstrap-colorpicker-3.0.3/js/bootstrap-colorpicker.min.js').'" type="text/javascript"></script>';
        $includes[] = "<script>
    $(function() {
        $('#colorPickerContainer".TGlobal::OutHTML($this->name)."').colorpicker({ format: 'hex', useHashPrefix: true });
    });
    </script>";

        return $includes;
    }

    /**
     * @deprecated since 6.3.0 - no longer used in Chameleon (looks like this is an orphan which was used by an older color picker).
     *
     * @return bool
     */
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
     * {@inheritdoc}
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
     * {@inheritdoc}
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
     * {@inheritdoc}
     */
    public function ConvertPostDataToSQL()
    {
        $this->data = str_replace('#', '', $this->data);

        return $this->data;
    }
}
