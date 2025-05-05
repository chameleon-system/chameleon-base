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
 * this class can manage rss feeds and output them in different formats.
 * /**/
class TCMSRssHandler
{
    /**
     * @var string rss/atom
     */
    protected $sFeedType = 'rss';
    protected $aItems = [];
    public $sFeedTitle = '';
    public $sFeedSubtitle = '';
    protected $aFeedLinks = [];
    protected $aFeedAuthors = [];
    public $sFeedDescription = '';
    public $sFeedHeadlineImageURL = '';
    public $sFeedId = '';
    public $sFeedUpdated = '';
    protected $aItemMapping = ['name' => 'title'];

    /**
     * set a title for the feed.
     *
     * @param string $sTitle
     */
    public function SetFeedTitle($sTitle)
    {
        $this->sFeedTitle = $sTitle;
    }

    /**
     * set a subtitle for the feed.
     *
     * @param string $sSubtitle
     */
    public function SetFeedSubtitle($sSubtitle)
    {
        $this->sFeedSubtitle = $sSubtitle;
    }

    /**
     * set an id for the feed.
     *
     * @param string $sId
     */
    public function SetId($sId = null)
    {
        if (is_null($sId)) {
            $this->sFeedId = TTools::GetUUID();
        } else {
            $this->sFeedId = $sId;
        }
    }

    /**
     * add links to the feed, rel and type can be set.
     *
     * @param string $sURL
     * @param string $sRel
     * @param string $sType
     */
    public function AddLink($sURL, $sRel = 'alternate', $sType = 'text/html')
    {
        $aLink = ['rel' => $sRel, 'type' => $sType, 'href' => $sURL, 'url'];
        $this->aFeedLinks[] = $aLink;
    }

    /**
     * add authors with name and email.
     *
     * @param string $sName
     * @param string $sEmail
     */
    public function AddAuthor($sName, $sEmail = '')
    {
        $aAuthor = ['name' => $sName, 'email' => $sEmail];
        $this->aFeedAuthors[] = $aAuthor;
    }

    /**
     * set a modified date for the feed, null sets date to curretn date.
     *
     * @param string $sDate
     */
    public function SetUpdatedDate($sDate = null)
    {
        if (is_null($sDate)) {
            $this->sFeedUpdated = date('Y-m-d H:i:s');
        } else {
            $this->sFeedUpdated = $sDate;
        }
    }

    /**
     * add an item to the feed, only valid indexes for feeds like summary,
     * title, link, id and updated are processed. You can pass a mapping
     * via AddItemMappingArray().
     *
     * @param array $aItem
     */
    public function AddItem($aItem)
    {
        if ($aItem && is_array($aItem)) {
            $aNewItem = [];
            $aMapping = $this->GetItemMapping();
            foreach ($aItem as $sItemName => $sItemValue) {
                if (array_key_exists($sItemName, $aMapping)) {
                    $aNewItem[$aMapping[$sItemName]] = $sItemValue;
                } else {
                    $aNewItem[$sItemName] = $sItemValue;
                }
            }
            $this->aItems[] = $aNewItem;
        }
    }

    /**
     * returns the mapping used for items added to the feed.
     *
     * @return array
     */
    public function GetItemMapping()
    {
        return $this->aItemMapping;
    }

    /**
     * add a mapping for new feed items in the form 'itemindex'=>'feedindex'.
     *
     * @param array $aMapping
     */
    public function AddItemMappingArray($aMapping)
    {
        if (is_array($aMapping)) {
            $this->aItemMapping = array_merge($this->aItemMapping, $aMapping);
        }
    }

    /**
     * @param string $sType
     */
    public function EscapeFeedData($mValue, $sType = 'text')
    {
        $mValue = str_replace("\r\n", ' ', $mValue);
        $mValue = str_replace("\n\r", ' ', $mValue);
        $mValue = str_replace("\n", ' ', $mValue);
        $mValue = str_replace("\t", ' ', $mValue);
        $mValue = str_replace('"', "'", $mValue);
        $mValue = trim($mValue);
        $mValue = html_entity_decode($mValue, null, 'UTF-8');
        $mValue = preg_replace('/&(?!\w+;)/', '&amp;', $mValue);

        return $mValue;
    }

