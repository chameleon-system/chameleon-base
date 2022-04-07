<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use ChameleonSystem\CoreBundle\Service\ActivePageServiceInterface;
use ChameleonSystem\CoreBundle\Service\PortalDomainServiceInterface;
use ChameleonSystem\CoreBundle\ServiceLocator;
use ChameleonSystem\CoreBundle\Util\UrlUtil;

class TDataExtranetCore extends TDataExtranetCoreAutoParent
{
    /**
     * set to true, if the content in internal cache has changed.
     *
     * @var bool
     */
    protected $bInternalCacheMarkedAsDirty = true;

    /**
     * @deprecated - This was only used by the forgot password email which does not include login name anymore.
     */
    const URL_PARAMETER_LOGINNAME = 'loginname';
    const URL_PARAMETER_CHANGE_PASSWORD = 'pwchangekey';

    /**
     * Fetches the instance for the current portal.
     * If no active extranet config exists, it will return the first available config, or false if no config exists.
     *
     * @return TdbDataExtranet|bool
     */
    public static function &GetInstance()
    {
        static $oInstance;
        $oActivePage = self::getActivePageService()->getActivePage();
        if (!$oInstance || ($oActivePage && $oInstance->fieldCmsPortalId != self::getMyPortalDomainService()->getActivePortal()->id)) {
            if ($oActivePage) {
                $oPortal = $oActivePage->GetPortal();
                $oInstance = TdbDataExtranet::GetNewInstance();
                $oInstance->SetLanguage(self::getLanguageService()->getActiveLanguageId());
                $oInstance->LoadFromField('cms_portal_id', $oPortal->id);
            } else {
                // just fetch the first config we can get
                $oConfig = TdbDataExtranetList::GetList();
                $oConfig->GoToStart();
                $oInstance = $oConfig->Current();
            }
        }

        return $oInstance;
    }

    /**
     * @param bool $bForcePortal
     *
     * @return string|null
     */
    public function GetLinkLoginPage($bForcePortal = false)
    {
        return $this->GetLinkForNode($this->fieldNodeLoginId, $bForcePortal);
    }

    /**
     * @param bool $bForcePortal
     *
     * @return string|null
     */
    public function GetLinkRegisterPage($bForcePortal = false)
    {
        return $this->GetLinkForNode($this->fieldNodeRegisterId, $bForcePortal);
    }

    /**
     * @param bool $bForcePortal
     *
     * @return string|null
     */
    public function GetLinkRegisterSuccessPage($bForcePortal = false)
    {
        return $this->GetLinkForNode($this->fieldNodeRegistrationSuccessId, $bForcePortal);
    }

    /**
     * @param bool $bForcePortal
     *
     * @return string|null
     */
    public function GetLinkForgotPasswordPage($bForcePortal = false)
    {
        return $this->GetLinkForNode($this->fieldForgotPasswordTreenodeId, $bForcePortal);
    }

    /**
     * @param bool $bForcePortal
     *
     * @return string|null
     */
    public function GetLinkAccessDeniedPage($bForcePortal = false)
    {
        return $this->GetLinkForNode($this->fieldAccessRefusedNodeId, $bForcePortal);
    }

    /**
     * @param bool $bForcePortal
     *
     * @return string|null
     */
    public function GetLinkGroupRightDeniedPage($bForcePortal = false)
    {
        return $this->GetLinkForNode($this->fieldGroupRightDeniedNodeId, $bForcePortal);
    }

    /**
     * @param bool $bForcePortal
     *
     * @return string|null
     */
    public function GetLinkLogoutPage($bForcePortal = false)
    {
        return $this->GetLinkForNode($this->fieldLogoutSuccessNodeId, $bForcePortal);
    }

    /**
     * @param bool $bForcePortal
     *
     * @return string|null
     */
    public function GetLinkConfirmRegistrationPage($bForcePortal = false)
    {
        return $this->GetLinkForNode($this->fieldNodeConfirmRegistration, $bForcePortal);
    }

    /**
     * @param string $sSpotName
     *
     * @return string
     */
    public function GetLinkLogout($sSpotName)
    {
        $aAdditionalParams = array('module_fnc['.$sSpotName.']' => 'Logout');

        return self::getActivePageService()->getActivePage()->GetRealURLPlain($aAdditionalParams);
    }

