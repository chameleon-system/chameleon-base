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
use ChameleonSystem\AutoclassesBundle\TableConfExport\DoctrineTransformableInterface;
use ChameleonSystem\CoreBundle\ServiceLocator;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * {inheritdoc}.
 */
class TCMSFieldBoolean extends TCMSFieldOption implements DoctrineTransformableInterface
{
    public function getDoctrineDataModelParts(string $namespace, array $tableNamespaceMapping): DataModelParts
    {
        $default = $this->oDefinition->sqlData['field_default_value'];
        if ('' === $default) {
            $default = '0';
        }

        $parameters = [
            'source' => get_class($this),
            'type' => 'bool',
            'docCommentType' => 'bool',
            'description' => $this->oDefinition->sqlData['translation'],
            'propertyName' => $this->snakeToCamelCase($this->name),
            'defaultValue' => sprintf("%s", '1' === $default ? 'true' : 'false'),
            'allowDefaultValue' => true,
            'getterName' => 'is'. $this->snakeToPascalCase($this->name),
            'setterName' => 'set'. $this->snakeToPascalCase($this->name),
        ];
        $propertyCode = $this->getDoctrineRenderer('model/default.property.php.twig', $parameters)->render();
        $methodCode = $this->getDoctrineRenderer('model/default.methods.php.twig', $parameters)->render();

        return new DataModelParts(
            $propertyCode,
            $methodCode,
            $this->getDoctrineDataModelXml($namespace),
            [],
            true
        );
    }

    protected function getDoctrineDataModelXml(string $namespace): string
    {
        $default = $this->oDefinition->sqlData['field_default_value'];
        if ('' === $default) {
            $default = '0';
        }
        return $this->getDoctrineRenderer('mapping/boolean.xml.twig', [
            'fieldName' => $this->snakeToCamelCase($this->name),
            'type' => 'boolean',
            'comment' => $this->oDefinition->sqlData['translation'],
            'column' => $this->name,
            'default' => $default,
        ])->render();
    }


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

        $checked = '';
        $disabled = '';
        if (true === $this->isChecked()) {
            $checked = ' checked';
            $disabled = ' disabled';
        }

        $html = '
            <label class="switch switch-lg switch-label switch-success">
                <input id="'.TGlobal::OutHTML($this->name).'" type="checkbox" value="1" name="'.TGlobal::OutHTML($this->name).'" autocomplete="off" class="switch-input"'.$checked.'>
                <span class="switch-slider" data-active="✓" data-inactive="✕"></span>
            </label>
            <input id="'.TGlobal::OutHTML($this->name).'hidden" type="hidden" value="0" name="'.TGlobal::OutHTML($this->name).'" autocomplete="off"'.$disabled.'>';

        return $html;
    }

    /**
     * {@inheritdoc}
     */
    public function GetReadOnly()
    {
        $checked = '';
        if (true === $this->isChecked()) {
            $checked = ' checked';
        }

        // The checkbox in readonly mode is a dummy for styling issues and therefore has no name and value.
        $html = '
            <label class="switch switch-lg switch-label switch-success">
            <input type="checkbox" autocomplete="off" class="switch-input"'.$checked.' disabled>
                <span class="switch-slider" data-active="✓" data-inactive="✕"></span>
            </label>';

        return $html;
    }

    private function isChecked()
    {
        return '1' === $this->data;
    }

    /**
     * {@inheritdoc}
     */
    protected function GetFieldWriterData()
    {
        $aData = parent::GetFieldWriterData();

        $aData['sFieldType'] = 'bool';

        $aData['sFieldDefaultValue'] = 'false';
        if ('1' == $this->data) {
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
        if (false === parent::DataIsValid()) {
            return false;
        }

        if ('0' == $this->data || '1' == $this->data || false === $this->HasContent()) {
            return true;
        }

        $messageManager = TCMSMessageManager::GetInstance();
        $consumerName = TCMSTableEditorManager::MESSAGE_MANAGER_CONSUMER;
        $fieldTitle = $this->oDefinition->GetName();
        $messageManager->AddMessage($consumerName, 'TABLEEDITOR_FIELD_BOOLEAN_NOT_VALID', array('sFieldName' => $this->name, 'sFieldTitle' => $fieldTitle));

        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function HasContent()
    {
        return '0' == $this->data || false === empty($this->data);
    }

    /**
     * {@inheritdoc}
     */
    public function _GetSQLDefinition($fieldDefinition = null)
    {
        // never allow an empty default value for boolean fields
        if (isset($fieldDefinition['field_default_value']) && '' === $fieldDefinition['field_default_value']) {
            $fieldDefinition['field_default_value'] = '0';
        }

        return parent::_GetSQLDefinition($fieldDefinition);
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
        return ServiceLocator::get('translator');
    }
}
