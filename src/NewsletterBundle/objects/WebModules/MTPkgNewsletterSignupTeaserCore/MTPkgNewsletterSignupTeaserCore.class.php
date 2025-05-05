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
use ChameleonSystem\CoreBundle\Service\PageServiceInterface;

/**
 * @deprecated since 6.2.0 - no longer used.
 */
class MTPkgNewsletterSignupTeaserCore extends TUserCustomModelBase
{
    /**
     * @var TdbPkgNewsletterModuleSignupTeaser|false|null
     */
    private $oModuleConfig;

    /**
     * @var TdbPkgNewsletterModuleSignupConfig
     */
    private $oTargetConfig;

    public function Execute()
    {
        parent::Execute();

        $this->data['oModuleConfig'] = $this->GetModuleConfig();
        $this->data['oTargetConfig'] = $this->GetModuleTargetConfig();

        if ($this->data['oTargetConfig']) {
            $this->data['aMainModuleInfo'] = $this->GetMainModuleInfo($this->data['oTargetConfig']->fieldCmsTplModuleInstanceId);
        }

        return $this->data;
    }

    /**
     * @return TdbPkgNewsletterModuleSignupTeaser|false
     */
    protected function GetModuleConfig()
    {
        if (is_null($this->oModuleConfig)) {
            $this->oModuleConfig = TdbPkgNewsletterModuleSignupTeaser::GetNewInstance();
            if (false == $this->oModuleConfig->LoadFromField('cms_tpl_module_instance_id', $this->instanceID)) {
                $this->oModuleConfig = false;
            }
        }

        return $this->oModuleConfig;
    }

    /**
     * @return TdbPkgNewsletterModuleSignupConfig|null
     */
    protected function GetModuleTargetConfig()
    {
        if (is_null($this->oTargetConfig)) {
            $oConfig = $this->GetModuleConfig();
            $this->oTargetConfig = TdbPkgNewsletterModuleSignupConfig::GetNewInstance();
            if (false == $this->oTargetConfig->LoadFromField('cms_tpl_module_instance_id', $oConfig->fieldConfigForSignupModuleInstanceId)) {
                $this->oTargetConfig = null;
            }
        }

        return $this->oTargetConfig;
    }

    /**
     * if this function returns true, then the result of the execute function will be cached.
     *
     * @return bool
     */
    public function _AllowCache()
    {
        return true;
    }

    /**
     * if the content that is to be cached comes from the database (as ist most often the case)
     * then this function should return an array of assoc arrays that point to the
     * tables and records that are associated with the content. one table entry has
     * two fields:
     *   - table - the name of the table
     *   - id    - the record in question. if this is empty, then any record change in that
     *             table will result in a cache clear.
     *
     * @return array
     */
    public function _GetCacheTableInfos()
    {
        $aTrigger = parent::_GetCacheTableInfos();
        if (!is_array($aTrigger)) {
            $aTrigger = [];
        }
        $sConfigId = null;
        $sTargetSignupId = null;
        $oConfig = $this->GetModuleConfig();
        if ($oConfig) {
            $sConfigId = $oConfig->id;
        }

        $oTargetConfig = $this->GetModuleTargetConfig();
        if ($oTargetConfig) {
            $sTargetSignupId = $oTargetConfig->id;
        }
        $aTrigger[] = ['table' => 'pkg_newsletter_module_signup_teaser', 'id' => $sConfigId];
        $aTrigger[] = ['table' => 'pkg_newsletter_module_signup_config', 'id' => $sTargetSignupId];

        return $aTrigger;
    }

