<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use ChameleonSystem\CoreBundle\Service\PortalDomainServiceInterface;
use Doctrine\DBAL\Connection;

/**
 * Class TCMSSearchIndexPortal.
 *
 * class for indexing a portal
 * every indexable link found on a page will be stored internally.
 * - first in $notIndexedPages
 * - and after it's indexed in $indexedPages
 *
 * @deprecated since 6.2.0 - no longer used.
 */
class TCMSSearchIndexPortal
{
    private $portal = null;

    /**
     * all master domains of the portal.
     *
     * @var array
     */
    private $portalDomains = array();

    /**
     * domain language mapping for portal.
     *
     * @see TMCSSearchIndexPortal::loadPortalMetaData() for more information
     *
     * @var array
     */
    private $portalDomainLanguageMapping = array();

    /**
     * all pages which have not been indexed yet.
     *
     * @var array
     */
    private $notIndexedPages = array();

    /**
     * all pages which already have been indexed.
     *
     * @var array
     */
    private $indexedPages = array();

    /**
     * @var null|array
     */
    private $validPrefixList = null;

    /**
     * @var Connection
     */
    private $databaseConnection = null;

    public function __construct(
        Connection $databaseConnection,
        TdbCmsPortal $portal
    ) {
        $this->databaseConnection = $databaseConnection;
        $this->portal = $portal;
    }

    /**
     * start indexing the portal.
     */
    public function startIndexing()
    {
        /**
         * load start page of portal first.
         */
        $this->loadPortalMetaData();

        $this->notIndexedPages = array($this->getPortal()->GetPortalHomeURL());
        while (count($this->notIndexedPages)) {
            $pageURL = array_shift($this->notIndexedPages);
            $this->indexUrl($pageURL);
        }
    }

    /**
     * indexes a url and stores its contents.
     *
     * @param string $url
     */
    private function indexUrl($url)
    {
        $page = new TCMSSearchIndexPage($url);
        if (!$this->isPageIndexed($page)) {
            $this->addIndexedPage($page);
            $page->load();
            if ($page->couldBeFetched() && $page->isValid()) {
                $this->storePage($page);
                foreach ($this->getCleanedLinksOnPage($page) as $pageLink) {
                    // push pages to internal index
                    $this->addPageToNotIndexedPages(new TCMSSearchIndexPage($pageLink));
                }
            }
        }
    }

    /**
     * looks up portal domains
     * and sets portalDomainLanguageMapping.
     */
    private function loadPortalMetaData()
    {
        /**
         * get all domains which handle a specific language.
         */
        $portal = $this->getPortal();
        $portalDomains = $portal->GetFieldCmsPortalDomainsList();
        $portalDomains->GoToStart();
        while ($portalDomain = $portalDomains->Next()) {
            if ($portalDomain->fieldIsMasterDomain) {
                $this->portalDomains[] = $portalDomain->GetName();

                if (null != $portalDomain->fieldCmsLanguageId) {
                    $this->portalDomainLanguageMapping[$portalDomain->GetName()] = $portalDomain->fieldCmsLanguageId;
                }
            }
        }

        /**
         * get all languages which are handled by primary domain.
         */
        $primaryDomainObject = $this->getPortalDomainService()->getPrimaryDomain($portal->id);
        $primaryDomain = $primaryDomainObject->getInsecureDomainName();
        if (null !== $primaryDomain && true === $portal->fieldUseMultilanguage) {
            $this->portalDomains[] = $primaryDomain;
            $this->portalDomainLanguageMapping[$primaryDomain] = $portal->fieldCmsLanguageId;

            $portalLanguages = $portal->GetActiveLanguages();

            $portalLanguages->GoToStart();
            while ($portalLanguage = $portalLanguages->Next()) {
                /**
                 * prepend iso-name of language to primary domain
                 * e.g. "google.com/en".
                 */
                $url = implode('/', array($primaryDomain, $portalLanguage->fieldIso6391));

                $this->portalDomainLanguageMapping[$url] = $portalLanguage->id;
            }
        }
        $this->portalDomains = array_unique($this->portalDomains);
    }

