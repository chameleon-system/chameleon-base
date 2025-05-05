<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use ChameleonSystem\AutoclassesBundle\TableConfExport\DoctrineNotTransformableMarkerInterface;

/**
 * this field type loads a class, that is set via field config params
 * (sFieldDBObject, sFieldDBObjectType, sFieldDBObjectSubType) and prints an
 * input field for every public variable of the referenced class
 * the values are stored as key=val pairs (one per line).
 *
 * /**/
class TCMSFieldClassParams extends TCMSField implements DoctrineNotTransformableMarkerInterface
{
    // not in the cms_field_type table
    public function GetHTML()
    {
        parent::GetHTML();

        $html = '<textarea id="'.TGlobal::OutHTML($this->name).'" name="'.TGlobal::OutHTML($this->name)."\" class=\"form-control form-control-sm\" style=\"width: {$this->fieldCSSwidth}\" width=\"{$this->fieldWidth}\">";
        $html .= TGlobal::OutHTML($this->data);
        $html .= '</textarea>';

        // get current parameter data
        $aParams = [];
        $aParamRows = explode("\n", $this->data);
        foreach ($aParamRows as $sParamRow) {
            if (!empty($sParamRow)) {
                $aParamRow = explode('=', $sParamRow);
                if (array_key_exists(1, $aParamRow)) {
                    $value = trim($aParamRow[1]);
                } else {
                    $value = '';
                }
                $aParams[trim($aParamRow[0])] = $value;
            }
        }

        // default fieldnames to look for class location parameters
        $sFieldDBObject = 'dbobject_class';
        $sFieldDBObjectType = 'dbobject_type';
        $sFieldDBObjectSubType = 'dbobject_subtype';

        // fetch fieldnames from field config
        $sTmpFieldDBObject = $this->oDefinition->GetFieldtypeConfigKey('sFieldDBObject');
        if (!empty($sTmpFieldDBObject)) {
            $sFieldDBObject = $sTmpFieldDBObject;
        }

        $sTmpFieldDBObjectType = $this->oDefinition->GetFieldtypeConfigKey('sFieldDBObjectType');
        if (!empty($sTmpFieldDBObjectType)) {
            $sFieldDBObjectType = $sTmpFieldDBObjectType;
        }

        $sTmpFieldDBObjectSubType = $this->oDefinition->GetFieldtypeConfigKey('sFieldDBObjectSubType');
        if (!empty($sTmpFieldDBObjectSubType)) {
            $sFieldDBObjectSubType = $sTmpFieldDBObjectSubType;
        }

        // fetch field values
        $sSubType = '';
        if (array_key_exists($sFieldDBObjectSubType, $this->oTableRow->sqlData)) {
            $sSubType = $this->oTableRow->sqlData[$sFieldDBObjectSubType];
        }
        $sType = '';
        if (array_key_exists($sFieldDBObjectType, $this->oTableRow->sqlData)) {
            $sType = $this->oTableRow->sqlData[$sFieldDBObjectType];
        }
        $sClass = '';
        if (array_key_exists($sFieldDBObject, $this->oTableRow->sqlData)) {
            $sClass = $this->oTableRow->sqlData[$sFieldDBObject];
        }

        // load the class
        if (!empty($sSubType) && !empty($sType)) {
            $path = TGlobal::_GetClassRootPath($sSubType, $sType);
            $classPath = $path.'/'.$sSubType.'/'.$sClass.'.class.php';
            if (file_exists($classPath)) {
                // get all public variables
                $class_vars = get_class_vars($sClass);

                $filterArray = ['table', 'sqlData', 'id', '_oTableConf'];

                // filter default TCMSRecord parameters
                $aFilteredVars = [];
                foreach ($class_vars as $key => $val) {
                    if ('field' != substr($key, 0, 5) && !in_array($key, $filterArray)) {
                        $aFilteredVars[$key] = $val;
                    }
                }

                if (count($aFilteredVars) > 0) {
                    $html = '';
                    foreach ($aFilteredVars as $key => $val) {
                        $value = $val; // default value from class
                        if (array_key_exists($key, $aParams)) {
                            $value = $aParams[$key];
                        } // current value from db
                        $html .= '<div style="padding-bottom: 5px;"><div style="padding-bottom: 5px;"><strong>'.$key.":</strong></div>
              <input type=\"text\" style=\"width: {$this->fieldCSSwidth}\" size=\"{$this->fieldWidth}\" maxlength=\"{$this->fieldWidth}\" name=\"".$this->name.'['.$key.']" value="'.TGlobal::OutHTML($value).'" /></div>';
                    }
                }
            }
        }

        return $html;
    }

    /**
     * this method converts post data like datetime (3 fields with date, hours, minutes in human readable format)
     * to sql format.
     */
    public function ConvertPostDataToSQL()
    {
        $sJoinedParams = '';
        if (is_array($this->data)) {
            foreach ($this->data as $key => $val) {
                $sJoinedParams .= $key.'='.trim($val)."\n";
            }
        }

        return $sJoinedParams;
    }
}
