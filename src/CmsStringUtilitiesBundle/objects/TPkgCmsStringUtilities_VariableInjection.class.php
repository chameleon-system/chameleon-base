<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

class TPkgCmsStringUtilities_VariableInjection implements IPkgCmsStringUtilities_VariableInjection
{
    /**
     * used for some methods to pass internal data in callback methods.
     *
     * @var array
     */
    private $aInternalDataCache;

    /**
     * replaces custom var or cms text blocks in the text
     * These variables in the text must have the following format: [{name:format}]
     * "format" ist either string, date, or number. It is possible to specify the number of decimals
     * used when formating a number: [{variable:number:decimalplaces}]
     * example [{costs:number:2}].
     *
     * {@inheritDoc}
     */
    public function replace($sString, $aCustomVariables, $bPassVarsThroughOutHTML = false, $iWidth = false)
    {
        if ('' === $sString) {
            return $sString;
        }

        if (false === strpos($sString, '[{')) {
            return $sString;
        }
        $matchString = '/\[\{(.*?)\}\]/si';
        if (!empty($sString) && preg_match($matchString, $sString)) {
            if (is_array($aCustomVariables) && count($aCustomVariables) > 0) {
                $this->aInternalDataCache = [];
                $this->aInternalDataCache = $aCustomVariables;
                $this->aInternalDataCache['_FIELDS'] = implode("<br />\n", array_keys($aCustomVariables));
                $this->aInternalDataCache['iTextReplaceWidth'] = $iWidth;
                if ($bPassVarsThroughOutHTML) {
                    $this->aInternalDataCache['__bPassVarsThroughOutHTML'] = true;
                } else {
                    $this->aInternalDataCache['__bPassVarsThroughOutHTML'] = false;
                }

                $sString = preg_replace_callback($matchString, [$this, 'InsertVariablesIntoMessageString'], $sString);
                $this->aInternalDataCache = null;
            }
        }

        return $sString;
    }

    /**
     * method called by the regex to replace the variables in the message string.
     *
     * @param array $aMatches
     *
     * @return string
     */
    protected function InsertVariablesIntoMessageString($aMatches)
    {
        $return = $aMatches[0];
        $aParts = explode(':', $aMatches[1]);
        $var = $aParts[0];
        if (true == array_key_exists($var, $this->aInternalDataCache)) {
            if (count($aParts) > 1) {
                $type = $aParts[1];
            } else {
                $type = 'string';
            }
            $modifier = null;
            if (count($aParts) > 2) {
                $modifier = $aParts[2];
            }
            switch ($type) {
                case 'lookup':
                    $sTarget = $aParts[2];
                    $sTargetField = 'id';
                    if (false !== strpos($sTarget, ',')) {
                        $aParts = explode(',', $sTarget);
                        $sTargetTable = $aParts[0];
                        $sTargetField = $aParts[1];
                    } else {
                        $sTargetTable = $sTarget;
                    }
                    $return = '';
                    $sClassName = TCMSTableToClass::GetClassName('Tdb', $sTargetTable);
                    /**
                     * @var TCMSRecord $oObj
                     */
                    $oObj = new $sClassName();
                    if (true == $oObj->LoadFromField($sTargetField, $this->aInternalDataCache[$var])) {
                        $return = $oObj->GetName();
                    }
                    break;
                case 'date':
                    $return = '';
                    if (false === empty($this->aInternalDataCache[$var])) {
                        $return = TCMSLocal::GetActive()->FormatDate($this->aInternalDataCache[$var]);
                    }
                    break;
                case 'number':
                    if (is_null($modifier)) {
                        $modifier = 0;
                    }
                    $return = '';
                    if (is_numeric($this->aInternalDataCache[$var])) {
                        /** @var int $modifier */
                        $return = TCMSLocal::GetActive()->FormatNumber($this->aInternalDataCache[$var], $modifier);
                    }
                    break;
                case 'string':
                default:
                    $return = '';
                    $allowedTypes = ['boolean', 'integer', 'double', 'string'];
                    if (in_array(gettype($this->aInternalDataCache[$var]), $allowedTypes)) {
                        $return = $this->aInternalDataCache[$var];
                    }
                    break;
            }
            if ($this->aInternalDataCache['__bPassVarsThroughOutHTML']) {
                // protect output
                $return = TGlobal::OutHTML($return);
            }
        }

        return $return;
    }
}