    /**
     * Output the feed as atom.
     */
    public function OutputAsAtom()
    {
        $this->sFeedType = 'atom';
        $oSimpleXMLElement = new SimpleXMLElement('<?xml version="1.0"  encoding="UTF-8"?><feed xmlns="http://www.w3.org/2005/Atom"></feed>');
        // $namespaces = $oSimpleXMLElement->getNamespaces(true);

        if (!empty($this->sFeedTitle)) {
            $oSimpleXMLElement->addChild('title', $this->EscapeFeedData($this->sFeedTitle, 'text'));
        }
        if (!empty($this->sFeedSubtitle)) {
            $oSimpleXMLElement->addChild('subtitle', $this->EscapeFeedData($this->sFeedSubtitle, 'text'));
        }
        if (count($this->aFeedLinks) > 0) {
            foreach ($this->aFeedLinks as $aLink) {
                $oLink = $oSimpleXMLElement->addChild('link');
                $oLink->addAttribute('rel', $this->EscapeFeedData($aLink['rel'], 'text'));
                $oLink->addAttribute('type', $this->EscapeFeedData($aLink['type'], 'text'));
                $oLink->addAttribute('href', $this->EscapeFeedData($aLink['href'], 'url'));
            }
        }
        if (count($this->aFeedAuthors) > 0) {
            foreach ($this->aFeedAuthors as $aAuthor) {
                $oAuthor = $oSimpleXMLElement->addChild('author');
                $oAuthor->addChild('name', $this->EscapeFeedData($aAuthor['name'], 'text'));
                if (!empty($aAuthor['email'])) {
                    $oAuthor->addChild('email', $this->EscapeFeedData($aAuthor['email'], 'text'));
                }
            }
        }
        if (!empty($this->sFeedUpdated)) {
            $oSimpleXMLElement->addChild('updated', gmdate("Y-m-d\TH:i:s\Z", strtotime($this->sFeedUpdated)));
        }
        if (!empty($this->sFeedId)) {
            $oSimpleXMLElement->addChild('id', $this->EscapeFeedData($this->sFeedId, 'text'));
        }

        foreach ($this->aItems as $aItem) {
            $oEntry = $oSimpleXMLElement->addChild('entry');
            if (!empty($aItem['title'])) {
                $oEntry->addChild('title', $this->EscapeFeedData($aItem['title'], 'text'));
            }
            if (!empty($aItem['link'])) {
                $oEntry->addChild('link', $this->EscapeFeedData($aItem['link'], 'url'));
            }
            if (!empty($aItem['id'])) {
                $oEntry->addChild('id', $this->EscapeFeedData($aItem['id'], 'text'));
            }
            if (!empty($aItem['updated'])) {
                $oEntry->addChild('updated', $this->EscapeFeedData($aItem['updated'], 'text'));
            }
            if (!empty($aItem['summary'])) {
                $oEntry->addChild('summary', $this->EscapeFeedData($aItem['summary'], 'text'));
            }
        }

        $dom = new DOMDocument('1.0');
        $dom->preserveWhiteSpace = false;
        $dom->formatOutput = true;
        $dom->loadXML($oSimpleXMLElement->asXML());
        echo $dom->saveXML();
        exit(0);
    }

    /**
     * Output the feed as RSS.
     */
    public function OutputAsRss()
    {
        $this->sFeedType = 'rss';
        $oSimpleXMLElement = new SimpleXMLElement('<?xml version="1.0"  encoding="UTF-8"?><rss version="2.0"></rss>');
        $oChannel = $oSimpleXMLElement->addChild('channel');
        // $namespaces = $oSimpleXMLElement->getNamespaces(true);

        if (!empty($this->sFeedTitle)) {
            $oChannel->addChild('title', $this->EscapeFeedData($this->sFeedTitle, 'text'));
        }
        if (!empty($this->sFeedSubtitle)) {
            $oChannel->addChild('description', $this->EscapeFeedData($this->sFeedSubtitle, 'text'));
        }
        if (count($this->aFeedLinks) > 0) {
            foreach ($this->aFeedLinks as $aLink) {
                $oLink = $oChannel->addChild('link');
                $oLink->addAttribute('rel', $this->EscapeFeedData($aLink['rel'], 'text'));
                $oLink->addAttribute('type', $this->EscapeFeedData($aLink['type'], 'text'));
                $oLink->addAttribute('href', $this->EscapeFeedData($aLink['href'], 'url'));
            }
        }
        if (count($this->aFeedAuthors) > 0) {
            foreach ($this->aFeedAuthors as $aAuthor) {
                $oAuthor = $oChannel->addChild('author');
                $oAuthor->addChild('name', $this->EscapeFeedData($aAuthor['name'], 'text'));
                if (!empty($aAuthor['email'])) {
                    $oAuthor->addChild('email', $this->EscapeFeedData($aAuthor['email'], 'text'));
                }
            }
        }
        if (!empty($this->sFeedUpdated)) {
            $oChannel->addChild('updated', gmdate("Y-m-d\TH:i:s\Z", strtotime($this->sFeedUpdated)));
        }
        if (!empty($this->sFeedId)) {
            $oChannel->addChild('id', $this->EscapeFeedData($this->sFeedId, 'text'));
        }

        foreach ($this->aItems as $aItem) {
            $oEntry = $oChannel->addChild('item');
            if (!empty($aItem['title'])) {
                $oEntry->addChild('title', $this->EscapeFeedData($aItem['title'], 'text'));
            }
            if (!empty($aItem['link'])) {
                $oEntry->addChild('link', $this->EscapeFeedData($aItem['link'], 'url'));
            }
            if (!empty($aItem['id'])) {
                $oEntry->addChild('id', $this->EscapeFeedData($aItem['id'], 'text'));
            }
            if (!empty($aItem['updated'])) {
                $oEntry->addChild('updated', $this->EscapeFeedData($aItem['updated'], 'text'));
            }
            if (!empty($aItem['summary'])) {
                $oEntry->addChild('description', $this->EscapeFeedData($aItem['summary'], 'text'));
            }
        }

        $dom = new DOMDocument('1.0');
        $dom->preserveWhiteSpace = false;
        $dom->formatOutput = true;
        $dom->loadXML($oSimpleXMLElement->asXML());
        echo $dom->saveXML();
        exit(0);
    }
}
