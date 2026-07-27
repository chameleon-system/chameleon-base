<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Algo26\IdnaConvert\ToIdn;

class TCMSPortalDomain extends TCMSRecord
{
    public function __construct($id = null, $iLanguage = null)
    {
        parent::__construct('cms_portal_domains', $id, $iLanguage);
    }

    /**
     * {@inheritdoc}
     */
    public function GetName()
    {
        $domainName = parent::GetName();

        return self::ConvertFromIDN($domainName);
    }

    /**
     * return the database domain name (either ssl or normal version, depending on protocol).
     *
     * @return string
     */
    public function GetActiveDomainName()
    {
        if (REQUEST_PROTOCOL === 'https' && '' !== trim($this->sqlData['sslname'])) {
            return $this->sqlData['sslname'];
        }

        return $this->sqlData['name'];
    }

    /**
     * @return string
     */
    public function getInsecureDomainName()
    {
        return $this->sqlData['name'];
    }

    /**
     * Returns the name of the configured secure domain ("SSL domain"). If no secure domain is configured, the insecure
     * domain name is returned.
     *
     * @return string
     */
    public function getSecureDomainName()
    {
        if ('' === $this->sqlData['sslname']) {
            return $this->sqlData['name'];
        }

        return $this->sqlData['sslname'];
    }

    public static function ConvertFromIDN($domain)
    {
        static $idnConverter;
        if (!$idnConverter) {
            $idnConverter = new ToUnicode();
        }

        return trim($idnConverter->convert($domain));
    }

    public static function ConvertToIDN($domain)
    {
        static $idnConverter;
        if (!$idnConverter) {
            $idnConverter = new ToIdn();
        }

        return trim($idnConverter->convert($domain));
    }

    public function GetDomainParts()
    {
        $aDomainParts = [];
        $sDomainName = $this->GetName();
        $lastDotPos = strrpos($sDomainName, '.');
        $topLevelDomain = substr($sDomainName, $lastDotPos);
        $domain = substr($sDomainName, 0, $lastDotPos);

        $aDomainParts['topLevelDomain'] = $topLevelDomain;
        $aDomainParts['domain'] = $domain;

        return $aDomainParts;
    }
}
