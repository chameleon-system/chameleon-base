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
 * fetches one (random) image for the current page. the image is loaded either from the page,
 * or from its division.
 * /**/
class MTPageImageCore extends TUserModelBase
{
    protected $bAllowHTMLDivWrapping = true;

    /**
     * all available images are loaded and the image is picked randomly
     * caching is disabled because it would break the randomness
     * by default this is on
     * you may disable this in the execute method if you are working with only one image.
     *
     * @var bool
     */
    protected $bPickRandomImage = true;

    public function Execute()
    {
        parent::Execute();
        $this->data['oImage'] = $this->GetPageImage();

        return $this->data;
    }

    /**
     * loads a random image from page/division.
     *
     * @return TCMSImage
     */
    public function GetPageImage()
    {
        $imageID = $this->GetImageFromPageTable();
        if (is_null($imageID)) {
            $imageID = $this->GetImageFromDivisionTable();
        }

        $oImage = null;
        if (!is_null($imageID)) {
            /** @var $oImage TCMSImage */
            $oImage = new TCMSImage();
            $oImage->Load($imageID);
        }

        return $oImage;
    }

    /**
     * loads a random image from the page config.
     *
     * @return int
     */
    protected function GetImageFromPageTable()
    {
        $imageID = null;
        $activePage = $this->getActivePageService()->getActivePage();
        if (array_key_exists('images', $activePage->sqlData)) {
            $oPageImages = $activePage->GetImages('images');
            if ($oPageImages->Length() > 0) {
                /* @var $oImage TCMSImage */
                if ($this->bPickRandomImage) {
                    $oImage = $oPageImages->Random();
                } else {
                    $oImage = $oPageImages->Current();
                }
                $imageID = $oImage->id;
            }
        }

        return $imageID;
    }

    /**
     * loads a random image from the division.
     *
     * @return int
     */
    protected function GetImageFromDivisionTable()
    {
        $imageID = null;
        $activePage = $this->getActivePageService()->getActivePage();
        $oDivision = $activePage->getDivision();

        if (null !== $oDivision && is_array($oDivision->sqlData) && array_key_exists('images', $oDivision->sqlData)) {
            $oDivisionImages = $oDivision->GetImages('images');

            if ($oDivisionImages->Length() > 0) {
                /* @var $oImage TCMSImage */
                if ($this->bPickRandomImage) {
                    $oImage = $oDivisionImages->Random();
                } else {
                    $oImage = $oDivisionImages->Current();
                }
                $imageID = $oImage->id;
            }
        }

        return $imageID;
    }

    /**
     * if this function returns true, then the result of the execute function will be cached.
     *
     * @return bool
     */
    public function _AllowCache()
    {
        // caching not possible because we randomize the images
        $bCachingAllowed = true;
        if ($this->bPickRandomImage) {
            $bCachingAllowed = false;
        }

        return $bCachingAllowed;
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
        $aTableInfo = parent::_GetCacheTableInfos();
        $activePage = $this->getActivePageService()->getActivePage();
        $aTableInfo[] = ['table' => 'cms_tpl_page', 'id' => $activePage->id];
        $aTableInfo[] = ['table' => 'cms_portal', 'id' => ''];
        $aTableInfo[] = ['table' => 'cms_division', 'id' => ''];

        return $aTableInfo;
    }

    /**
     * @return ActivePageServiceInterface
     */
    private function getActivePageService()
    {
        return ChameleonSystem\CoreBundle\ServiceLocator::get('chameleon_system_core.active_page_service');
    }
}
