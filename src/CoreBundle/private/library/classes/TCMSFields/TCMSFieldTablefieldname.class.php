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
 * field name in a table.
 * /**/
class TCMSFieldTablefieldname extends TCMSFieldOption implements DoctrineTransformableInterface
{
    public function getDoctrineDataModelParts(string $namespace, array $tableNamespaceMapping): DataModelParts
    {
        $parameters = [
            'source' => get_class($this),
            'type' => 'string',
            'docCommentType' => 'string',
            'description' => $this->oDefinition->sqlData['translation'],
            'propertyName' => $this->snakeToCamelCase($this->name),
            'defaultValue' => sprintf("'%s'", addslashes($this->oDefinition->sqlData['field_default_value'])),
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
        return $this->getDoctrineRenderer('mapping/string.xml.twig', [
            'fieldName' => $this->snakeToCamelCase($this->name),
            'type' => 'string',
            'column' => $this->name,
            'comment' => $this->oDefinition->sqlData['translation'],
            'default' => $this->oDefinition->sqlData['field_default_value'],
            'length' => '' === $this->oDefinition->sqlData['length_set'] ? 255 : $this->oDefinition->sqlData['length_set'],
        ])->render();
    }

    public function GetOptions()
    {
        // use the field name to make a lookup of the right table
        if (stristr($this->name, '_cmsfieldname')) {
            $tableName = mb_substr($this->name, 0, -13);
        } else {
            $tableName = $this->name;
        }

        // we need to fetch the field translation...
        $query = "SELECT id FROM `cms_tbl_conf` WHERE `name` = '".MySqlLegacySupport::getInstance()->real_escape_string($tableName)."'";
        if ($tmp = MySqlLegacySupport::getInstance()->fetch_assoc(MySqlLegacySupport::getInstance()->query($query))) {
            $oTableConf = new TCMSTableConf();
            /* @var $oTableConf TCMSTableConf */
            $oTableConf->Load($tmp['id']);

            $oFields = $oTableConf->GetFieldDefinitions();
            while ($oField = $oFields->Next()) {
                $fieldTitleDecoded = $oField->sqlData['translation'];

                $fieldTitle = $fieldTitleDecoded;
                $this->options[$oField->sqlData['name']] = $fieldTitle;
            }
        }
    }
}
