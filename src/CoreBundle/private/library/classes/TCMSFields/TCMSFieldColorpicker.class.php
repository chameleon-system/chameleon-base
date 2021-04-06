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

        return '
            <div class="input-group input-group-sm" id="colorPickerContainer'.TGlobal::OutHTML($this->name).'">
                <input type="hidden" value="'.TGlobal::OutHTML(
                $value
            ).'" id="'.TGlobal::OutHTML($this->name).'" name="'.TGlobal::OutHTML($this->name).'">
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
                $('#colorPickerContainer".TGlobal::OutHTML($this->name)."').colorpicker({
                  format: 'hex', 
                  useHashPrefix: true,
                  autoInputFallback: false,
                  inline: true,
                  container: true,
                  template: '<div class=\"colorpicker\">' +
                  '<div class=\"colorpicker-saturation\"><i class=\"colorpicker-guide\"></i></div>' +
                  '<div class=\"colorpicker-hue\"><i class=\"colorpicker-guide\"></i></div>' +
                  '<div class=\"colorpicker-alpha\">' +
                  '   <div class=\"colorpicker-alpha-color\"></div>' +
                  '   <i class=\"colorpicker-guide\"></i>' +
                  '</div>' +
                  '<div class=\"colorpicker-bar\">' +
                  '   <div class=\"input-group\">' +
                  '       <input class=\"form-control input-block color-io\" />' +
                  '   </div>' +
                  '</div>' +
                  '</div>'
                })
                .on('colorpickerCreate', function (e) {
                  var io = e.colorpicker.element.find('.color-io');
                  ".('' !== $this->data ? 'io.val(e.color.string());' : '')."
                  io.on('change keyup', function () {
                    e.colorpicker.setValue(io.val());
                  });
                })
                .on('colorpickerChange', function (e) {
                  var io = e.colorpicker.element.find('.color-io');
                  if (e.color === null) {
                      e.colorpicker.inputHandler.input.val('');
                      return;
                  }
                  if (e.value === io.val() || !e.color || !e.color.isValid()) {
                    // Do not replace the input value if the color is invalid or equals the current value.
                    return;
                  }
        
                  io.val(e.color.string());
                });
            });
            </script>";

        return $includes;
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
