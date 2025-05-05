<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

class TDataExtranetUserAddress extends TDataExtranetUserAddressAutoParent
{
    public const VIEW_PATH = 'pkgExtranet/views/db/TDataExtranetUserAddress';
    public const MSG_FORM_FIELD = 'tdataextranetuseraddressform';
    public const FORM_DATA_NAME_BILLING = 'aUserAddressBilling';
    public const FORM_DATA_NAME_SHIPPING = 'aUserAddressShipping';

    /**
     * salutation.
     *
     * @var TdbDataExtranetSalutation
     */
    protected $oSalutation;

    /**
     * country.
     *
     * @var TdbDataCountry
     */
    protected $oCountry;

    /**
     * returns salutation.
     *
     * @param bool $refresh
     *
     * @return TdbDataExtranetSalutation|null
     */
    public function GetSalutation($refresh = false)
    {
        if (is_null($this->oSalutation) || $refresh) {
            $this->oSalutation = TdbDataExtranetSalutation::GetNewInstance();
            if (!$this->oSalutation->Load($this->fieldDataExtranetSalutationId)) {
                $this->oSalutation = null;
            }
        }

        return $this->oSalutation;
    }

    /**
     * returns the country.
     *
     * @param bool $refresh
     *
     * @return TdbDataCountry|null
     */
    public function GetCountry($refresh = false)
    {
        if (is_null($this->oCountry) || $refresh) {
            $this->oCountry = TdbDataCountry::GetNewInstance();
            if (!$this->oCountry->Load($this->fieldDataCountryId)) {
                $this->oCountry = null;
            }
        }

        return $this->oCountry;
    }

    /**
     * tries to fetch post data (shipping or billing post data).
     *
     * @return array
     */
    protected function GetPostData()
    {
        $oGlobal = TGlobal::instance();
        $oUser = TdbDataExtranetUser::GetInstance();
        $aPostData = [];

        if ($oGlobal->UserDataExists(self::FORM_DATA_NAME_BILLING) || $oGlobal->UserDataExists(self::FORM_DATA_NAME_SHIPPING)) {
            if ($this->id == $oUser->fieldDefaultBillingAddressId && $oGlobal->UserDataExists(self::FORM_DATA_NAME_BILLING)) {
                $aPostData = $oGlobal->GetUserData(self::FORM_DATA_NAME_BILLING);
            } elseif ($oGlobal->UserDataExists(self::FORM_DATA_NAME_SHIPPING)) {
                $aPostData = $oGlobal->GetUserData(self::FORM_DATA_NAME_SHIPPING);
            }
        }

        return $aPostData;
    }

