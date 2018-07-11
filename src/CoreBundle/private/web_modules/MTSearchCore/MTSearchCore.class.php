<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use ChameleonSystem\CoreBundle\Service\LanguageServiceInterface;
use ChameleonSystem\CoreBundle\Service\SystemPageServiceInterface;
use ChameleonSystem\CoreBundle\Service\TreeServiceInterface;

/**
 * @deprecated since 6.2.0 - no longer used.
 */
class MTSearchCore extends TUserCustomModelBase
{
    /**
     * @var bool
     */
    protected $bAllowHTMLDivWrapping = true;

    /**
     * the search has no paging yet, so the results are limited to 50 (default)
     * set to -1 if you added paging and want all results.
     *
     * @var int
     */
    protected $iResultLimit = 50;

    /**
     * @var TdbModuleCmsSearch|null
     */
    protected $_oTableRow = null;

    /**
     * tree node on which to display the search results.
     *
     * @var TdbCmsTree
     */
    protected $oSearchPageNode = null;

    /**
     * holds the page object needed in the XML view.
     *
     * @deprecated since 6.1.6 - OpenSearch is no longer supported
     *
     * @var TCMSRecord|null
     */
    protected $oPage = null;

    /**
     * result set of the search.
     *
     * @var TdbCmsSearchIndexList|null
     */
    protected $oResult = null;

    /**
     * URL to the search page
     * used also for the detail page
     * is loaded from systempage: "searchResult".
     *
     * @var string
     */
    protected $sSearchURL = '';

    /**
     * {@inheritdoc}
     */
    public function Init()
    {
        parent::Init();
        $this->LoadTableRow();

        $fnc = $this->global->GetUserData('_fnc');
        if ('RedirectSearchPage' === $fnc) {
            $aParams = array('_fnc' => 'RunSearch');
            $sSearchTerm = $this->getSearchTerm();
            if ($sSearchTerm) {
                $aParams['searchword'] = $sSearchTerm;
            }
            $sURL = $this->getTreeService()->getLinkToPageForTreeRelative($this->oSearchPageNode, $aParams);
            $this->getRedirect()->redirect($sURL);
        } elseif ('RunSearch' === $fnc) {
            $this->RunSearch();
        } elseif ('' === $fnc && $this->getSearchTerm()) {
            $this->RunSearch();
        }
    }

    /**
     * {@inheritdoc}
     */
    protected function DefineInterface()
    {
        parent::DefineInterface();
        $externalFunctions = array('RunSearch', 'RedirectSearchPage');
        $this->methodCallAllowed = array_merge($this->methodCallAllowed, $externalFunctions);
    }

    /**
     * {@inheritdoc}
     */
    public function &Execute()
    {
        $this->data = parent::Execute();
        $this->data['searchword'] = '';
        $this->data['q'] = '';

        $sSearchTerm = $this->getSearchTerm();
        if ($sSearchTerm) {
            $this->data['searchword'] = $sSearchTerm;
            $this->data['q'] = $sSearchTerm;
        }
        $this->data['oTableRow'] = $this->_oTableRow;
        $this->data['searchURL'] = $this->getSearchPageURL();
        $this->data['oSearchPageNode'] = $this->oSearchPageNode;
        $this->data['oResults'] = $this->oResult;

        return $this->data;
    }

    protected function LoadTableRow()
    {
        $langID = $this->global->GetUserData('langID');
        $instanceID = $this->global->GetUserData('instID');

        $oModuleCmsSearch = TdbModuleCmsSearch::GetNewInstance();
        $oModuleCmsSearch->SetLanguage($langID);
        $oModuleCmsSearch->LoadFromFieldWithCaching('cms_tpl_module_instance_id', $instanceID);
        $this->_oTableRow = $oModuleCmsSearch;

        $this->oSearchPageNode = null;
        if (isset($this->_oTableRow->sqlData['cms_tree_id']) && !empty($this->_oTableRow->sqlData['cms_tree_id'])) {
            $this->oSearchPageNode = new TdbCmsTree();
            $this->oSearchPageNode->LoadWithCaching($this->_oTableRow->sqlData['cms_tree_id']);
        }
    }

    /**
     * get the search term from GET/POST
     * we read "q" and "searchword" - you should use "q" because search engines match this as search term to ignore it.
     *
     * @return mixed - returns string or false
     */
    protected function getSearchTerm()
    {
        $sSearchTerm = $this->global->GetUserData('q');
        if ('' === $sSearchTerm) {
            $sSearchTerm = $this->global->GetUserData('searchword');
        }

        return $sSearchTerm;
    }

