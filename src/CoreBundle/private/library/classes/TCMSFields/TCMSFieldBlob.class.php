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
 * The class takes data and serializes it to db.
 *
 * /**/
class TCMSFieldBlob extends TCMSFieldText implements DoctrineTransformableInterface
{
    public function getDoctrineDataModelParts(string $namespace, array $tableNamespaceMapping): DataModelParts
    {
        $parameters = [
            'source' => get_class($this),
            'type' => '?object',
            'docCommentType' => 'object|null',
            'description' => $this->oDefinition->sqlData['translation'],
            'propertyName' => $this->snakeToCamelCase($this->name),
            'defaultValue' => 'null',
            'allowDefaultValue' => true,
            'getterName' => 'get'.$this->snakeToPascalCase($this->name),
            'setterName' => 'set'.$this->snakeToPascalCase($this->name),
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
            'fieldName' => $this->snakeToCamelCase($this->name),
            'type' => 'object',
            'column' => $this->name,
            'comment' => $this->oDefinition->sqlData['translation'],
        ];
        if ('' !== $this->oDefinition->sqlData['field_default_value']) {
            $parameter['default'] = $this->oDefinition->sqlData['field_default_value'];
        }

        return $this->getDoctrineRenderer('mapping/text.xml.twig', $parameter)->render();
    }

    public function ConvertDataToFieldBasedData($sData)
    {
        $sData = parent::ConvertDataToFieldBasedData($sData);
        $sData = serialize($sData);

        return $sData;
    }

    /**
     * this method converts post data like datetime (3 fields with date, hours, minutes in human readable format)
     * to sql format.
     */
    public function ConvertPostDataToSQL()
    {
        $sData = parent::ConvertPostDataToSQL();
        $sData = unserialize($sData);

        return $sData;
    }

    public function GetHTML()
    {
        $sOriginal = $this->data;
        $this->data = print_r($this->data, true);
        $sResponse = $this->GetReadOnly();
        $this->data = $sOriginal;

        return $sResponse;
    }

    public function RenderFieldPostLoadString()
    {
        $oViewParser = new TViewParser();
        /* @var $oViewParser TViewParser */
        $oViewParser->bShowTemplatePathAsHTMLHint = false;
        $aData = $this->GetFieldWriterData();
        $oViewParser->AddVarArray($aData);

        return $oViewParser->RenderObjectView('postload', 'TCMSFields/TCMSFieldBlob');
    }

    public function GetReadOnly()
    {
        if (is_string($this->data)) {
            $html = parent::GetReadOnly();
        } else {
            $html = TGlobal::OutHTML(print_r($this->data, true));
        }

        return '<pre style="white-space:pre">'.$html.'</pre>';
    }

    public function RenderFieldPropertyString()
    {
        $viewParser = new TViewParser();
        $viewParser->bShowTemplatePathAsHTMLHint = false;
        $data = $this->GetFieldWriterData();

        $viewParser->AddVarArray($data);

        return $viewParser->RenderObjectView('typed-property', 'TCMSFields/TCMSField');
    }

    protected function GetFieldWriterData()
    {
        $data = parent::GetFieldWriterData();
        $data['sFieldType'] = 'mixed';

        return $data;
    }
}