    /**
     * used to display the user (including edit forms for user data).
     *
     * @param string $sViewName - the view to use
     * @param string $sViewType - where the view is located (Core, Custom-Core, Customer)
     * @param array $aCallTimeVars - place any custom vars that you want to pass through the call here
     * @param bool $bAutoLoadPostData - this is deprecated and should default to false in later versions
     *
     * @return string
     */
    public function Render($sViewName = 'standard', $sViewType = 'Core', $aCallTimeVars = [], $bAutoLoadPostData = false)
    {
        $sHTML = '';
        $oView = new TViewParser();

        // add view variables
        $oExtranetConfig = TdbDataExtranet::GetInstance();
        $oView->AddVar('oUserAddress', $this);
        $oView->AddVar('oExtranetConfig', $oExtranetConfig);
        $oView->AddVar('aCallTimeVars', $aCallTimeVars);
        $aOtherParameters = $this->GetAdditionalViewVariables($sViewName, $sViewType);
        $oView->AddVarArray($aOtherParameters);

        $aPostData = $this->GetPostData();
        // get post data
        if ($bAutoLoadPostData && is_array($aPostData) && count($aPostData) > 0) {
            // copy original values
            $aOriginalSqlData = $this->sqlData;
            // overwrite original data with post data and reload the object
            $this->LoadFromRow(array_merge($aOriginalSqlData, $aPostData));
            $sHTML = $oView->RenderObjectPackageView($sViewName, self::VIEW_PATH, $sViewType);
            // reset to original values
            $this->LoadFromRow($aOriginalSqlData);
        } else {
            $sHTML = $oView->RenderObjectPackageView($sViewName, self::VIEW_PATH, $sViewType);
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
     * returns false if the current address does not contain data.
     *
     * @return bool
     */
    public function ContainsData()
    {
        $bContainsData = false;

        if (false === $this->sqlData) {
            return false;
        }

        reset($this->sqlData);
        foreach ($this->sqlData as $sKey => $sValue) {
            $sValue = trim($sValue);
            if ('data_extranet_user_id' != $sKey && !empty($sValue)) {
                $bContainsData = true;
                break;
            }
        }

        return $bContainsData;
    }

    /**
     * validates the address data.
     *
     * @param string $sFormDataName - the array name used for the form. send error messages here. if "" dont add messages
     *
     * @return bool
     */
    public function ValidateData($sFormDataName)
    {
        $bIsValid = true;
        $oMsgManager = TCMSMessageManager::GetInstance();

        $aRequiredFields = $this->GetRequiredFields();
        foreach ($aRequiredFields as $sFieldName) {
            $bFieldValid = true;
            if (false !== $this->sqlData) {
                if (!array_key_exists($sFieldName, $this->sqlData)) {
                    $bFieldValid = false;
                } else {
                    $this->sqlData[$sFieldName] = trim($this->sqlData[$sFieldName]);
                }
                if ($bFieldValid && empty($this->sqlData[$sFieldName])) {
                    $bFieldValid = false;
                }
            }
            if (!$bFieldValid) {
                if ('' != $sFormDataName) {
                    $oMsgManager->AddMessage($sFormDataName.'-'.$sFieldName, 'ERROR-USER-REQUIRED-FIELD-MISSING');
                }
                $bIsValid = false;
            }
        }

        if (false !== $this->sqlData) {
            // check postal code for country
            $bHasCountry = (array_key_exists('data_country_id', $this->sqlData) && !empty($this->sqlData['data_country_id']));
            $bHasPostalcode = (array_key_exists('postalcode', $this->sqlData) && !empty($this->sqlData['postalcode']));
            if ($bHasCountry && $bHasPostalcode) {
                $oCountry = TdbDataCountry::GetNewInstance();
                if ($oCountry->Load($this->sqlData['data_country_id'])) {
                    if (!$oCountry->IsValidPostalcode($this->sqlData['postalcode'])) {
                        if ('' != $sFormDataName) {
                            $oMsgManager->AddMessage($sFormDataName.'-postalcode', 'ERROR-USER-FIELD-INVALID-POSTALCODE');
                        }
                        $bIsValid = false;
                    }
                }
            }

            if (array_key_exists('vat_id', $this->sqlData) && !empty($this->sqlData['vat_id'])) {
                if (false == TTools::IsVatIdValid($this->sqlData['vat_id'], null, $this->sqlData['data_country_id'])) {
                    if ('' != $sFormDataName) {
                        $oMsgManager->AddMessage($sFormDataName.'-vat_id', 'ERROR-USER-VAT-ID-INVALID');
                    }
                    $bIsValid = false;
                }
            }

            // validate field length for all char fields
            $sQuery = "SELECT `cms_field_conf`.*
                     FROM `cms_field_conf`
               INNER JOIN `cms_tbl_conf` ON `cms_field_conf`.`cms_tbl_conf_id` = `cms_tbl_conf`.`id`
               INNER JOIN `cms_field_type` on `cms_field_conf`.`cms_field_type_id` = `cms_field_type`.`id`
                    WHERE `cms_tbl_conf`.`name` = '".MySqlLegacySupport::getInstance()->real_escape_string($this->table)."'
                      AND `cms_field_type`.`constname` = 'CMSFIELD_STRING'";
            $oFields = TdbCmsFieldConfList::GetList($sQuery);
            while ($oField = $oFields->Next()) {
                if (isset($this->sqlData[$oField->fieldName])) {
                    $iLength = $oField->fieldLengthSet;
                    if (empty($iLength)) {
                        $iLength = 255;
                    } // default is 255 chars
                    if (mb_strlen($this->sqlData[$oField->fieldName]) > $iLength) {
                        if ('' != $sFormDataName) {
                            $oMsgManager->AddMessage($sFormDataName.'-'.$oField->fieldName, 'ERROR-USER-FIELD-TO-LONG', ['iLengthAllowed' => $iLength, 'iUserLength' => mb_strlen($this->sqlData[$oField->fieldName])]);
                        }
                        $bIsValid = false;
                    }
                }
            }
        }

        return $bIsValid;
    }

    /**
     * loads data from row. will not overwrite fields in the object that are not passed via
     * aRow or that are in the protected list.
     *
     * @param array $aRow
     *
     * @return void
     */
    public function LoadFromRowProtected($aRow)
    {
        $aData = $this->sqlData;
        $aProtected = ['id', 'data_extranet_user_id'];
        foreach ($aRow as $sFieldName => $sValue) {
            if (!in_array($sFieldName, $aProtected)) {
                $aData[$sFieldName] = $sValue;
            }
        }
        $oUser = TdbDataExtranetUser::GetInstance();
        if (null !== $oUser) {
            $aData['data_extranet_user_id'] = $oUser->id;
        }
        $this->LoadFromRow($aData);
    }
}
