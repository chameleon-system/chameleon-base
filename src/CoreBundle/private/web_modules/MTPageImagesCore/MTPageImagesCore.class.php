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

/**
 * returns an iterator of images for the current page
 * including additional data assigned to each image. Images are connected to a division
 * and page via the property tables data_division_images/data_bereich_images and data_page_images
 * we will always take the images from the page first. only if we find none will
 * we move to division.
 * /**/
class MTPageImagesCore extends TUserModelBase
{
    /**
     *  current page id.
     *
     * @var string|null
     */
    protected $iPageId;

    /**
     * the page object.
     *
     * @var TCMSPage
     */
    protected $oPage;

    /**
     * the portal division object.
     *
     * @var TdbCmsDivision
     */
    protected $oDivision;

    protected $bAllowHTMLDivWrapping = true;

    public function Init()
    {
        parent::Init();

        $oActivePage = $this->getActivePageService()->getActivePage();
        if ($oActivePage) {
            $this->iPageId = $oActivePage->id;
            $this->oPage = $oActivePage;
            $this->oDivision = $oActivePage->getDivision();
        }
    }

    public function Execute()
    {
        parent::Execute();
        if (is_null($this->oPage)) {
            $this->oPage = new TCMSPage();
            $this->oPage->Load($this->iPageId);
            $this->oDivision = TdbCmsDivision::GetPageDivision($this->oPage);
        }

        $oImageList = $this->GetPageImages();

        if (is_null($oImageList) || $oImageList->Length() < 1) {
            $oImageList = $this->GetDivisionImages();
        }

        if (is_null($oImageList) || $oImageList->Length() < 1) {
            $oImageList = false;
        }

        $this->data['oPageImages'] = $oImageList;

        return $this->data;
    }

    protected function GetPageImages()
    {
        $oImageList = null;
        if ($this->oPage && array_key_exists('data_page_images', $this->oPage->sqlData)) {
            $oImageList = $this->oPage->GetProperties('data_page_images');
        }

        return $oImageList;
    }

    protected function GetDivisionImages()
    {
        $oImageList = null;
        if ($this->oDivision) {
            if (array_key_exists('data_bereich_images', $this->oDivision->sqlData)) {
                $oImageList = $this->oDivision->GetProperties('data_bereich_images');
            } elseif (array_key_exists('data_division_images', $this->oDivision->sqlData)) {
                $oImageList = $this->oDivision->GetProperties('data_division_images');
            }
        }

        return $oImageList;
    }

    /**
     * @return ActivePageServiceInterface
     */
    private function getActivePageService()
    {
        return ChameleonSystem\CoreBundle\ServiceLocator::get('chameleon_system_core.active_page_service');
    }
}
