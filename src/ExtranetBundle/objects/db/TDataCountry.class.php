<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

class TDataCountry extends TDataCountryAutoParent
{
    public const ISO_3166_ALPHA2 = 'iso_code_2';
    public const ISO_3166_ALPHA3 = 'iso_code_3';

    /**
     * validates a zip code.
     *
     * @param string $sZipCode
     *
     * @return bool
     */
    public function IsValidPostalcode($sZipCode)
    {
        $bIsValid = true;
        $sPattern = trim($this->fieldPostalcodePattern);
        if (!empty($sPattern)) {
            $bIsValid = false;
            $sPattern = "/^{$sPattern}$/";
            if (preg_match($sPattern, $sZipCode)) {
                $bIsValid = true;
            }
        }

        return $bIsValid;
    }

    /**
     * return an instance for the given iso code (either 2 or 3 digits can be used).
     *
     * @param string $sIsoCode
     *
     * @return TdbDataCountry|null
     */
    public static function GetInstanceForIsoCode($sIsoCode)
    {
        $oCountry = null;
        $sQuery = 'SELECT `data_country`.*
                   FROM `data_country`
             INNER JOIN `t_country` ON `data_country`.`t_country_id` = `t_country`.`id`
                ';
        if (2 == strlen($sIsoCode)) {
            $sQuery .= " WHERE `t_country`.`iso_code_2` = '".MySqlLegacySupport::getInstance()->real_escape_string($sIsoCode)."'";
        } else {
            $sQuery .= " WHERE `t_country`.`iso_code_3` = '".MySqlLegacySupport::getInstance()->real_escape_string($sIsoCode)."'";
        }
        if ($aData = MySqlLegacySupport::getInstance()->fetch_assoc(MySqlLegacySupport::getInstance()->query($sQuery))) {
            $oCountry = TdbDataCountry::GetNewInstance();
            $oCountry->LoadFromRow($aData);
        }

        return $oCountry;
    }

    /**
     * returns the iso 3166 2 or 3 char code of the country.
     *
     * @param string $sIsoCodeType
     *
     * @return string|null
     */
    public function getIso3166CountryCode($sIsoCodeType = self::ISO_3166_ALPHA2)
    {
        $sIsoCode2 = null;
        $oCountryObject = $this->GetFieldTCountry();
        if ($oCountryObject) {
            $sIsoCode2 = trim(strtoupper($oCountryObject->sqlData[$sIsoCodeType]));
        }

        return $sIsoCode2;
    }

    /**
     * @return string|null
     */
    public function getTopLevelDomain()
    {
        $sTopLevelDomain = null;
        $oCountryObject = $this->GetFieldTCountry();
        if ($oCountryObject && true === isset($oCountryObject->sqlData['toplevel_domain'])) {
            $sTopLevelDomain = trim(strtoupper($oCountryObject->sqlData['toplevel_domain']));
        }

        return $sTopLevelDomain;
    }

    /**
     * @return string|null
     */
    public function getInternationalDialingCode()
    {
        $sDialingCode = null;
        $oCountryObject = $this->GetFieldTCountry();
        if ($oCountryObject && true === isset($oCountryObject->sqlData['international_dialling_code'])) {
            $sDialingCode = trim(strtoupper($oCountryObject->sqlData['international_dialling_code']));
        }

        return $sDialingCode;
    }

    /**
     * @return string|null
     */
    public function getGermanPostalCode()
    {
        $sPostalCode = null;
        $oCountryObject = $this->GetFieldTCountry();
        if ($oCountryObject && true === isset($oCountryObject->sqlData['german_postalcode'])) {
            $sPostalCode = trim(strtoupper($oCountryObject->sqlData['german_postalcode']));
        }

        return $sPostalCode;
    }

    /**
     * @return bool
     */
    public function isEUMember()
    {
        $bIsEUMember = false;
        $oCountryObject = $this->GetFieldTCountry();
        if ($oCountryObject && true === isset($oCountryObject->sqlData['eu_member'])) {
            $bIsEUMember = ('1' == $oCountryObject->sqlData['eu_member']);
        }

        return $bIsEUMember;
    }

    /**
     * @return bool
     */
    public function isActive()
    {
        return true == $this->fieldActive;
    }
}