    /**
     * returns an array of only the local links of the current page
     * (valid portal domain and same portal prefix if defined).
     *
     * @param TCMSSearchIndexPage $page
     *
     * @return array
     */
    private function getCleanedLinksOnPage(TCMSSearchIndexPage $page)
    {
        $cleanedLinks = array();
        foreach ($page->getLinksOnPage() as $link) {
            /**
             * check if link is relative (not external)
             * -> link is from portal-domain itself
             * -> add portal-domain.
             */
            if ('/' == substr($link, 0, 1)) {
                // remove first "/"
                $link = substr_replace($link, '', 0, 1);

                // add base-url of portal infront
                $link = $this->getBaseUrl($page->getUrl()).$link;

                // add "/" at the end, if there isn't one
                if (0 == substr_count($link, '/', strlen($link) - 1, 1)) {
                    $link .= '/';
                }

                $cleanedLinks[] = $this->cleanLink($link);
                continue;
            }

            /**
             * check if domain is one of the portal-domains.
             */
            if (!in_array($this->getBaseUrl($link, true), $this->getPortalDomains())) {
                continue;
            }

            if (false === $this->hasSamePortalPrefix($link)) {
                continue;
            }

            $cleanedLinks[] = $this->cleanLink($link);
        }

        return array_unique($cleanedLinks);
    }

    /**
     * @param string $url
     *
     * @return bool
     */
    private function hasSamePortalPrefix($url)
    {
        $indexedUrlPrefix = $this->getPortalPrefixFromURL($url);

        if ($this->portal->fieldIdentifier === $indexedUrlPrefix) {
            return true;
        }

        return false;
    }

    /**
     * @param string $url
     *
     * @return string
     */
    private function getPortalPrefixFromURL($url)
    {
        $validPortalPrefixes = $this->getPortalPrefixList();
        if (0 === count($validPortalPrefixes)) {
            return '';
        }

        $urlParts = parse_url($url);
        if (!isset($urlParts['path']) || '' === $urlParts['path']) {
            return '';
        }

        $pathParts = explode('/', $urlParts['path']);

        if (!isset($pathParts[1])) {
            return '';
        }

        $prefix = $pathParts[1];
        if (in_array($prefix, $validPortalPrefixes)) {
            return $prefix;
        }

        return '';
    }

    /**
     * @return array
     */
    private function getPortalPrefixList()
    {
        if (null !== $this->validPrefixList) {
            return $this->validPrefixList;
        }

        $this->validPrefixList = array();
        $query = "SELECT `cms_portal`.`identifier`
					FROM `cms_portal`
				   WHERE `cms_portal`.`identifier` != ''";
        $portalList = TdbCmsPortalList::GetList($query);
        while ($portal = $portalList->Next()) {
            $this->validPrefixList[] = $portal->fieldIdentifier;
        }

        return $this->validPrefixList;
    }

    /**
     * - removes session name from url
     * - removes fragment
     * - sorts parameters by name.
     *
     * @param $link
     *
     * @return string
     */
    private function cleanLink($link)
    {
        $link = urldecode($link);

        $urlParts = @parse_url($link);

        $return = $link;

        if ($urlParts) {
            unset($urlParts['fragment']);

            /**
             * sort query parameters.
             */
            $queryParams = array();
            if (array_key_exists('query', $urlParts)) {
                $_queryParams = explode('&', $urlParts['query']);

                foreach ($_queryParams as $_queryParam) {
                    $params = explode('=', $_queryParam);
                    if (isset($params[1]) && '' !== $params[1]) {
                        $queryParams[$params[0]] = $params[1];
                    }
                }

                $queryParams = array_unique($queryParams);
                unset($queryParams[session_name()]);
                ksort($queryParams, SORT_STRING);

                $urlParts['query'] = http_build_query($queryParams);
            }

            $url = (isset($urlParts['scheme'])) ? $urlParts['scheme'].'://' : '';
            $url .= (isset($urlParts['host'])) ? $urlParts['host'] : '';
            $url .= (isset($urlParts['path'])) ? $urlParts['path'] : '';
            $url .= (isset($urlParts['query'])) ? '?'.$urlParts['query'] : '';

            $return = $url;
        }

        return $return;
    }

    /**
     * returns scheme and host for a $url
     * if $hostOnly is set, it will return the host only.
     *
     * @param string $url
     * @param bool   $hostOnly
     *
     * @return string
     */
    private function getBaseUrl($url, $hostOnly = false)
    {
        $urlParts = parse_url($url);

        if ($hostOnly) {
            $url = $urlParts['host'];
        } else {
            $url = $urlParts['scheme'].'://';
            $url .= $urlParts['host'].'/';
        }

        return $url;
    }

    /**
     * remove portal name from page title.
     */
    private function cleanPageTitle($title)
    {
        $title = str_replace($this->getPortal()->GetName(), '', $title);
        if (' - ' == mb_substr($title, 0, 3)) {
            $title = mb_substr($title, 3);
        }

        return $title;
    }

