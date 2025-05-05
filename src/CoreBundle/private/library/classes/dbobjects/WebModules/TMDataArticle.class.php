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
 * holds a news article item.
 * /**/
class TMDataArticle extends TCMSRecord
{
    /**
     * article teaser image.
     *
     * @var TCMSImage
     */
    public $oTeaserImage;

    public $sArticleLink;

    public $sDivisionLink;

    public $sDivisionName;

    public $sChangedDate;

    /**
     * this class may be used without an active page set (RSS), so we can not use the activePage singleton
     * and need to be able to set the page manually from the calling class
     * if not set it will default to TCMSActivePage.
     *
     * @var TCMSPage
     */
    public $oActivePage;

    public function __construct($id = null)
    {
        parent::__construct('data_article', $id);
    }

    /**
     * returns a list of divisions.
     *
     * @return TdbCmsDivisionList
     */
    public function GetDivisions()
    {
        $oDivisions = null;
        $sClassName = get_class($this);
        if ('TdbDataArticle' == $sClassName) {
            $oDivisions = $this->GetFieldCmsDivisionList();
        } else {
            $oDataArticle = TdbDataArticle::GetNewInstance();
            $oDivisions = $oDataArticle->GetFieldCmsDivisionList();
        }

        return $oDivisions;
    }

    /**
     * returns a list of related articles.
     *
     * @return TCMSRecordList
     */
    public function GetRelatedArticles()
    {
        $oRelatedArticles = $this->GetMLT('data_article_mlt', 'TMDataArticle', '`data_article`.`teaser_priority`,`data_article`.`name`', 'dbobjects/WebModules', 'Core');

        return $oRelatedArticles;
    }

    protected function PostLoadHook()
    {
        if (null === $this->oActivePage) {
            $this->oActivePage = $this->getActivePageService()->getActivePage();
        }

        // fetch translation
        parent::PostLoadHook();
        $oImages = $this->GetImages('images', true);
        $this->oTeaserImage = $oImages->Current();

        // fetch article link...
        $sArticleName = $this->GetName();
        $sArticleName = $this->getUrlNormalizationUtil()->normalizeUrl($sArticleName);

        $articleId = $this->id;

        // we get the link target from the division of the article...
        $oDivisions = $this->GetDivisions();
        $oDivision = $oDivisions->Current();
        /* @var $oDivision TdbCmsDivision */
        $this->sDivisionLink = static::getTreeService()->getLinkToPageForTreeRelative($oDivision->GetDivisionNode());
        $this->sDivisionName = $oDivision->GetName();

        $sLinkParameters = 'artikel='.urlencode($sArticleName).'&amp;itemid='.$articleId.'&amp;mode=display';
        $this->sArticleLink = $this->sDivisionLink.'?'.$sLinkParameters;
        $this->sChangedDate = substr(ConvertDate($this->sqlData['time_stamp'], 'sql2g'), 0, 10);
    }

    protected function GetListPageUrl()
    {
        return self::getPageService()->getLinkToPageObjectRelative($this->oActivePage, [
            'ipage' => $this->iPage,
        ]);
    }

    /**
     * @return ActivePageServiceInterface
     */
    private function getActivePageService()
    {
        return ChameleonSystem\CoreBundle\ServiceLocator::get('chameleon_system_core.active_page_service');
    }

    /**
     * @return UrlNormalizationUtil
     */
    private function getUrlNormalizationUtil()
    {
        return ChameleonSystem\CoreBundle\ServiceLocator::get('chameleon_system_core.util.url_normalization');
    }
}
