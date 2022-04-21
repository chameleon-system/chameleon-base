<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use ChameleonSystem\CoreBundle\ServiceLocator;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * an enum field.
/**/
class TCMSFieldOption extends TCMSField
{
    /**
     * array of options to list in the select box.
     *
     * @var array
     */
    protected $options = array();

    /**
     * view path for frontend.
     */
    protected $sViewPath = 'TCMSFields/views/TCMSFieldOption';

    /**
     * set this to true if you want to allow an empty selection in the select box.
     *
     * @var bool
     */
    protected $allowEmptySelection = false;

    /**
     * {@inheritdoc}
     */
    public function GetHTML()
    {
        $this->GetOptions();

        if (count($this->options) >= 3) {
            // show as select box
            $html = '<div>';
            $html .= '<select name="'.TGlobal::OutHTML($this->name).'" id="'.TGlobal::OutHTML($this->name).'" class="form-control form-control-sm" data-select2-option=\'{"width": "100%"}\'>';
            if ($this->allowEmptySelection) {
                $chooseMessage = TGlobal::Translate('chameleon_system_core.form.select_box_nothing_selected');

                $html .= '<option value="">'.TGlobal::OutHTML($chooseMessage)."</option>\n";
            }
            foreach ($this->options as $key => $value) {
                $selected = '';
                if ($this->data == $key) {
                    $selected = ' selected="selected"';
                }
                $html .= '<option value="'.TGlobal::OutHTML($key)."\"{$selected}>".TGlobal::OutHTML($value)."</option>\n";
            }
            $html .= '</select>';

            $html .= "</div>\n";
        } else {
            // show as radio button
            $html = '';

            $selected = '';
            if ('' == $this->data) {
                $selected = ' checked="checked"';
            }

            if ($this->allowEmptySelection) {
                $html .= '<label style="padding-right: 10px;"><input type="radio" class="radio" style="margin-right: 3px;" id="'.TGlobal::OutHTML($this->name).'" name="'.TGlobal::OutHTML($this->name)."\" value=\"\"{$selected} /> ".TGlobal::Translate('chameleon_system_core.field_options.select_nothing')."</label>\n";
            }

            foreach ($this->options as $key => $value) {
                $selected = '';
                if ($this->data == $key) {
                    $selected = ' checked="checked"';
                }
                $html .= '<label style="padding-right: 10px;"><input type="radio" class="radio" style="margin-right: 3px;" id="'.TGlobal::OutHTML($this->name).'" name="'.TGlobal::OutHTML($this->name).'" value="'.TGlobal::OutHTML($key)."\"{$selected} /> ".TGlobal::OutHTML($value)."</label>\n";
            }
        }

        return $html;
    }

    /**
     * {@inheritdoc}
     */
    public function GetReadOnly()
    {
        $this->GetOptions();
        if (array_key_exists($this->data, $this->options)) {
            return $this->_GetHiddenField().'<div class="form-content-simple">'.TGlobal::OutHTML($this->options[$this->data]).'</div>';
        } else {
            return $this->_GetHiddenField();
        }
    }

    /**
     * {@inheritdoc}
     */
    public function GetOptions()
    {
        // the field description holds the values
        $query = 'SHOW FIELDS FROM `'.MySqlLegacySupport::getInstance()->real_escape_string($this->sTableName)."` LIKE '".MySqlLegacySupport::getInstance()->real_escape_string($this->name)."'";
        if ($field = MySqlLegacySupport::getInstance()->fetch_assoc(MySqlLegacySupport::getInstance()->query($query))) {
            $type = substr($field['Type'], 5, -1);
            $tmp = explode("','", $type);
            foreach ($tmp as $value) {
                if ("'" === mb_substr($value, 0, 1)) {
                    $value = mb_substr($value, 1);
                } // remove the starting "'"
                if ("'" === mb_substr($value, -1)) {
                    $value = mb_substr($value, 0, -1);
                } // remove the ending "'"

                $this->options[$value] = $this->translateEnumKey($value);
            }
        }

        // to be sure that yes/no and similar fields are formatted the same way resort the value array by key
        if (count($this->options) <= 3) {
            ksort($this->options);
        }
    }

    /**
     * {@inheritdoc}
     */
    protected function GetFieldWriterData()
    {
        $aData = parent::GetFieldWriterData();

        $value = \str_replace("'", "\'", $this->data);
        $aData['sFieldDefaultValue'] = "'$value'";

        return $aData;
    }

    /**
     * {@inheritdoc}
     */
    public function DataIsValid()
    {
        $bDataIsValid = parent::DataIsValid();
        if ($bDataIsValid) {
            $this->GetOptions();
            if ($this->HasContent() && !array_key_exists($this->data, $this->options)) {
                $bDataIsValid = false;
                $oMessageManager = TCMSMessageManager::GetInstance();
                $sConsumerName = TCMSTableEditorManager::MESSAGE_MANAGER_CONSUMER;
                $sFieldTitle = $this->oDefinition->GetName();
                $oMessageManager->AddMessage($sConsumerName, 'TABLEEDITOR_FIELD_OPTION_NOT_VALID', array('sFieldName' => $this->name, 'sFieldTitle' => $sFieldTitle));
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
    protected function GetAdditionalViewData()
    {
        $aAdditionalViewData = parent::GetAdditionalViewData();
        $this->GetOptions();
        $aAdditionalViewData['aOptions'] = $this->options;
        $aAdditionalViewData['aOptionNameMapping'] = $this->GetOptionNameMapping();

        return $aAdditionalViewData;
    }

    /**
     * @return array
     */
    protected function GetOptionNameMapping()
    {
        return array();
    }

    /**
     * public getter method for $this->allowEmptySelection.
     *
     * @return bool
     */
    public function EmptySelectionAllowed()
    {
        return $this->allowEmptySelection;
    }

    /**
     * returns the value or its translation.
     *
     * @param string $value
     *
     * @return string
     */
    private function translateEnumKey($value)
    {
        if ('class_type' === $this->name && in_array($value, array('Core', 'Custom-Core', 'Customer'))) {
            return $value;
        }
        $key = $this->createEnumTranslationKey($value);
        $translatedValue = $this->getTranslator()->trans($key, array(), \ChameleonSystem\CoreBundle\i18n\TranslationConstants::DOMAIN_BACKEND_ENUM);
        if ($translatedValue === $key) {
            return $value;
        }

        return $translatedValue;
    }

    /**
     * creates the translation key under which the translation of the option may potentially be found.
     *
     * @param string $value
     *
     * @return string
     */
    private function createEnumTranslationKey($value)
    {
        return $this->sTableName.'.'.$this->name.'.'.$value;
    }

    /**
     * {@inheritdoc}
     */
    public function GetCMSHtmlHeadIncludes()
    {
        $includes = parent::GetCMSHtmlHeadIncludes();
        $includes[] = '<link href="'.TGlobal::GetStaticURLToWebLib('/components/select2.v4/css/select2.min.css').'" media="screen" rel="stylesheet" type="text/css" />';

        return $includes;
    }

    /**
     * {@inheritdoc}
     */
    public function GetCMSHtmlFooterIncludes()
    {
        $includes = parent::GetCMSHtmlFooterIncludes();
        $includes[] = '<script src="'.TGlobal::GetStaticURLToWebLib('/components/select2.v4/js/select2.full.min.js').'" type="text/javascript"></script>';

        return $includes;
    }

    private function getTranslator(): TranslatorInterface
    {
        return ServiceLocator::get('translator');
    }
}