    /**
     * adds portal, domain and pageId to the template data.
     *
     * @deprecated since 6.1.6 - OpenSearch is no longer supported
     */
    public function GetOpenSearchXMLData()
    {
    }

    /**
     * returns the search result URL
     * based on system page "searchResult".
     *
     * @return string
     */
    protected function getSearchPageURL()
    {
        if (empty($this->sSearchURL)) {
            $this->sSearchURL = $this->getSystemPageService()->getLinkToSystemPageRelative('searchResult');
        }

        return $this->sSearchURL;
    }

    public function RunSearch()
    {
        $sSearchWord = $this->getSearchTerm();
        $sSearchWord = trim($sSearchWord);
        $_aSearchWords = explode(' ', $sSearchWord);

        $aSearchWords = array();

        foreach ($_aSearchWords as $sSearchWord) {
            if (!in_array($sSearchWord, $this->getStopWords(), true)) {
                $aSearchWords[] = MySqlLegacySupport::getInstance()->real_escape_string(strtolower($sSearchWord));
            }
        }

        if (0 === count($aSearchWords)) {
            return;
        }

        $portal = $this->getActivePortal();

        if (null === $portal) {
            return;
        }

        // columns to search an their weight
        $aSearchColumns = array(
            '`cms_search_index`.`pagetitle`' => 100.0,
            '`cms_search_index`.`content`' => 1.0,
        );

        $query = 'SELECT `cms_search_index`.*';

        $aOccurrencesQueryParts = array();
        foreach ($aSearchColumns as $sSearchColumn => $fSearchColumnWeight) {
            $aOccurrencesWeightQueryParts = array();
            /*
             * get the occurences of the searchword in the field
             * unfortunately mysql does not provide any smart way for it
             * (length of the columns - length of the column without searchword) / length of the searchword = occurences of the searchword in the column
             */
            foreach ($aSearchWords as $sSearchWord) {
                $aOccurrencesWeightQueryParts[] = '(((LENGTH('.$sSearchColumn.') - LENGTH(REPLACE(LOWER('.$sSearchColumn.'), "'.$sSearchWord.'", ""))) / LENGTH("'.$sSearchWord.'")) * '.$fSearchColumnWeight.' )';
            }
            $aOccurrencesQueryParts[] = ' ( '.implode(' + ', $aOccurrencesWeightQueryParts).' ) ';
        }
        $query .= ', ('.implode(' + ', $aOccurrencesQueryParts).') as `_occurrences` ';

        $languageService = $this->getLanguageService();

        $query .= 'FROM `cms_search_index`
                   WHERE
                        `cms_search_index`.`cms_portal_id`  = "'.MySqlLegacySupport::getInstance(
            )->real_escape_string($portal->id).'"
                        AND
                        `cms_search_index`.`cms_language_id`  = "'.MySqlLegacySupport::getInstance(
            )->real_escape_string($languageService->getActiveLanguageId()).'"';

        $aSearchColumnQueryParts = array();
        foreach ($aSearchColumns as $sSearchColumn => $fSearchColumnWeight) {
            $aSearchWordQueryParts = array();
            foreach ($aSearchWords as $sSearchWord) {
                $aSearchWordQueryParts[] = $sSearchColumn.' LIKE "%'.$sSearchWord.'%"';
            }
            $aSearchColumnQueryParts[] = ' ( '.implode(' AND ', $aSearchWordQueryParts).' ) ';
        }
        $query .= ' AND ( '.implode(' OR ', $aSearchColumnQueryParts).' )';
        $query .= ' ORDER BY  `_occurrences` desc';

        if ($this->iResultLimit > 0) {
            $query .= ' LIMIT '.MySqlLegacySupport::getInstance()->real_escape_string($this->iResultLimit);
        }
        $this->oResult = TdbCmsSearchIndexList::GetList($query);
    }

    /**
     * @return array
     */
    public function getStopWords()
    {
        $stopWordsEnglish = array('a', 'about', 'above', 'after', 'again', 'against', 'all', 'am', 'an', 'and', 'any', 'are', "aren't", 'as', 'at', 'be', 'because', 'been', 'before', 'being', 'below', 'between', 'both', 'but', 'by', "can't", 'cannot', 'could', "couldn't", 'did', "didn't", 'do', 'does', "doesn't", 'doing', "don't", 'down', 'during', 'each', 'few', 'for', 'from', 'further', 'had', "hadn't", 'has', "hasn't", 'have', "haven't", 'having', 'he', "he'd", "he'll", "he's", 'her', 'here', "here's", 'hers', 'herself', 'him', 'himself', 'his', 'how', "how's", 'i', "i'd", "i'll", "i'm", "i've", 'if', 'in', 'into', 'is', "isn't", 'it', "it's", 'its', 'itself', "let's", 'me', 'more', 'most', "mustn't", 'my', 'myself', 'no', 'nor', 'not', 'of', 'off', 'on', 'once', 'only', 'or', 'other', 'ought', 'our', 'ours', 'ourselves', 'out', 'over', 'own', 'same', "shan't", 'she', "she'd", "she'll", "she's", 'should', "shouldn't", 'so', 'some', 'such', 'than', 'that', "that's", 'the', 'their', 'theirs', 'them', 'themselves', 'then', 'there', "there's", 'these', 'they', "they'd", "they'll", "they're", "they've", 'this', 'those', 'through', 'to', 'too', 'under', 'until', 'up', 'very', 'was', "wasn't", 'we', "we'd", "we'll", "we're", "we've", 'were', "weren't", 'what', "what's", 'when', "when's", 'where', "where's", 'which', 'while', 'who', "who's", 'whom', 'why', "why's", 'with', "won't", 'would', "wouldn't", 'you', "you'd", "you'll", "you're", "you've", 'your', 'yours', 'yourself', 'yourselves');
        $stopWordsGerman = array('aber', 'als', 'am', 'an', 'auch', 'auf', 'aus', 'bei', 'bin', 'bis', 'bist', 'da', 'dadurch', 'daher', 'darum', 'das', 'daß', 'dass', 'dein', 'deine', 'dem', 'den', 'der', 'des', 'dessen', 'deshalb', 'die', 'dies', 'dieser', 'dieses', 'doch', 'dort', 'du', 'durch', 'ein', 'eine', 'einem', 'einen', 'einer', 'eines', 'er', 'es', 'euer', 'eure', 'für', 'hatte', 'hatten', 'hattest', 'hattet', 'hier', 'hinter', 'ich', 'ihr', 'ihre', 'im', 'in', 'ist', 'ja', 'jede', 'jedem', 'jeden', 'jeder', 'jedes', 'jener', 'jenes', 'jetzt', 'kann', 'kannst', 'können', 'könnt', 'machen', 'mein', 'meine', 'mit', 'muß', 'mußt', 'musst', 'müssen', 'müßt', 'nach', 'nachdem', 'nein', 'nicht', 'nun', 'oder', 'seid', 'sein', 'seine', 'sich', 'sie', 'sind', 'soll', 'sollen', 'sollst', 'sollt', 'sonst', 'soweit', 'sowie', 'und', 'unser', 'unsere', 'unter', 'vom', 'von', 'vor', 'wann', 'warum', 'was', 'weiter', 'weitere', 'wenn', 'wer', 'werde', 'werden', 'werdet', 'weshalb', 'wie', 'wieder', 'wieso', 'wir', 'wird', 'wirst', 'wo', 'woher', 'wohin', 'zu', 'zum', 'zur', 'über');

        return array_merge($stopWordsEnglish, $stopWordsGerman);
    }

    /**
     * @return TreeServiceInterface
     */
    private function getTreeService()
    {
        return \ChameleonSystem\CoreBundle\ServiceLocator::get('chameleon_system_core.tree_service');
    }

    /**
     * @return ICmsCoreRedirect
     */
    private function getRedirect()
    {
        return \ChameleonSystem\CoreBundle\ServiceLocator::get('chameleon_system_core.redirect');
    }

    /**
     * @return null|TdbCmsPortal
     */
    private function getActivePortal()
    {
        return \ChameleonSystem\CoreBundle\ServiceLocator::get('chameleon_system_core.portal_domain_service')->getActivePortal();
    }

    /**
     * @return SystemPageServiceInterface
     */
    private function getSystemPageService()
    {
        return \ChameleonSystem\CoreBundle\ServiceLocator::get('chameleon_system_core.system_page_service');
    }

    /**
     * @return LanguageServiceInterface
     */
    private function getLanguageService()
    {
        return \ChameleonSystem\CoreBundle\ServiceLocator::get('chameleon_system_core.language_service');
    }
}
