<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * The class takes data and serializes it to db.
 *
/**/
class TCMSFieldBlob extends TCMSFieldText
{
    public function ConvertDataToFieldBasedData($sData)
    {
        $sData = parent::ConvertDataToFieldBasedData($sData);
        $sData = serialize($sData);

        return $sData;
    }

    /**
     * this method converts post data like datetime (3 fields with date, hours, minutes in human readable format)
     * to sql format.
     *
     * @return mixed
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
        /** @var $oViewParser TViewParser */
        $oViewParser->bShowTemplatePathAsHTMLHint = false;
        $aData = $this->GetFieldWriterData();
        $oViewParser->AddVarArray($aData);

        return $oViewParser->RenderObjectView('postload', 'TCMSFields/TCMSFieldBlob');
    }

    public function GetReadOnly()
    {
        $sReturnVal = '';
        if (is_string($this->data)) {
            $html = parent::GetReadOnly();
        } else {
            $html = TGlobal::OutHTML(print_r($this->data, true));
        }

        return '<pre style="white-space:pre">'.$html.'</pre>';
    }
}