    /**
     * @param bool $bForcePortal
     *
     * @return string|null
     */
    public function GetLinkMyAccountPage($bForcePortal = false)
    {
        $field = ('' !== $this->fieldNodeMyAccountCmsTreeId) ? $this->fieldNodeMyAccountCmsTreeId : $this->fieldLoginSuccessNodeId;

        return $this->GetLinkForNode($field, $bForcePortal);
    }

    /**
     * return link to node id.
     *
     * @param int  $iNodeId
     * @param bool $bForcePortal - include domain?
     *
     * @return string|null
     */
    protected function GetLinkForNode($iNodeId, $bForcePortal = false)
    {
        $sKey = 'nodeLink'.$iNodeId;
        if ($bForcePortal) {
            $sKey .= '-withportal';
        }

        /** @var string|null $sLink */
        $sLink = $this->GetFromInternalCache($sKey);

        if (null === $sLink) {
            $oNode = self::getTreeService()->getById($iNodeId);
            if (null !== $oNode) {
                if ($bForcePortal) {
                    $sLink = static::getTreeService()->getLinkToPageForTreeAbsolute($oNode);
                } else {
                    $sLink = static::getTreeService()->getLinkToPageForTreeRelative($oNode);
                }
            }
            $this->SetInternalCache($sKey, $sLink);
        }

        return $sLink;
    }

    /**
     * @param string $sConfimKey
     *
     * @return string
     */
    public function GetConfirmRegistrationURL($sConfimKey)
    {
        $link = $this->GetLinkConfirmRegistrationPage(true);
        $data = array(
            'module_fnc' => array(
                $this->fieldExtranetSpotName => 'ConfirmUser',
                ),
            'key' => $sConfimKey,
        );
        $urlUtil = static::getUrlUtil();
        $link .= $urlUtil->getArrayAsUrl($data, '?', '&');
        $link = $urlUtil->removeAuthenticityTokenFromUrl($link);

        return $link;
    }

    /**
     * Get link to change password for forgot-password email.
     *
     * @param string $loginname
     * @param string $key
     * @param string $spotName
     *
     * @return string
     */
    public function GetPasswordChangeURL($loginname, $key, $spotName)
    {
        $node = new TdbCmsTree();
        $node->Load($this->sqlData['forgot_password_treenode_id']);
        $data = array(
            'module_fnc' => array($spotName => 'ChangeForgotPassword'),
            self::URL_PARAMETER_CHANGE_PASSWORD => $key,
        );
        $link = static::getTreeService()->getLinkToPageForTreeAbsolute($node, $data);
        $link = static::getUrlUtil()->removeAuthenticityTokenFromUrl($link);

        return $link;
    }

    /**
     * return cache trigger for the object
     * @return array{table: string, id: string}[]
     */
    public function GetCacheTrigger()
    {
        $aTrigger = $this->GetFromInternalCache('aCacheTrigger');
        if (null === $aTrigger) {
            $aTrigger = array();
            $aTrigger[] = array('table' => $this->table, 'id' => $this->id);
            $aTrigger[] = array('table' => 'cms_tree', 'id' => '');
            $aTrigger[] = array('table' => 'cms_portal', 'id' => '');
            $this->SetInternalCache('aCacheTrigger', $aTrigger);
        }

        return $aTrigger;
    }

    /**
     * get the cache key used to id the object in cache.
     *
     * @return string
     */
    protected static function CacheGetKey()
    {
        static $sKey = null;
        if (null === $sKey) {
            $aKey = array('class' => __CLASS__, 'ident' => 'objectInstance');
            $oActivePage = self::getActivePageService()->getActivePage();
            if ($oActivePage) {
                $aKey['iLanguageId'] = self::getLanguageService()->getActiveLanguageId();
                $aKey['sPortalId'] = $oActivePage->GetPortal()->id;
            }

            $sKey = TCacheManager::GetKey($aKey);
        }

        return $sKey;
    }

    /**
     * {@inheritdoc}
     *
     * @return void
     *
     * @param string $varName
     * @param array{table: string, id: string}[]|null|string $content
     */
    protected function SetInternalCache($varName, $content)
    {
        parent::SetInternalCache($varName, $content);
        $this->bInternalCacheMarkedAsDirty = true;
    }

    private static function getActivePageService(): ActivePageServiceInterface
    {
        return ServiceLocator::get('chameleon_system_core.active_page_service');
    }

    private static function getMyPortalDomainService(): PortalDomainServiceInterface
    {
        return ServiceLocator::get('chameleon_system_core.portal_domain_service');
    }

    private static function getUrlUtil(): UrlUtil
    {
        return ServiceLocator::get('chameleon_system_core.util.url');
    }
}
