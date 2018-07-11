<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * @deprecated since 6.2.0 - no longer used.
 */
class TCMSSearchIndexPage
{
    private $htmlContent = null;

    private $textContent = null;

    private $title = null;

    private $url = null;

    private $linksOnPage = array();

    private $fetched = false;

    private $valid = true;

    public function __construct($url)
    {
        $this->url = $url;
    }

    /**
     * fetches a page and extract its elements.
     */
    public function load()
    {
        if ($this->fetchUrl()) {
            $this->extractElements();
        }
    }

    /**
     * fetches a page.
     *
     * @return bool
     */
    private function fetchUrl()
    {
        $url = $this->getUrl();
        $curl = $this->getCurlConfig($url);
        if (null === $curl) {
            return false;
        }

        try {
            $response = curl_exec($curl);
            $error = curl_error($curl);
            $headerSize = curl_getinfo($curl, CURLINFO_HEADER_SIZE);
            $header = substr($response, 0, $headerSize);
            $headerElements = explode("\r\n", $header);
            $body = substr($response, $headerSize);
            curl_close($curl);

            if (stristr($headerElements[0], '404')) {
                $this->setHtmlContent('');
                $this->setFetched(false);
            } else {
                $this->setHtmlContent($body);
                $this->setFetched(true);
            }
        } catch (Exception $e) {
        }

        return $this->couldBeFetched();
    }

    /**
     * @param $url
     *
     * @return null|resource
     */
    private function getCurlConfig($url)
    {
        $urlParts = @parse_url($url);

        if (false === $urlParts) {
            return null;
        }

        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_HEADER, true);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        if (true === _DEVELOPMENT_MODE) {
            curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        }

        return $curl;
    }

    /**
     * extracts all relevant elements from the fetched page.
     */
    private function extractElements()
    {
        $this->extractTitle();
        $this->extractTextContents();
        $this->extractLinksOnPage();
    }

    /**
     * extracts all unique links on page.
     */
    private function extractLinksOnPage()
    {
        $matchString = "/<a(?<before_href>[^>]+?)href=['\"](?<href>[^'\"]*?)['\"](?<after_href>[^>]*?)>(?<link_name>.*?)<\\/a>/si";
        $matches = array();
        preg_match_all($matchString, $this->getHtmlContent(), $matches);

        $attributesBeforeHref = $matches['before_href'];
        $attributesAfterHref = $matches['after_href'];
        $hrefs = $matches['href'];

        /**
         * remove duplicates
         * (array_unique preserves array keys).
         */
        $uniqueHrefs = array_unique($hrefs);

        foreach ($uniqueHrefs as $key => $href) {
            /**
             * only add links starting with "/", "http", "https".
             *
             * links starting with "mailto:", "javascript", etc. won't be added
             */
            if (preg_match('/^(\/|http|https)(.*)/', $href)) {
                /**
                 * check for "rel=nofollow".
                 */
                $linkAttributesWithoutHref = $attributesBeforeHref[$key].' '.$attributesAfterHref[$key];

                if (!preg_match('/rel=(\'|")nofollow(\'|")/', $linkAttributesWithoutHref)) {
                    /**
                     * check for file extensions.
                     */
                    if ('.' != substr($href, -4, 1) && '.' != substr($href, -3, 1)) {
                        $href = $this->cleanLink($href);

                        $this->addLinkOnPage($href);
                    }
                }
            }
        }
    }

    /**
     * - decodes utf-8 link.
     *
     * @param $link
     *
     * @return string
     */
    private function cleanLink($link)
    {
        $link = html_entity_decode($link, ENT_QUOTES, 'UTF-8');

        return $link;
    }

    /**
     * extracts text contents from html content.
     */
    private function extractTextContents()
    {
        $textContent = $this->getHtmlContent();

        /**
         * remove cms exclude sections.
         */
        $matchString = '/(<\\!-- <CMS:exclude> -->(.*?)<\\!-- <\\/CMS:exclude> -->)/si';
        $textContent = preg_replace($matchString, '', $textContent);

        /**
         * extract everything in <body> only.
         */
        $matchString = '/<body(?<body_attribues>.*?)>(?<body_content>.*?)<\/body>/usi';
        $matches = array();
        preg_match_all($matchString, $textContent, $matches);
        if (array_key_exists('body_content', $matches) && array_key_exists('0', $matches['body_content'])) {
            $textContent = $matches['body_content'][0];

            $config = HTMLPurifier_Config::createDefault();
            $config->set('HTML.TidyLevel', 'none');
            $config->set('Core.RemoveInvalidImg', false);
            $config->set('Core.AggressivelyFixLt', false);
            $config->set('Core.ConvertDocumentToFragment', false);
            $config->set('Cache.SerializerPath', PATH_CMS_CUSTOMER_DATA);
            $config->set('HTML.ForbiddenElements', array('script', 'iframe'));
            $purifier = new HTMLPurifier($config);
            $textContent = $purifier->purify($textContent);

            $textContent = str_replace(array('</', "\n", "\t"), array(' </', ' ', ' '), $textContent);
            $textContent = strip_tags($textContent);
            $textContent = html_entity_decode($textContent, ENT_QUOTES, 'UTF-8');
            $textContent = preg_replace('/\s\s+/u', ' ', $textContent);
        } else {
            /**
             * no body found
             * -> page is not indexable.
             */
            $this->setIsInValid();
            $textContent = '';
        }

        $this->setTextContent($textContent);
    }

    /**
     * extracts title from html contents.
     */
    private function extractTitle()
    {
        $matchString = '/<title>(?<THE_TITLE>.*?)<\/title>/si';
        $matches = array();
        preg_match_all($matchString, $this->getHtmlContent(), $matches); //($matchString,array(&$this,'InserVariable'),$sTranslation);
        $title = '';
        if (is_array($matches)) {
            $title = $matches['THE_TITLE'][0];
            $title = html_entity_decode($title, ENT_QUOTES, 'UTF-8');
        }

        $this->setTitle($title);
    }

    /**
     * @return bool
     */
    public function couldBeFetched()
    {
        return $this->fetched;
    }

    public function getHtmlContent()
    {
        return $this->htmlContent;
    }

    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param bool $fetched
     */
    private function setFetched($fetched)
    {
        $this->fetched = $fetched;
    }

    /**
     * @param null $title
     */
    private function setTitle($title)
    {
        $this->title = $title;
    }

    /**
     * @param null $htmlContent
     */
    private function setHtmlContent($htmlContent)
    {
        $this->htmlContent = $htmlContent;
    }

    public function getTextContent()
    {
        return $this->textContent;
    }

    /**
     * @param null $textContent
     */
    private function setTextContent($textContent)
    {
        $this->textContent = $textContent;
    }

    /**
     * @return mixed
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * @return array
     */
    public function getLinksOnPage()
    {
        return $this->linksOnPage;
    }

    /**
     * @param $link
     */
    private function addLinkOnPage($link)
    {
        if (!array_key_exists(sha1($link), $this->linksOnPage)) {
            $this->linksOnPage[sha1($link)] = $link;
        }
    }

    /**
     * @return bool
     */
    public function isValid()
    {
        return $this->valid;
    }

    /**
     * sets a page as invalid (e.g. no body found)
     * emptys title, html-content, text-content and linksOnPage.
     */
    private function setIsInValid()
    {
        $this->valid = false;
        $this->setTitle('');
        $this->setHtmlContent('');
        $this->setTextContent('');
        $this->linksOnPage = array();
    }
}
