<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Symfony\Component\Translation\TranslatorInterface;

/**
 * {inheritdoc}.
 */
class TCMSFieldBoolean extends TCMSFieldOption
{
    public function GetOptions()
    {
        parent::GetOptions();

        $translator = $this->getTranslator();
        // translate points
        $this->options['0'] = $translator->trans('chameleon_system_core.field_boolean.no', array(), 'admin');
        $this->options['1'] = $translator->trans('chameleon_system_core.field_boolean.yes', array(), 'admin');
    }

    /**
     * {@inheritdoc}
     */
    public function GetHTML()
    {
        $this->GetOptions();
        $html = '<div class="btn-group btn-group-sm TCMSFieldBoolean" data-toggle="buttons">';
        foreach ($this->options as $key => $value) {
            $key = (string) $key;

            $selected = '';
            $sClass = '';
            if ($this->data === $key) {
                $selected = ' checked';
                $sClass = 'active';
            }

            if ('1' === $key) {
                $sClass .= ' button-on';
            } else {
                $sClass .= ' button-off';
            }

            $html .= '<label class="btn btn-default '.$sClass.'">
                        <input type="radio" class="radio" autocomplete="off" id="'.TGlobal::OutHTML($this->name.$key).'" name="'.TGlobal::OutHTML($this->name).'" value="'.TGlobal::OutHTML($key).'"'.$selected.' /> '.TGlobal::OutHTML($value)."
                      </label>\n";
        }

        $html .= '</div>';

        return $html;
    }

    /**
     * {@inheritdoc}
     */
    protected function GetFieldWriterData()
    {
        $aData = parent::GetFieldWriterData();

        $aData['sFieldType'] = 'boolean';

        $aData['sFieldDefaultValue'] = 'false';
        if ('1' === $this->data) {
            $aData['sFieldDefaultValue'] = 'true';
        }

        return $aData;
    }

    public function RenderFieldPostLoadString()
    {
        $viewParser = new TViewParser();
        $viewParser->bShowTemplatePathAsHTMLHint = false;
        $aData = $this->GetFieldWriterData();
        $viewParser->AddVarArray($aData);

        return $viewParser->RenderObjectView('postload', 'TCMSFields/TCMSFieldBoolean');
    }

    /**
     * {@inheritdoc}
     */
    public function DataIsValid()
    {
        $bDataIsValid = parent::DataIsValid();
        if (true === $bDataIsValid && '0' !== $this->data && '1' !== $this->data && true === $this->HasContent()) {
            $messageManager = TCMSMessageManager::GetInstance();
            $consumerName = TCMSTableEditorManager::MESSAGE_MANAGER_CONSUMER;
            $fieldTitle = $this->oDefinition->GetName();
            $messageManager->AddMessage($consumerName, 'TABLEEDITOR_FIELD_BOOLEAN_NOT_VALID', array('sFieldName' => $this->name, 'sFieldTitle' => $fieldTitle));

            return false;
        }

        return $bDataIsValid;
    }

    /**
     * {@inheritdoc}
     */
    public function HasContent()
    {
        $bHasContent = false;
        if (!empty($this->data) || '0' === $this->data) {
            $bHasContent = true;
        }

        return $bHasContent;
    }

    /**
     * {@inheritdoc}
     */
    public function GetCMSHtmlHeadIncludes()
    {
        $aIncludes = parent::GetCMSHtmlHeadIncludes();
        $aIncludes[] = '<script type="text/javascript">
        $(document).ready(function() {
            $(".TCMSFieldBoolean .btn").bootstrapBtn();
        });
        </script>';

        return $aIncludes;
    }

    /**
     * {@inheritdoc}
     */
    public function _GetSQLDefinition(&$postData = null)
    {
        // never allow an empty default value for boolean fields
        if (isset($postData['field_default_value']) && '' === $postData['field_default_value']) {
            $postData['field_default_value'] = '0';
        }

        return parent::_GetSQLDefinition($postData);
    }

    /**
     * {@inheritdoc}
     */
    public function toString()
    {
        $this->GetOptions();

        return $this->options[$this->data];
    }

    /**
     * @return TranslatorInterface
     */
    private function getTranslator()
    {
        return \ChameleonSystem\CoreBundle\ServiceLocator::get('translator');
    }
}
