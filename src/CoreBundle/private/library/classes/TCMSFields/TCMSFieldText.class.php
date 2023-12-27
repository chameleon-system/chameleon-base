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

/**
 * a long text field.
/**/
class TCMSFieldText extends TCMSField implements DoctrineTransformableInterface
{
    /**
     * view path for frontend.
     */
    protected $sViewPath = 'TCMSFields/views/TCMSFieldText';

    public function getDoctrineDataModelParts(string $namespace, array $tableNamespaceMapping): DataModelParts
    {
        $parameters = [
            'source' => get_class($this),
            'type' => 'string',
            'docCommentType' => 'string',
            'description' => $this->oDefinition->sqlData['translation'],
            'propertyName' => $this->snakeToPascalCase($this->name),
            'defaultValue' => sprintf("'%s'", addslashes($this->oDefinition->sqlData['field_default_value'])),
            'allowDefaultValue' => true,
            'getterName' => 'get'. $this->snakeToCamelCase($this->name),
            'setterName' => 'set'. $this->snakeToCamelCase($this->name),
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
        $parameter = [
            'fieldName' => $this->snakeToPascalCase($this->name),
            'type' => 'text',
            'column' => $this->name,
            'comment' => $this->oDefinition->sqlData['translation'],

        ];
        if ('' !== $this->oDefinition->sqlData['field_default_value']) {
            $parameter['default'] = $this->oDefinition->sqlData['field_default_value'];
        }

        return $this->getDoctrineRenderer('mapping/text.xml.twig', $parameter)->render();
    }


    /**
     * {@inheritdoc}
     */
    public function GetHTML()
    {
        parent::GetHTML();

        return $this->renderTextArea($this->data, false);
    }

    private function renderTextArea(string $data, bool $readOnly): string
    {
        $cssParts = [];
        if ('100%' !== $this->fieldCSSwidth) {
            $cssParts[] = 'width:'.$this->fieldCSSwidth;
        }

        if ('' !== $data) {
            $lineCount = count(explode("\n", $data));
            $height = min(300, 18 * ($lineCount + 2)); // 18 should correspond to the actual line height
            if ($height > 100) {
                $cssParts[] = 'height:'.$height.'px';
            }
        }

        $cssStyle = \implode(';', $cssParts);

        $html = '';
        $html .= sprintf(
            '<textarea id="%s" name="%s" class="fieldtext form-control form-control-sm resizable" width="%s" style="%s" %s>',
            TGlobal::OutHTML($this->name),
            TGlobal::OutHTML($this->name),
            $this->fieldWidth,
            $cssStyle,
            true === $readOnly ? 'readonly' : ''
        );
        $html .= TGlobal::OutHTML($data);
        $html .= '</textarea>';

        return $html;
    }

    /**
     * {@inheritdoc}
     */
    public function GetReadOnly()
    {
        // todo: remove need to call _GetFieldWidth here
        // instead of just returning the field width, the method sets some properties on $this - which are needed
        // by renderTextArea.
        $this->_GetFieldWidth();

        return $this->renderTextArea($this->data, true);
    }

    /**
     * {@inheritdoc}
     */
    public function HasContent()
    {
        $bHasContent = false;
        if ('' != trim($this->data)) {
            $bHasContent = true;
        }

        return $bHasContent;
    }

    /**
     * {@inheritDoc}
     */
    public function ConvertPostDataToSQL()
    {
        $data = parent::ConvertPostDataToSQL();

        // make sure line endings are always consistent (simple \n)
        $data = \str_replace("\r\n", "\n", $data);
        $data = \str_replace("\r", "\n", $data);

        return $data;
    }
}
