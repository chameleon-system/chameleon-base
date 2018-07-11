<?php

use ChameleonSystem\CoreBundle\Service\ActivePageServiceInterface;

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * @deprecated since 6.2.0 - use TDataExtranetUser instead.
 */
class TCMSDataExtranetUser extends TCMSRecord
{
    public function __construct($id = null)
    {
        $table = 'data_extranet_user';
        parent::__construct($table, $id);
    }

    protected function InUserGroups($aUserGroups)
    {
        $aActiveGroups = $this->GetUserGroupIds();
        // any matches?
        $bIsInGroup = false;
        if (is_array($aActiveGroups) && count($aActiveGroups) > 0) {
            while (!$bIsInGroup && ($groupId = next($aUserGroups))) {
                if (in_array($groupId, $aActiveGroups)) {
                    $bIsInGroup = true;
                }
            }
        }

        return $bIsInGroup;
    }

    protected function GetUserGroupIds()
    {
        $aActiveGroups = $this->GetFromInternalCache('_user_group_ids');
        if (is_null($aActiveGroups)) {
            $aActiveGroups = $this->GetMLTIdList('data_extranet_group_mlt');
            $this->SetInternalCache('_user_group_ids', $aActiveGroups);
        }

        return $aActiveGroups;
    }

    public function AllowActivePageAccess()
    {
        if (TTools::CMSEditRequest()) {
            return true;
        }
        $oActivePage = $this->getActivePageService()->getActivePage();

        return $this->AllowPageAccess($oActivePage->id);
    }

    public function AllowPageAccess($iPage)
    {
        static $aPageList;
        if (TTools::CMSEditRequest()) {
            return true;
        }

        if (!$aPageList || !array_key_exists($iPage, $aPageList)) {
            if (!is_array($aPageList)) {
                $aPageList = array();
            }
            $aPageList[$iPage] = false;
            $aActiveGroups = $this->GetUserGroupIds();
            $databaseConnection = $this->getDatabaseConnection();
            $quotedPageId = $databaseConnection->quote($iPage);
            $activeGroupString = implode(',', array_map(array($databaseConnection, 'quote'), $aActiveGroups));
            if (is_array($aActiveGroups) && count($aActiveGroups) > 0) {
                $query = "SELECT * FROM `cms_tpl_page_data_extranet_group_mlt`
                     WHERE `source_id` = $quotedPageId
                       AND `target_id` IN ($activeGroupString)";
                $matchGroups = MySqlLegacySupport::getInstance()->query($query);
                if (MySqlLegacySupport::getInstance()->num_rows($matchGroups) > 0) {
                    $aPageList[$iPage] = true;
                }
            }
        }

        return $aPageList[$iPage];
    }

    /**
     * return country if connected.
     *
     * @return TCMSCountry
     */
    public function GetCountry()
    {
        $oCountry = null;
        if (array_key_exists('data_country_id', $this->sqlData)) {
            $oCountry = &$this->GetLookup('data_country_id', 'TCMSCountry');
        }

        return $oCountry;
    }

    /**
     * Anrede.
     *
     * @return TdbDataExtranetSalutation
     */
    public function &GetFieldDataExtranetSalutation()
    {
        $oItem = null;
        if (array_key_exists('data_extranet_salutation_id', $this->sqlData)) {
            $oItem = $this->GetFromInternalCache('oLookupdata_extranet_salutation_id');
            if (is_null($oItem)) {
                $oItem = TdbDataExtranetSalutation::GetNewInstance();
                $oItem->SetLanguage($this->iLanguageId);
                if (!$oItem->Load($this->sqlData['data_extranet_salutation_id'])) {
                    $oItem = null;
                }
                $this->SetInternalCache('oLookupdata_extranet_salutation_id', $oItem);
            }
        }

        return $oItem;
    }

    public function GetTitle()
    {
        $sTitle = '';
        $oTitle = $this->GetFieldDataExtranetSalutation();

        if (!is_null($oTitle)) {
            $sTitle = $oTitle->fieldName;
        }

        return $sTitle;
    }

    /**
     * @return ActivePageServiceInterface
     */
    private function getActivePageService()
    {
        return \ChameleonSystem\CoreBundle\ServiceLocator::get('chameleon_system_core.active_page_service');
    }
}
