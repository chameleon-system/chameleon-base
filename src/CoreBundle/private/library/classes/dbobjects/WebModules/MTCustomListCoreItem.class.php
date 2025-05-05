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

/**
 * holds an item of the custom list.
 * /**/
class MTCustomListCoreItem extends TCMSRecord
{
    public $sDetailLink;
    public $iPage = 0;
    public $sListLink;
    public $sLinkParameters;

    protected $bAllowHTMLDivWrapping = true;

    protected function PostLoadHook()
    {
        parent::PostLoadHook();

        if ('<div>&nbsp;</div>' != $this->sqlData[$this->GetItemNameField()] && !empty($this->sqlData[$this->GetItemNameField()])) {
            $oGlobal = TGlobal::instance();
            if ($oGlobal->UserDataExists('ipage')) {
                $this->iPage = $oGlobal->GetUserData('ipage');
            } else {
                $this->iPage = 0;
            }

            $this->sListLink = $this->GetListPageUrl();

            $sItemName = $this->GetName();
            $sItemName = $this->getUrlNormalizationUtil()->normalizeUrl($sItemName);

            $itemId = $this->id;
            $this->sLinkParameters = urlencode($this->GetURLFakeParameter()).'='.urlencode($sItemName).'&amp;itemid='.$itemId; // .'&amp;ipage='.$this->iPage;
            $this->sDetailLink = $this->sListLink.'&amp;'.$this->sLinkParameters;
        }
    }

    /**
     * @return string
     */
    protected function GetItemNameField()
    {
        return 'artikel';
    }

    /**
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
     * the parameter is never used, but makes for nice urls (like ?headline=Important_Notice).
     *
     * @return string
     */
    protected function GetURLFakeParameter()
    {
        return 'artikel';
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
