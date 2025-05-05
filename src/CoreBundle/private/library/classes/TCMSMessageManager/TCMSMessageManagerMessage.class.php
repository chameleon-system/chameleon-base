<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

class TCMSMessageManagerMessage extends TAdbCmsMessageManagerMessage
{
    public const VIEW_PATH = 'TCMSMessageManagerMessage';

    /**
     * optional variables that will be replaced in the message.
     *
     * @var array
     */
    protected $aMessageParameters = [];

    /**
     * render the message.
     *
     * @param string $sViewName - the view to use
     * @param string $sViewType - where the view is located (Core, Custom-Core, Customer)
     * @param array $aCallTimeVars - place any custom vars that you want to pass through the call here
     *
     * @return string
     */
    public function Render($sViewName = null, $sViewType = null, $aCallTimeVars = [])
    {
        $oView = new TViewParser();

        $sMessage = $this->GetMessageString();
        $oMessageType = TdbCmsMessageManagerMessageType::GetNewInstance();
        /** @var $oMessageType TdbCmsMessageManagerMessageType */
        if (!$oMessageType->Load($this->fieldCmsMessageManagerMessageTypeId)) {
            $oMessageType = null;
        }

        // add view variables
        $oView->AddVar('oMessageObject', $this);
        $oView->AddVar('oMessageType', $oMessageType);
        $oView->AddVar('sMessageString', $sMessage);
        $oView->AddVar('aCallTimeVars', $aCallTimeVars);

        // view and viewtype
        if (!isset($sViewType) || is_null($sViewType) || empty($sViewType)) {
            if (!empty($this->fieldMessageLocationType)) {
                $sViewType = $this->fieldMessageLocationType;
            }
        }
        if (!isset($sViewName) || is_null($sViewName) || empty($sViewName)) {
            if (!empty($this->fieldMessageView)) {
                $sViewName = $this->fieldMessageView;
            }
        }

        $aOtherParameters = $this->GetAdditionalViewVariables($sViewName, $sViewType);
        $oView->AddVarArray($aOtherParameters);
        $sHTML = $oView->RenderObjectView($sViewName, self::VIEW_PATH, $sViewType);

        if (_DEVELOPMENT_MODE) {
            $sHTML = "<!-- MESSAGE CODE: {$this->fieldName} -->".$sHTML;
        }

        return $sHTML;
    }

    /**
     * use this method to add any variables to the render method that you may
     * require for some view.
     *
     * @param string $sViewName - the view being requested
     * @param string $sViewType - the location of the view (Core, Custom-Core, Customer)
     *
     * @return array
     */
    protected function GetAdditionalViewVariables($sViewName, $sViewType)
    {
        $aViewVariables = [];

        return $aViewVariables;
    }

    /**
     * return message string.
     *
     * @return string
     */
    public function GetMessageString()
    {
        $sMessage = $this->fieldMessage;
        $sMessage = nl2br($sMessage);
        $matchString = '/\[\{(.*?)(:(string|number|date))*(:(.*?))*\}\]/si';
        $sMessage = preg_replace_callback($matchString, [$this, 'InsertVariablesIntoMessageString'], $sMessage);

        return $sMessage;
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
        $oLocal = TCMSLocal::GetActive();
        $return = $aMatches[0];
        $var = $aMatches[1];
        if (array_key_exists($var, $this->aMessageParameters)) {
            if (count($aMatches) > 2) {
                $type = $aMatches[3];
            } else {
                $type = 'string';
            }
            $modifier = null;
            if (6 == count($aMatches)) {
                $modifier = $aMatches[5];
            }
            switch ($type) {
                case 'date':
                    $return = $oLocal->FormatDate($this->aMessageParameters[$var]);
                    break;
                case 'number':
                    if (is_null($modifier)) {
                        $modifier = 0;
                    }
                    $return = $oLocal->FormatNumber($this->aMessageParameters[$var], $modifier);
                    break;
                case 'string':
                default:
                    $return = $this->aMessageParameters[$var];
                    break;
            }
        }

        return $return;
    }

    /**
     * an assoc array with parameters to be placed into the message.
     *
     * @param array $aParameters
     */
    public function SetMessageParameters($aParameters)
    {
        $this->aMessageParameters = $aParameters;
    }

    /**
     * returns an assoc array with parameters that are placed into the message.
     *
     * @return array
     */
    public function GetMessageParameters()
    {
        return $this->aMessageParameters;
    }

    public function __sleep()
    {
        return ['table', 'id', 'iLanguageId', 'aMessageParameters'];
    }

    public function __wakeup()
    {
        $this->Load($this->id);
    }
}
