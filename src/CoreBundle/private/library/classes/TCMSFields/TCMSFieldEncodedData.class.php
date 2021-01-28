<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

class TCMSFieldEncodedData extends TCMSFieldBlob
{
    public function ConvertDataToFieldBasedData($sData)
    {
        $sData = parent::ConvertDataToFieldBasedData($sData);
        $sData = $this->EncodeData($sData);

        return $sData;
    }

    public function RenderFieldPostLoadString()
    {
        //first we do our decoding stuff!
        $oViewParser = new TViewParser();
        /** @var $oViewParser TViewParser */
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
                //decode data
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
                //decode data
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