    /**
     * get main module info
     * NOTE: the code is a copy of the code in MTPkgNewsletterSignupCore - this should be cleaned up and moved to a common class.
     *
     * @param string $sModuleInstanceId
     *
     * @return array $aReturnArray (page url of main module & spotname of mainmodule)
     */
    protected function GetMainModuleInfo($sModuleInstanceId)
    {
        $oPortal = $this->getActivePageService()->getActivePage()->GetPortal();
        $aReturnArray = [];
        $oModuleInstance = TdbCmsTplModuleInstance::GetNewInstance();
        $oModuleInstance->Load($sModuleInstanceId);
        if (method_exists($oModuleInstance, 'GetFieldCmsTplPageList')) {
            $oPageList = $oModuleInstance->GetFieldCmsTplPageList();
            $bFound = false;
            while ($oPage = $oPageList->Next()) {
                if ($oPage->fieldCmsPortalId == $oPortal->id && !$bFound) {
                    $aReturnArray['URL'] = $this->getPageService()->getLinkToPageObjectRelative($oPage);
                    $query = "SELECT `cms_master_pagedef_spot`.`name` FROM `cms_tpl_page_cms_master_pagedef_spot`
           LEFT JOIN `cms_master_pagedef_spot` ON `cms_master_pagedef_spot`.`id`=`cms_tpl_page_cms_master_pagedef_spot`.`cms_master_pagedef_spot_id`
               WHERE `cms_tpl_page_cms_master_pagedef_spot`.`cms_tpl_page_id` ='".MySqlLegacySupport::getInstance()->real_escape_string($oPage->id)."'
                 AND `cms_tpl_page_cms_master_pagedef_spot`.`cms_tpl_module_instance_id` = '".MySqlLegacySupport::getInstance()->real_escape_string($sModuleInstanceId)."'";
                    $res = MySqlLegacySupport::getInstance()->query($query);
                    while ($Spotrow = MySqlLegacySupport::getInstance()->fetch_assoc($res)) {
                        $aReturnArray['spotname'] = $Spotrow['name'];
                    }
                    $bFound = true;
                }
            }
        } elseif (method_exists($oModuleInstance, 'GetFieldCmsTplPageCmsMasterPagedefSpotList')) {
            $oPageDefSpotList = $oModuleInstance->GetFieldCmsTplPageCmsMasterPagedefSpotList();
            $bFound = false;
            while ($oPageDefSpot = $oPageDefSpotList->Next()) {
                $oPage = TdbCmsTplPage::GetNewInstance();
                $oPage->Load($oPageDefSpot->fieldCmsTplPageId);
                if ($oPage->fieldCmsPortalId == $oPortal->id && !$bFound) {
                    $aReturnArray['URL'] = $this->getPageService()->getLinkToPageObjectRelative($oPage);
                    $query = "SELECT `cms_master_pagedef_spot`.`name` FROM `cms_tpl_page_cms_master_pagedef_spot`
           LEFT JOIN `cms_master_pagedef_spot` ON `cms_master_pagedef_spot`.`id`=`cms_tpl_page_cms_master_pagedef_spot`.`cms_master_pagedef_spot_id`
               WHERE `cms_tpl_page_cms_master_pagedef_spot`.`cms_tpl_page_id` ='".MySqlLegacySupport::getInstance()->real_escape_string($oPage->id)."'
                 AND `cms_tpl_page_cms_master_pagedef_spot`.`cms_tpl_module_instance_id` = '".MySqlLegacySupport::getInstance()->real_escape_string($sModuleInstanceId)."'";
                    $res = MySqlLegacySupport::getInstance()->query($query);
                    while ($Spotrow = MySqlLegacySupport::getInstance()->fetch_assoc($res)) {
                        $aReturnArray['spotname'] = $Spotrow['name'];
                    }
                    $bFound = true;
                }
            }
        }

        return $aReturnArray;
    }

    /**
     * @return ActivePageServiceInterface
     */
    private function getActivePageService()
    {
        return ChameleonSystem\CoreBundle\ServiceLocator::get('chameleon_system_core.active_page_service');
    }

    /**
     * @return PageServiceInterface
     */
    private function getPageService()
    {
        return ChameleonSystem\CoreBundle\ServiceLocator::get('chameleon_system_core.page_service');
    }
}
