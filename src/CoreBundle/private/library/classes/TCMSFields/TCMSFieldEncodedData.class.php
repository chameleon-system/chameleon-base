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

class TCMSFieldEncodedData extends TCMSFieldBlob implements DoctrineTransformableInterface
{
    // doctrine itself does not recommend using the database for encryption/decryption https://github.com/doctrine/orm/issues/1744
    // for now, we assume that the encoding / decoding happens outside of doctrine
    public function getDoctrineDataModelParts(string $namespace, array $tableNamespaceMapping): DataModelParts
    {
        $parameters = [
            'source' => get_class($this),
            'type' => '?string',
            'docCommentType' => 'string|null',
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
            'type' => 'blob',
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
        $sData = $this->EncodeData($sData);

        return $sData;
    }

    public function RenderFieldPostLoadString()
    {
        // first we do our decoding stuff!
        $oViewParser = new TViewParser();
        /* @var $oViewParser TViewParser */
        $oViewParser->bShowTemplatePathAsHTMLHint = false;
        $aData = $this->GetFieldWriterData();
        $oViewParser->AddVarArray($aData);

        $oRet = parent::RenderFieldPostLoadString();
        $oRet .= $oViewParser->RenderObjectView('postload', 'TCMSFields/TCMSFieldEncodedData');

        return $oRet;
    }

    /**
     * Encode data.
     *
     * @return bool
     */
    protected function DecodeData($sData)
    {
        if (defined('CMSFIELD_DATA_ENCODING_KEY')) {
            $sKey = CMSFIELD_DATA_ENCODING_KEY;
            if (!empty($sKey)) {
                $sKey = str_rot13($sKey);
                // decode data
                $sQry = "SELECT DECODE('".MySqlLegacySupport::getInstance()->real_escape_string($sData)."','".MySqlLegacySupport::getInstance()->real_escape_string($sKey)."') AS decoded_value ";
                $aDecodedData = MySqlLegacySupport::getInstance()->fetch_assoc(MySqlLegacySupport::getInstance()->query($sQry));
                $sData = $aDecodedData['decoded_value'];
            }
        }

        return $sData;
    }

    /**
     * Encode data.
     *
     * @return bool
     */
    protected function EncodeData($sData)
    {
        if (defined('CMSFIELD_DATA_ENCODING_KEY')) {
            $sKey = CMSFIELD_DATA_ENCODING_KEY;
            if (!empty($sKey)) {
                $sKey = str_rot13($sKey);
                // decode data
                $sQry = "SELECT ENCODE('".MySqlLegacySupport::getInstance()->real_escape_string($sData)."','".MySqlLegacySupport::getInstance()->real_escape_string($sKey)."') AS encoded_value ";
                $aDecodedData = MySqlLegacySupport::getInstance()->fetch_assoc(MySqlLegacySupport::getInstance()->query($sQry));
                $sData = $aDecodedData['encoded_value'];
            }
        }

        return $sData;
    }

    public function GetHTMLX()
    {
        $sOriginal = $this->data;
        $this->data = $this->DecodeData($this->data);
        $sResponse = parent::GetHTML();
        $this->data = $sOriginal;

        return $sResponse;
    }
}