    /**
     * returns the language for a TCMSSearchIndexPage
     * (checks against internal $portalDomainLanguageMapping).
     *
     * @param TCMSSearchIndexPage $page
     *
     * @return string
     */
    private function getPageLanguage(TCMSSearchIndexPage $page)
    {
        $language = null;

        $url = $page->getUrl();

        if ('' !== $url) {
            foreach ($this->getPortalDomainLanguageMapping() as $mappedUrl => $mappedLanguageId) {
                $regex = preg_quote($mappedUrl, '/');
                $regexResult = preg_match('/^(http|https):\/\/'.$regex.'/i', $url);
                if ($regexResult) {
                    $language = $mappedLanguageId;
                }
            }
        }

        return (null == $language) ? $this->portal->fieldCmsLanguageId : $language;
    }

    /**
     * stores the page in temp table.
     *
     * @param TCMSSearchIndexPage $page
     */
    private function storePage(TCMSSearchIndexPage $page)
    {
        $conn = $this->getDatabaseConnection();
        $conn->executeQuery('INSERT INTO `cms_search_index_tmp`
                            SET `name` = :name,
                                `content` = :content,
                                `url` = :url,
                                `cms_portal_id` = :cms_portal_id,
                                `pagetitle` = :title,
                                `id` = :id,
                                `cms_language_id` = :cms_language_id',
            array(
                'name' => md5($page->getUrl()),
                'content' => $page->getTextContent(),
                'url' => $page->getUrl(),
                'cms_portal_id' => $this->getPortal()->id,
                'title' => $this->cleanPageTitle($page->getTitle()),
                'id' => TTools::GetUUID(),
                'cms_language_id' => $this->getPageLanguage($page),
            )
        );

        $this->log(sprintf('<strong>%s</strong><br>(%s)', $page->getUrl(), $page->getTitle()));
    }

    /**
     * adds page (url) to internal index.
     *
     * @param TCMSSearchIndexPage $page
     */
    private function addPageToNotIndexedPages(TCMSSearchIndexPage $page)
    {
        if (!$this->isPageInNotIndexedPages($page) && !$this->isPageIndexed($page)) {
            $this->notIndexedPages[$this->getPageKey($page)] = $page->getUrl();
        }
    }

    /**
     * check if page is in internal "not-indexed-pages" index.
     *
     * @param TCMSSearchIndexPage $page
     *
     * @return bool
     */
    private function isPageInNotIndexedPages(TCMSSearchIndexPage $page)
    {
        return array_key_exists($this->getPageKey($page), $this->notIndexedPages);
    }

    /**
     * returns unique key for a page (by url).
     *
     * @param TCMSSearchIndexPage $page
     *
     * @return string
     */
    private function getPageKey(TCMSSearchIndexPage $page)
    {
        return sha1($page->getUrl());
    }

    /**
     * @return array
     */
    private function getPortalDomainLanguageMapping()
    {
        return $this->portalDomainLanguageMapping;
    }

    /**
     * @return int
     */
    public function getIndexedPagesCount()
    {
        return count($this->indexedPages);
    }

    /**
     * add a page to the internal "indexed-pages" index
     * if the page exists in the interal "not-indexed-pages" index, it will be removed from there.
     *
     * @param TCMSSearchIndexPage $page
     */
    private function addIndexedPage(TCMSSearchIndexPage $page)
    {
        /**
         * remove page from "not-indexed-pages".
         */
        if (array_key_exists($this->getPageKey($page), $this->notIndexedPages)) {
            unset($this->notIndexedPages[$this->getPageKey($page)]);
        }

        $this->indexedPages[$this->getPageKey($page)] = $page->getUrl();
    }

    /**
     * checks if page is already indexed.
     *
     * @param TCMSSearchIndexPage $page
     *
     * @return bool
     */
    private function isPageIndexed(TCMSSearchIndexPage $page)
    {
        return array_key_exists($this->getPageKey($page), $this->indexedPages);
    }

    /**
     * @return \TdbCmsPortal
     */
    private function getPortal()
    {
        return $this->portal;
    }

    /**
     * @param string $msg
     */
    private function log($msg)
    {
        echo '<tr><td>'.$msg.'</td></tr>';
        flush();
    }

    /**
     * @return array
     */
    private function getPortalDomains()
    {
        return $this->portalDomains;
    }

    /**
     * @return Connection
     */
    private function getDatabaseConnection()
    {
        return $this->databaseConnection;
    }

    /**
     * @return PortalDomainServiceInterface
     */
    private function getPortalDomainService()
    {
        return \ChameleonSystem\CoreBundle\ServiceLocator::get('chameleon_system_core.portal_domain_service');
    }
}
