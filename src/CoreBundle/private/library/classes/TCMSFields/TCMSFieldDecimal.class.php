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
 * a number (int).
 */
class TCMSFieldDecimal extends TCMSField implements DoctrineTransformableInterface
{
    /**
     * decimal points number.
     *
     * @var int
     */
    protected $numberOfDecimals = null;

    /**
     * view path for frontend.
     */
    protected $sViewPath = 'TCMSFields/views/TCMSFieldDecimal';

    public function getDoctrineDataModelParts(string $namespace, array $tableNamespaceMapping): DataModelParts
    {
        $parameters = [
            'source' => get_class($this),
            // doctrine handles decimals as string
            'type' => 'string',
            'docCommentType' => 'string',
            'description' => $this->oDefinition->sqlData['translation'],
            'propertyName' => $this->snakeToCamelCase($this->name),
            'defaultValue' => sprintf("'%s'", (string)$this->oDefinition->sqlData['field_default_value']),
            'allowDefaultValue' => true,
            'getterName' => 'get'. $this->snakeToPascalCase($this->name),
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
        $lengthData = $this->oDefinition->sqlData['length_set'];
        if ('' === $lengthData) {
            $lengthData = '10,2';
        }
        $lengthParts = explode(',', str_replace(' ', '',$lengthData));
        if (1 === count($lengthParts)) {
            $lengthData[1] = 0;
        }
        return $this->getDoctrineRenderer('mapping/decimal.xml.twig', [
            'fieldName' => $this->snakeToCamelCase($this->name),
            'column' => $this->name,
            'comment' => $this->oDefinition->sqlData['translation'],
            'precision' => $lengthData[0],
            'scale' => $lengthData[1],
            'default' => $this->oDefinition->sqlData['field_default_value'],
        ])->render();
    }


    public function GetHTML()
    {
        // number of decimals can be retrived from the length set of the field definition
        $sFormatValue = number_format($this->data, $this->_GetNumberOfDecimals(), ',', '.');

        $html = '<div class="row field-decimal"><div class="col-xs-12 col-md-6 col-lg-4">';
        
        $html .= sprintf('<input class="fieldnumber form-control form-control-sm" onblur="this.value=NumberFormat(NumberToFloat(this.value, \',\', \'.\'), %s, \',\', \'.\')" type="text" id="%s" name="%s" value="%s"',
            $this->numberOfDecimals,
            TGlobal::OutHTML($this->name),
            TGlobal::OutHTML($this->name),
            TGlobal::OutHTML($sFormatValue)
        );
        
        if ('' !== $this->fieldWidth && $this->fieldWidth > 0) {
            $html .= ' size="'.TGlobal::OutHTML($this->fieldWidth).'" maxlength="'.TGlobal::OutHTML($this->fieldWidth).'"';
        }

        $html .= ' />';
        $html .= '</div></div>';

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
        $sqlString = str_replace('.', '', $this->data);
        $sqlString = str_replace(',', '.', $sqlString);

        return $sqlString;
    }

    public function _GetNumberOfDecimals()
    {
        if (is_null($this->numberOfDecimals)) {
            $this->numberOfDecimals = 0;
            $lengthSet = $this->oDefinition->sqlData['length_set'];
            $tmp = explode(',', $lengthSet);
            if (is_array($tmp) && count($tmp) > 1) {
                $num = trim($tmp[1]);
                $this->numberOfDecimals = intval($num);
            }
        }

        return $this->numberOfDecimals;
    }

    /**
     * return an array of all js, css, or other header includes that are required
     * in the cms for this field. each include should be in one line, and they
     * should always be typed the same way so that no includes are included mor than once.
     *
     * @return array
     */
    public function GetCMSHtmlHeadIncludes()
    {
        $aIncludes = parent::GetCMSHtmlHeadIncludes();
        $aIncludes[] = '<script src="'.URL_CMS.'/javascript/NumberFormat.fun.js" type="text/JavaScript"></script>';

        return $aIncludes;
    }

    protected function GetFieldWriterData()
    {
        $aData = parent::GetFieldWriterData();
        $aData['sFieldType'] = 'double';

        return $aData;
    }

    public function RenderFieldPostLoadString()
    {
        $oViewParser = new TViewParser();
        $oViewParser->bShowTemplatePathAsHTMLHint = false;
        $aData = $this->GetFieldWriterData();
        $aData['numberOfDecimals'] = $this->_GetNumberOfDecimals();
        $oViewParser->AddVarArray($aData);

        return $oViewParser->RenderObjectView('postload', 'TCMSFields/TCMSFieldDecimal');
    }

    public function RenderFieldPropertyString()
    {
        $sNormalfield = parent::RenderFieldPropertyString();

        $oViewParser = new TViewParser();
        $oViewParser->bShowTemplatePathAsHTMLHint = false;
        $aData = $this->GetFieldWriterData();
        $aData['sFieldName'] = $aData['sFieldName'].'Formated';
        $aData['sFieldType'] = 'string';

        $oViewParser->AddVarArray($aData);

        $sNormalfield .= "\n".$oViewParser->RenderObjectView('property', 'TCMSFields/TCMSField');

        return $sNormalfield;
    }

    /**
     * renders a input field of type "hidden", used in readonly mode.
     *
     * @return string
     */
    public function _GetHiddenField()
    {
        $html = '<input type="hidden" name="'.TGlobal::OutHTML($this->name).'" id="'.TGlobal::OutHTML($this->name).'" value="'.TGlobal::OutHTML(number_format($this->data, $this->_GetNumberOfDecimals(), ',', '.')).'" />';

        return $html;
    }

    /**
     * renders the read only view of the field.
     *
     * @return string
     */
    public function GetReadOnly()
    {
        $html = $this->_GetHiddenField();
        $html .= '<div class="form-content-simple">'.TGlobal::OutHTML(number_format($this->data, $this->_GetNumberOfDecimals(), ',', '.')).'</div>';

        return $html;
    }

    /**
     * checks if field is mandatory and if field content is valid
     * overwrite this method to add your field based validation
     * you need to add a message to TCMSMessageManager for handling error messages
     * <code>
     * <?php
     *   $oMessageManager = TCMSMessageManager::GetInstance();
     *   $sConsumerName = TCMSTableEditorManager::MESSAGE_MANAGER_CONSUMER;
     *   $oMessageManager->AddMessage($sConsumerName,'TABLEEDITOR_FIELD_IS_MANDATORY');
     * ?>
     * </code>.
     *
     * @todo add regex with right decimal format check
     *
     * @return bool - returns false if field is mandatory and field content is empty or data is not valid
     */
    public function DataIsValid()
    {
        $bDataIsValid = parent::DataIsValid();
        if ($bDataIsValid) {
            if ($this->HasContent() && !is_numeric($this->ConvertPostDataToSQL())) {
                $bDataIsValid = false;
                $oMessageManager = TCMSMessageManager::GetInstance();
                $sConsumerName = TCMSTableEditorManager::MESSAGE_MANAGER_CONSUMER;
                $sFieldTitle = $this->oDefinition->GetName();
                $oMessageManager->AddMessage($sConsumerName, 'TABLEEDITOR_FIELD_DECIMAL_NOT_VALID', array('sFieldName' => $this->name, 'sFieldTitle' => $sFieldTitle));
            }
        }

        return $bDataIsValid;
    }

    /**
     * returns true if field data is not empty
     * overwrite this method for mlt and property fields.
     *
     * @return bool
     */
    public function HasContent()
    {
        $bHasContent = false;
        if (!empty($this->data) || '0' == $this->data) {
            $bHasContent = true;
        }

        return $bHasContent;
    }
}
