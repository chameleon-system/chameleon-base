<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use ChameleonSystem\CoreBundle\Service\PortalDomainServiceInterface;

class TCMSPortalDomain extends TCMSRecord
{
    public function __construct($id = null, $iLanguage = null)
    {
        parent::TCMSRecord('cms_portal_domains', $id, $iLanguage);
    }

    /**
     * {@inheritdoc}
     */
    public function GetName()
    {
        $sName = parent::GetName();
        $sName = self::ConvertFromIDN($sName);

        return $sName;
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

    public static function ConvertFromIDN($sString)
    {
        static $IDN;
        if (!$IDN) {
            $IDN = new idna_convert();
        }

        return trim($IDN->decode(($sString), 'utf8'));
    }

    public static function ConvertToIDN($sString)
    {
        static $IDN;
        if (!$IDN) {
            $IDN = new idna_convert();
        }

        return trim($IDN->encode($sString, 'utf8'));
    }

    public function GetDomainParts()
    {
        $aDomainParts = array();
        $sDomainName = $this->GetName();
        $lastDotPos = strrpos($sDomainName, '.');
        $topLevelDomain = substr($sDomainName, $lastDotPos);
        $domain = substr($sDomainName, 0, $lastDotPos);

        $aDomainParts['topLevelDomain'] = $topLevelDomain;
        $aDomainParts['domain'] = $domain;

        return $aDomainParts;
    }

    /**
     * returns the current domain.
     *
     * @return TCMSPortalDomain
     *
     * @deprecated since 6.2.0 - use chameleon_system_core.portal_domain_service::getActiveDomain() instead.
     */
    public static function &GetActiveDomain()
    {
        $domain = self::getStaticPortalDomainService()->getActiveDomain();

        return $domain;
    }

    /**
     * @return PortalDomainServiceInterface
     */
    private static function getStaticPortalDomainService()
    {
        return \ChameleonSystem\CoreBundle\ServiceLocator::get('chameleon_system_core.portal_domain_service');
    }
}
