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
 * std varchar text field (max 255 chars).
/**/
class TCMSFieldUID extends TCMSField implements DoctrineTransformableInterface
{

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
        return $this->getDoctrineRenderer('mapping/string-guid.xml.twig', [
            'fieldName' => $this->snakeToPascalCase($this->name),
            'type' => 'string',
            'column' => $this->name,
            'comment' => $this->oDefinition->sqlData['translation'],
        ])->render();
    }

    public function GetHTML()
    {
        parent::GetHTML();
        $html = $this->_GetHiddenField().'<div class="form-content-simple">'.$this->_GetHTMLValue().'</div>';

        return $html;
    }

    public function _GetHTMLValue()
    {
        $html = parent::_GetHTMLValue();
        $html = TGlobal::OutHTML($html);

        return $html;
    }

    /**
     * this method converts post data like datetime (3 fields with date, hours, minutes in human readable format)
     * to sql format.
     *
     * @return mixed
     */
    public function ConvertPostDataToSQL()
    {
        $sReturnVal = trim($this->data);
        if (empty($sReturnVal)) {
            $sReturnVal = $this->oDefinition->GetUIDForTable($this->sTableName);
        }

        return $sReturnVal;
    }
}
