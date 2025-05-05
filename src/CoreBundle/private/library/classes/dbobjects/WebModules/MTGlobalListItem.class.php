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
use ChameleonSystem\CoreBundle\Util\UrlNormalization\UrlNormalizationUtil;

class MTGlobalListItem extends TCMSRecord
{
    public $sDetailLink;

    public $sListLink;

    public $sLinkParameters;

    public $iPage = 0;

    protected $bAllowHTMLDivWrapping = true;

    protected function PostLoadHook()
    {
        parent::PostLoadHook();
        $oGlobal = TGlobal::instance();
        if ($oGlobal->UserDataExists('ipage')) {
            $this->iPage = $oGlobal->GetUserData('ipage');
        } else {
            $this->iPage = 0;
        }

        $this->sListLink = $this->GetListPageUrl();

        $sProductName = $this->GetName();
        $sProductName = $this->getUrlNormalizationUtil()->normalizeUrl($sProductName);

        $productId = $this->id;
        $this->sLinkParameters = urlencode($this->GetURLFakeParameter()).'='.urlencode($sProductName).'&amp;itemid='.$productId;
        $sSep = '?';
        if (strpos($this->sListLink, '?')) {
            $sSep = '&amp;';
        }
        $this->sDetailLink = $this->sListLink.$sSep.$this->sLinkParameters;
    }

    /**
     * returns the url that shows the detail page of the item.
     *
     * @return string
     */
    protected function GetListPageUrl()
    {
        return $this->getActivePageService()->getLinkToActivePageRelative([
            'ipage' => $this->iPage,
        ]);
    }

    /**
     * returns the "fake" parameter for the url that points to the item name
     * the parameter is never used, but makes nice SEO URLs (like ?headline=Important_Notice).
     *
     * @return string
     */
    protected function GetURLFakeParameter()
    {
        return 'headline';
    }

    /**
     * @return UrlNormalizationUtil
     */
    private function getUrlNormalizationUtil()
    {
        return ChameleonSystem\CoreBundle\ServiceLocator::get('chameleon_system_core.util.url_normalization');
    }

    /**
     * @return ActivePageServiceInterface
     */
    private function getActivePageService()
    {
        return ChameleonSystem\CoreBundle\ServiceLocator::get('chameleon_system_core.active_page_service');
    }
}
