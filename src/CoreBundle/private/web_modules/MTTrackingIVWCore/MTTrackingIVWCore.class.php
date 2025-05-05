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

class MTTrackingIVWCore extends TUserCustomModelBase
{
    /**
     * TCMSActivePage object.
     *
     * @var TCMSActivePage
     */
    protected $oActivePage;

    /**
     * IVW Customer ID.
     *
     * @var string
     */
    protected $sIVWCustomerId = '';

    /**
     * AGOF customers who want to use the FRABO tag need to enable this
     * default = false.
     *
     * @var bool
     */
    protected $iFraboTagEnabled = false;

    /**
     * IVW identification code for the page
     * this is loaded from portal division or page configuration if available.
     *
     * @var string
     */
    protected $sIVWPageCode = '';

    /**
     * {@inheritdoc}
     */
    public function Execute()
    {
        $this->data = parent::Execute();
        $this->data['sSZMTag'] = '';
        $this->LoadIVWTag();

        return $this->data;
    }

    /**
     * load Customer IVW Tag from active page.
     *
     * @see http://redmine.esono.de/issues/12178
     * @since 3.4
     */
    protected function LoadIVWTag()
    {
        $this->oActivePage = self::getMyActivePageService()->getActivePage();
        // get IVW Customer/Website ID
        $this->sIVWCustomerId = $this->getPortalDomainService()->getActivePortal()->fieldIvwId;
        if (!empty($this->sIVWCustomerId)) {
            // load IVW code from divison
            $this->sIVWPageCode = $this->oActivePage->getDivision()->sqlData['ivw_code'];

            // overload IVW code from page config if available
            if (!empty($this->oActivePage->fieldIvwCode)) {
                $this->sIVWPageCode = $this->oActivePage->fieldIvwCode;
            }

            $this->data['sSZMTag'] = $this->GetSZMTag();
        }
    }

    /**
     * renders SZM tag HTML.
     *
     * @return string
     */
    protected function GetSZMTag()
    {
        $sTag = '
        <div style="position: absolute; top: 0px; left: 0px;">
            <!-- SZM VERSION="2.0" -->
            <script type="text/javascript">
             var iam_data = {
              "mg":"yes", // migrationsmodus aktiviert
              "st":"'.TGlobal::OutJS($this->sIVWCustomerId).'",
              "cp":"'.TGlobal::OutJS($this->sIVWPageCode).'", // code
              "oc":"'.TGlobal::OutJS($this->sIVWPageCode).'", // code SZM-System 1.5
              "sv":"'.$this->getFraboTagMode().'" // frabo tag (on home)
            }
             iom.c(iam_data);
            </script>
            <!--/SZM -->
        </div>
        ';

        return $sTag;
    }

    /**
     * returns the AGOF "frabo" tag mode string if enabled and page = home.
     *
     * @return string
     */
    protected function getFraboTagMode()
    {
        $sFRABOTagMode = 'ke';
        if ($this->iFraboTagEnabled) {
            // FRABO Tag may not be included on homepage
            $sPortalHomePageID = $this->getPortalDomainService()->getActivePortal()->GetPortalPageId();
            if ($sPortalHomePageID != $this->oActivePage->id) {
                $sFRABOTagMode = 'in';
            }
        }

        return $sFRABOTagMode;
    }

    /**
     * {@inheritdoc}
     */
    public function _AllowCache()
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function _GetCacheParameters()
    {
        $aParameters = parent::_GetCacheParameters();
        $aParameters['activepage'] = self::getMyActivePageService()->getActivePage()->id;

        return $aParameters;
    }

    /**
     * {@inheritdoc}
     */
    public function _GetCacheTableInfos()
    {
        $aClearCacheInfo = parent::_GetCacheTableInfos();

        $oActivePage = self::getMyActivePageService()->getActivePage();

        if (!is_array($aClearCacheInfo)) {
            $aClearCacheInfo = [];
        }
        $aAdditionalClearCacheInfo = [[
            'table' => 'cms_portal',
            'id' => $this->getPortalDomainService()->getActivePortal()->id, ],
            [
                'table' => 'cms_division',
                'id' => $oActivePage->getDivision()->id,
            ],
            [
                'table' => 'cms_tpl_page',
                'id' => $oActivePage->id,
        ], ];

        $aClearCacheInfo = array_merge($aClearCacheInfo, $aAdditionalClearCacheInfo);

        return $aClearCacheInfo;
    }

    /**
     * {@inheritdoc}
     */
    public function GetHtmlHeadIncludes()
    {
        $aIncludes = parent::GetHtmlHeadIncludes();
        $aIncludes[] = '<script type="text/javascript" src="https://script.ioam.de/iam.js"></script>';

        return $aIncludes;
    }

    /**
     * @return ActivePageServiceInterface
     */
    private static function getMyActivePageService()
    {
        return ChameleonSystem\CoreBundle\ServiceLocator::get('chameleon_system_core.active_page_service');
    }

    /**
     * @return PortalDomainServiceInterface
     */
    private function getPortalDomainService()
    {
        return ChameleonSystem\CoreBundle\ServiceLocator::get('chameleon_system_core.portal_domain_service');
    }
}
