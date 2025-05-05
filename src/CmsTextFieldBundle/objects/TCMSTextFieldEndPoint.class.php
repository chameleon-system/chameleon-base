<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use ChameleonSystem\CoreBundle\Service\PageServiceInterface;
use ChameleonSystem\CoreBundle\ServiceLocator;
use ChameleonSystem\CoreBundle\Util\UrlUtil;
use Symfony\Component\Routing\Exception\RouteNotFoundException;

/**
 * manages a wysiwyxg textfield.
 * /**/
class TCMSTextFieldEndPoint
{
    /**
     * if the size difference between the thumbnail and the original image is smaller than 5 pixel
     * the extra link for the original image will not be rendered.
     *
     * @var int
     */
    protected $iThumbnailSizeThreshold = 5;

    /**
     * the wysiwyg text content.
     *
     * @var string - default = null
     */
    public $content;

    /**
     * max width of a thumbnail inside the wysiwyg text content block
     * forces thumbnail creation to this value if image is to big.
     *
     * @var int
     */
    protected $iMaxThumbWidth = 1200;

    /**
     * CSS rel tag name for lightbox/thickbox groups.
     *
     * @var string
     */
    protected $sImageGroupName = 'lightbox';

    /**
     * array of image effects
     * See method TCMSImage::GetThumbnailPointer for available effects.
     *
     * @var array
     */
    protected $aEffects = [];

    /**
     * max width of zoom images (thickbox/lightbox)
     * if full image is bigger a 2nd thumbnail will be generated.
     *
     * @var int
     */
    protected $iMaxZoomImageWidth = -1;

    /**
     * max height of zoom images (thickbox/lightbox)
     * if full image is bigger a 2nd thumbnail will be generated.
     *
     * @var int
     */
    protected $iMaxZoomImageHeight = -1;

    /**
     * array of cms_media object ids that are enclosed in the text.
     *
     * @var array
     */
    protected $aEnclosedMediaIDs = [];

    /**
     * if set to true, the replace functions will force full urls.
     *
     * @var bool
     */
    protected $bForceFullURLs = false;

    /*
     * used to pass variables to the callback methods called by the regex processes
     */
    /**
     * @var array
     */
    protected $aProcessStack = [];

    /**
     * @param string $content
     */
    public function __construct($content = null)
    {
        $this->content = $content;
    }

    /**
     * returns an array of cms_media object ids that are enclosed in the text
     * use this for cache key trigger ids.
     *
     * @return array
     */
    public function GetEnclosedMediaIDs()
    {
        if (0 == count($this->aEnclosedMediaIDs)) {
            $this->_ReplaceImages($this->content);
        }

        return $this->aEnclosedMediaIDs;
    }

    /**
     * sets the maximum width and height of zoomed images (thickbox/lightbox).
     *
     * @param int $width - default 780
     * @param int $height - default 600
     *
     * @return void
     */
    public function SetMaxImageZoomDimensions($width = 780, $height = 600)
    {
        $this->iMaxZoomImageWidth = $width;
        $this->iMaxZoomImageHeight = $height;
    }

    /**
     * Outputs a WYSIWYG text. Will return an empty string if the text field is "empty" (<div>&nbsp;</div> are
     * considered empty, too).
     * Optionally custom variables can be placed into the WYSIWYG editor - they will be replaced using the
     * aCustomVariables passed. These variables must have the following format: [{name:format}] where "format" is either
     * "string", "date", or "number". It is possible to specify the number of decimals used when formating a number:
     * [{variable:number:decimalplaces}] - example: [{costs:number:2}].
     *
     * @param int $thumbnailWidth - max image width within the text
     * @param bool $includeClearDiv - include a clear div at the end of the text block (is true by default)
     * @param array $aCustomVariables - any custom variables you want to replace
     * @param string $sImageGroupName
     * @param array $aEffects - See method TCMSImage::GetThumbnailPointer for available effects
     *
     * @return string
     */
    public function GetText($thumbnailWidth = 1200, $includeClearDiv = true, $aCustomVariables = null, $sImageGroupName = 'lightbox', $aEffects = [])
    {
        $this->sImageGroupName = $sImageGroupName;
        $this->iMaxThumbWidth = $thumbnailWidth;
        $this->aEffects = $aEffects;

        $content = $this->content;
        $content = $this->replaceFrontController($content);
        $content = $this->_AddCMSClassToLinkedImages($content);
        $content = $this->_ReplaceImages($content);
        $content = $this->_AddCMSClassToExternalLinks($content);
        $content = $this->_ReplaceLinks($content);
        $content = $this->_ReplaceDownloadLinks($content);
        $content = $this->_ReplaceInvalidDivs($content);
        $content = $this->_ReplaceEmptyAligns($content);
        $content = $this->_RemoveEmptyTags($content);
        $content = $this->_AddCMSClassToAnchors($content);
        $content = $this->_ReplaceCmsTextBlockInString($content, $thumbnailWidth);
        if (false === $this->isScriptTagAllowed()) {
            $content = $this->_RemoveScriptTags($content);
        }
        $content = $this->ReplaceCustomVariablesInString($content, $aCustomVariables, $thumbnailWidth);
        $this->content = trim($this->content); // trim whitespaces
        if (!$this->IsEmpty() && !$includeClearDiv) {
            $content = '<div class="cmswysiwyg">'.$content.'</div>';
        }
        if (!$this->IsEmpty() && $includeClearDiv) {
            $content = '<div class="cmswysiwyg">'.$content.'<div class="cleardiv">&nbsp;</div></div>';
        } elseif ($this->IsEmpty()) {
            $content = '';
        }

        return $content;
    }

    private function isScriptTagAllowed(): bool
    {
        return ServiceLocator::getParameter('chameleon_system_cms_text_field.allow_script_tags');
    }

    /**
     * @param string $content
     *
     * @return string
     */
    protected function replaceFrontController($content)
    {
        return str_replace('/INDEX', PATH_CUSTOMER_FRAMEWORK_CONTROLLER, $content);
    }

    /**
     * Outputs a WYSIWYG text for a field for external usage, such as emails, RSS feeds etc.
     *
     * @see TCMSTextFieldEndPoint::GetText()
     *
     * @param int $thumbnailWidth - max image width within the text
     * @param bool $includeClearDiv - include a clear div at the end of the text block (is false by default)
     * @param array $aCustomVariables - any custom variables you want to replace
     * @param bool $bClearThickBox - remove all a href with class thickbox
     * @param bool $bClearScriptTags - clear all script tags
     *
     * @return string
     */
    public function GetTextForExternalUsage($thumbnailWidth = 1200, $includeClearDiv = false, $aCustomVariables = null, $bClearThickBox = false, $bClearScriptTags = false)
    {
        $bOldState = $this->bForceFullURLs;
        $this->bForceFullURLs = true;
        $this->iMaxThumbWidth = $thumbnailWidth;
        $content = $this->replaceFrontController($this->content);
        $content = $this->_ReplaceImages($content, $bClearThickBox);
        $content = $this->_ReplaceLinks($content);
        $content = $this->_ReplaceDownloadLinks($content);
        $content = $this->_ReplaceInvalidDivs($content);
        $content = $this->_ReplaceEmptyAligns($content);
        $content = $this->_RemoveEmptyTags($content);
        $content = $this->_ReplaceCmsTextBlockInString($content, $thumbnailWidth);
        if (true === $bClearScriptTags || false === $this->isScriptTagAllowed()) {
            $content = $this->_RemoveScriptTags($content);
        }
        $content = $this->ReplaceCustomVariablesInString($content, $aCustomVariables, $thumbnailWidth);
        if (!$this->IsEmpty() && !$includeClearDiv) {
            $content = '<div class="cmswysiwyg">'.$content.'</div>';
        }
        if (!$this->IsEmpty() && $includeClearDiv) {
            $content = '<div class="cmswysiwyg">'.$content.'<div class="cleardiv">&nbsp;</div></div>';
        } elseif ($this->IsEmpty()) {
            $content = '';
        }
        $this->bForceFullURLs = $bOldState;

        return $content;
    }

    /**
     * @param string $sContent
     * @param array|null $aCustomVariables
     * @param int $thumbnailWidth
     *
     * @return string
     */
    protected function ReplaceCustomVariablesInString($sContent, $aCustomVariables, $thumbnailWidth)
    {
        if (is_array($aCustomVariables)) {
            $oStringReplace = new TPkgCmsStringUtilities_VariableInjection();
            $sContent = $oStringReplace->replace($sContent, $aCustomVariables, false, $thumbnailWidth);

            // add twig support
            $oSnippet = TPkgSnippetRenderer::GetNewInstance($sContent, IPkgSnippetRenderer::SOURCE_TYPE_STRING);
            reset($aCustomVariables);
            foreach (array_keys($aCustomVariables) as $sKey) {
                $oSnippet->setVar($sKey, $aCustomVariables[$sKey]);
            }
            $sContent = $oSnippet->render();
            unset($oSnippet);
        }

        return $sContent;
    }

    /**
     * returns true if the content is empty or has only an empty <div> or <p> tag.
     *
     * @return bool
     */
    public function IsEmpty()
    {
        return empty($this->content) || '<div>&nbsp;</div>' === $this->content || '<p>&nbsp;</p>' === $this->content;
    }

    /**
     * returns the content of a field without any html and cutted to given length
     * without splitting a word
     * Optional custom variables can be placed into the wysiwyg editor - they will be replaced using the aCustomVariables passed.
     * These variables must have the following format: [{name:formatierung}]
     * "formatierung" ist either string, date, or number. It is possible to specify the number of decimals
     * used when formating a number: [{variable:number:decimalplaces}]
     * example [{costs:number:2}].
     *
     * @param int $length - max length of the text
     * @param array $aCustomVariables - any custom variables you want to replace
     *
     * @return string - the cutted plain text
     */
    public function GetPlainTextWordSave($length = null, $aCustomVariables = null)
    {
        $content = html_entity_decode(strip_tags(trim($this->_ReplaceCmsTextBlockInString($this->content))), null, 'UTF-8');
        // $content = preg_replace('/\s+/', ' ', $content); // remove double whitespaces // but don't remove all CR's!
        while (strpos($content, '  ')) {
            $content = str_replace('  ', ' ', $content);
        }
        $content = str_replace("\t", '  ', $content);
        $content = $this->ReplaceCustomVariablesInString($content, $aCustomVariables, 1200);
        if (!is_null($length) && mb_strlen($content) > $length) {
            $content = mb_substr($content, 0, $length);
        }

        return $content;
    }

    /**
     * @param string $content
     * @param bool $bClearThickbox
     *
     * @return string
     */
    protected function _ReplaceImages($content, $bClearThickbox = false)
    {
        if (false !== stripos($content, 'cmsmedia')) {
            $matchString = "/<img([^>]+?)cmsmedia=['\"]([^'\"]+?)['\"](.*?)\\/>/usi";
            $content = preg_replace_callback($matchString, [$this, '_callback_cmstextfield_imageparser'], $content);
        }

        if ($bClearThickbox) {
            if (false !== stripos($content, 'thickbox')) {
                $matchString = "/<a([^>]+?)class=['\"]([^'\"]*?)thickbox([^'\"]*?)['\"]([^>]*?)>(.*?)<\\/a>/usi";
                $content = preg_replace_callback($matchString, [$this, '_callback_cmstextfield_image_thickbox_clear'], $content);
            }
        } else {
            if (false !== stripos($content, 'cmsLinkSurroundsImage')) {
                $matchString = "/(<a[^>]+?class=['\"][^'\"]*?cmsLinkSurroundsImage[^'\"]*?['\"][^>]*?>.*?<figure[^>]+?class=['\"][^'\"]*?cssmedia[^'\"]*?['\"][^>]*?>.*?)(<a[^>]+?class=['\"][^'\"]*?thickbox[^'\"]*?['\"][^>]*?>)(.*?)(<\\/a>)(.*?<\\/figure>.*?<\\/a>)/usi";
                $content = preg_replace_callback($matchString, [$this, '_callback_cmstextfield_image_thickbox_clear_inHREF'], $content);
            }
        }

        return $content;
    }

    /**
     * @param string $content
     *
     * @return string
     */
    protected function _ReplaceLinks($content)
    {
        if (false === stripos($content, 'pagedef')) {
            return $content;
        }
        $matchString = '/\\'.PATH_CUSTOMER_FRAMEWORK_CONTROLLER.'\\?pagedef=([^&#"]+)([^"]+)?/si';
        $content = preg_replace_callback($matchString, [$this, '_callback_cmstextfield_linkparser'], $content);

        return $content;
    }

    /**
     * add cms class to set default css for anchors.
     *
     * @param string $content
     *
     * @return string
     */
    protected function _AddCMSClassToAnchors($content)
    {
        if (false === stripos($content, '</a>')) {
            return $content;
        }
        $sPatter = '#<a.+?>(|.+?)</a>#';
        $content = preg_replace_callback($sPatter, [$this, '_callback_cmstextfield_anchorparser'], $content);

        return $content;
    }

    /**
     * add cms class to set default css for external links.
     *
     * @param string $content
     *
     * @return string
     */
    protected function _AddCMSClassToExternalLinks($content)
    {
        if (false === stripos($content, '</a>')) {
            return $content;
        }
        $matchString = '#<a.+?>(|.+?)</a>#';
        $content = preg_replace_callback($matchString, [$this, '_callback_cmstextfield_externallinkparser'], $content);

        return $content;
    }

    /**
     * add cms class to set css for links that surround an <img /> tag.
     *
     * @param string $content
     *
     * @return string|null
     */
    protected function _AddCMSClassToLinkedImages($content)
    {
        if (false === stripos($content, '</a>')) {
            return $content;
        }
        $sPatter = '#(<a.+?>)([^</a>]*?(<img.+?>).*?</a>|.*?</a>|([^<img]*?)</a>)#si';
        $content = preg_replace_callback($sPatter, [$this, 'CallbackCmsTextfieldLinkSurroundImageParser'], $content);

        return $content;
    }

    /**
     * callback method to add cms class to anchors.
     *
     * @param array $aMatch
     *
     * @return string
     */
    protected function CallbackCmsTextfieldLinkSurroundImageParser($aMatch)
    {
        if (count($aMatch) > 3 && '' !== trim($aMatch[3])) {
            $sClassName = 'cmsLinkSurroundsImage';
            if (strstr($aMatch[1], 'class')) {
                $sReturnString = preg_replace('#class[[:space:]]*=[[:space:]]*"#', 'class="'.$sClassName.' ', $aMatch[0]);
            } else {
                $sReturnString = preg_replace("#<a\s#", '<a class="'.$sClassName.'" ', $aMatch[0]);
            }
        } else {
            $sReturnString = $aMatch[0];
        }

        return $sReturnString;
    }

    /**
     * @param array $aMatch
     *
     * @return string
     */
    protected function _callback_cmstextfield_image_thickbox_clear($aMatch)
    {
        $returnString = $aMatch[5];

        return $returnString;
    }

    /**
     * Clears an thickbox a tag if its in a tag. then the outer at has higher priority.
     *
     * @param array $aMatch
     *
     * @return string
     */
    protected function _callback_cmstextfield_image_thickbox_clear_inHREF($aMatch)
    {
        $returnString = $aMatch[0];
        if (isset($aMatch[2]) && false !== strpos($aMatch[2], 'thickbox')) {
            $galleryMatchString = "/<a([^>]+?)class=['\"]([^'\"]*?)thickbox([^'\"]*?)['\"]([^>]*?)>(.*?)<\\/a>/usi";
            $returnString = preg_replace_callback($galleryMatchString, [$this, '_callback_cmstextfield_image_thickbox_clear'], $aMatch[0]);
        }

        return $returnString;
    }

    /**
     * @param string $content
     *
     * @return string
     */
    protected function _ReplaceDownloadLinks($content)
    {
        if (false !== stripos($content, 'cmsdocument')) {
            // old download links
            $matchString = "/<span([^>]+?)cmsdocument=[\"]([^'\"]+?)[\"]([^>]*?)><a([^>]+?)href=['\"]([^'\"]*?)['\"]([^>]*?)>([^<]*?)<\\/a>\\s*<span([^>]*?)>([^<]*?)<\\/span><\\/span>/usi";
            $content = preg_replace_callback($matchString, [$this, '_callback_cmstextfield_downloadparser'], $content);

            // new shorter download links
            $aDownloadSpans = $this->_GetDownloadSpans($content);
            foreach ($aDownloadSpans as $sSpan) {
                $sDownloadLink = $this->_GetDownloadLinkFromSpan($sSpan);
                $content = str_replace($sSpan, $sDownloadLink, $content);
            }
        }

        $oStringReplace = new TPkgCmsStringUtilities_VariableInjection_WYSIWYGDownloads();
        $content = $oStringReplace->replace($content, [], false, false);

        return $content;
    }

    /**
     * @param string $sSpan
     *
     * @return string
     */
    protected function _GetDownloadLinkFromSpan($sSpan)
    {
        $aMatch = [];
        $aMatch[0] = $sSpan;

        preg_match('#<span([^>]+?)cmsdocument_(.+?)">(.+?)</span>#', $sSpan, $aMatches);
        if (is_array($aMatches) && count($aMatches) > 0) {
            $aMatch[2] = $aMatches[2]; // download id
            $aMatch[3] = $aMatches[3]; // content between the spans (may contain additional html tags)
        }

        return $this->DownloadParserVersion2($aMatch);
    }

    /**
     * @param string $content
     *
     * @return array
     */
    protected function _GetDownloadSpans($content)
    {
        $aFoundLinks = [];
        while (null != $content) {
            $aResult = $this->_DoGetDownloadSpans($content);
            $content = $aResult['content'];
            if (array_key_exists('span', $aResult)) {
                $aFoundLinks[] = $aResult['span'];
            }
        }

        return $aFoundLinks;
    }

    /**
     * @param string $content
     *
     * @return array
     */
    protected function _DoGetDownloadSpans($content)
    {
        $pos = stripos($content, '<span');
        if ($pos > -1) {
            $nextDocAttr = stripos($content, 'cmsdocument_', $pos);
            $endTagPos = stripos($content, '>', $pos);
            if ($nextDocAttr > -1 && ($endTagPos > $nextDocAttr)) {
                $spanbuffer = substr($content, $pos, $endTagPos - $pos);
                $depth = 1;
                $content = substr($content, $endTagPos);
                while ($depth > 0) {
                    $nextOpenSpan = stripos($content, '<span');
                    $nextClosingSpan = stripos($content, '</span>');
                    if ($nextOpenSpan && ($nextOpenSpan < $nextClosingSpan)) {
                        ++$depth;
                        $spanbuffer .= substr($content, 0, $nextOpenSpan + 5);
                        $content = substr($content, $nextOpenSpan + 5);
                    } else {
                        --$depth;
                        $spanbuffer .= substr($content, 0, $nextClosingSpan + 7);
                        $content = substr($content, $nextClosingSpan + 7);
                    }
                }

                return ['content' => $content, 'span' => $spanbuffer];
            } else {
                $nextEndTag = stripos($content, '</span>');
                $content = substr($content, $nextEndTag + 7);

                return ['content' => $content];
            }
        }

        return ['content' => null];
    }

    /**
     * removes <div"> (we don`t know where these came from, maybe a bug in WYSIWYGPro).
     *
     * @param string $content
     *
     * @return string
     */
    protected function _ReplaceInvalidDivs($content)
    {
        $content = str_replace('<div">', '', $content);
        $content = str_replace('</div">', '', $content);

        return $content;
    }

    /**
     * sets "bottom" in empty align properties.
     *
     * @param string $content
     *
     * @return string
     */
    protected function _ReplaceEmptyAligns($content)
    {
        $content = str_replace('align=""', 'align="bottom"', $content);

        return $content;
    }

    /**
     * @param string $content
     *
     * @return string
     */
    protected function _RemoveScriptTags($content)
    {
        return preg_replace('/<script([^>]+?)>(.*?)<\\/script>/si', '', $content);
    }

    /**
     * filters empty tags (a, b, strong, em, i, div, p, span).
     *
     * @param string $content
     *
     * @return string
     */
    protected function _RemoveEmptyTags($content)
    {
        $content = str_replace('<strong></strong>', '', $content);
        $content = str_replace('<em></em>', '', $content);

        return $content;
    }

    /**
     * callback method that replaces CMS download links with frontend HTML.
     *
     * @param array $aMatch
     *
     * @return string
     */
    public function _callback_cmstextfield_downloadparser($aMatch)
    {
        $sResult = '';
        if ('wysiwyg_cmsdownloaditem' == $aMatch[1]) {
            $sResult = $this->DownloadParserVersion2($aMatch);
        } else {
            $sResult = $this->DownloadParserVersion1($aMatch);
        }

        return $sResult;
    }

    /**
     * Parse downloads for wysiwyg downloads with style <span class="wysiwyg_cmsdownloaditem cmsdocument_13">[ico]title[kb]</span>
     * If no [ico] or [kb] or no title function returns link without fileicon or file size or title.
     *
     * @param array $aMatch
     *
     * @return string
     */
    protected function DownloadParserVersion2($aMatch)
    {
        $sResult = '';
        $itemId = '';
        if (isset($aMatch[2])) {
            $itemId = $aMatch[2];
        }
        $oItem = new TCMSDownloadFile(); /* @var $oItem TCMSDownloadFile */
        if (!empty($itemId) && $oItem->Load($itemId)) {
            $bHideSize = true;
            $bHideName = false;
            $bHideIcon = true;
            $sLinkName = '';
            if (!empty($aMatch[3])) {
                if (preg_match("#^(\[ico\])?(.*\\s*.*\\s*.*)(\[kb\])?$#", $aMatch[3], $aSubMatch)) {
                    $iLen = strlen($aSubMatch[0]);
                    $iStart = strpos($aSubMatch[0], '[ico]');
                    if (false !== strpos($aSubMatch[0], '[ico]')) {
                        $iStart = strpos($aSubMatch[0], '[ico]') + 5;
                        $bHideIcon = false;
                    }
                    if (false !== strpos($aSubMatch[0], '[kb]')) {
                        $iLen = $iLen - 4;
                        $bHideSize = false;
                    }

                    $sLinkName = substr($aSubMatch[0], $iStart, $iLen - $iStart);
                    if ('' == trim($sLinkName)) {
                        $bHideName = true;
                    }
                }
            } else {
                $bHideName = true;
            }
            if ($sLinkName != $oItem->GetName()) {
                $sResult = $oItem->getDownloadHtmlTag(false, $bHideName, $bHideSize, $bHideIcon, $sLinkName);
            } else {
                $sResult = $oItem->getDownloadHtmlTag(false, $bHideName, $bHideSize, $bHideIcon);
            }
        } else {
            $sResult = $aMatch[0];
        }

        return $sResult;
    }

    /**
     * Parse downloads for wysiwyg with style <div><span class="cmsdownloaditem"><a title="title" target="_blank" href="...">Title</a></span>[63 kb]</div>.
     *
     * @param array $aMatch
     *
     * @return string
     */
    protected function DownloadParserVersion1($aMatch)
    {
        $sResult = '';
        $sItemId = $aMatch[2];
        $sLinkName = $aMatch[7];
        $oItem = new TCMSDownloadFile();
        if ($oItem->Load($sItemId)) {
            if ($sLinkName != $oItem->GetName()) {
                $sResult = $oItem->getDownloadHtmlTag(false, false, false, false, $sLinkName);
            } else {
                $sResult = $oItem->getDownloadHtmlTag();
            }
        }

        return $sResult;
    }

    /**
     * search for all attributes within an a-html-tag link.
     *
     * @param string $sLink full html tag
     *
     * @return array
     */
    protected function getLinkAttributes($sLink)
    {
        preg_match_all('/ (?:[\w]*) *= *"(?:(?:(?:(?:(?:\\\W)*\\\W)*[^"]*)\\\W)*[^"]*")/', $sLink, $aLinkAttributes);
        if (isset($aLinkAttributes) && isset($aLinkAttributes[0])) {
            return $aLinkAttributes[0];
        } else {
            return [];
        }
    }

    /**
     * callback method that replaces CMS links (treeID) with frontend SEO links.
     *
     * @param array $aMatch
     *
     * @return string
     */
    protected function _callback_cmstextfield_linkparser($aMatch)
    {
        $pageId = trim($aMatch[1]);
        $urlParams = '';
        if (3 === count($aMatch)) {
            $urlParams = trim($aMatch[2]);
        }
        $pageService = $this->getPageService();
        $page = $pageService->getById($pageId);
        if (null === $page) {
            return '#';
        }

        $anchorPos = strpos($urlParams, '#');
        $anchor = '';
        if (false !== $anchorPos) {
            $anchor = substr($urlParams, $anchorPos);
            $urlParams = substr($urlParams, 0, $anchorPos);
        }
        $urlParams = preg_replace('/&amp;/', '&', $urlParams);
        // other parameters in url? keep them
        $iOtherParamPos = strpos($urlParams, '&');
        if (false === $iOtherParamPos) {
            $parameters = [];
        } else {
            $otherParams = substr($urlParams, $iOtherParamPos);
            $parameters = $this->getUrlUtil()->getUrlParametersAsArray($otherParams);
        }

        try {
            if ($this->bForceFullURLs) {
                $link = $pageService->getLinkToPageObjectAbsolute($page, $parameters);
            } else {
                $link = $pageService->getLinkToPageObjectRelative($page, $parameters);
            }

            $link .= $anchor;
        } catch (RouteNotFoundException $e) {
            return '#pageNotFound';
        }

        return $link;
    }

    /**
     * callback method to replace CMS image tags with frontend html
     * if you need to add custom code you should overwrite the PostImageWraper method.
     *
     * @param array $aMatch
     *
     * @return string
     *
     * @psalm-suppress InvalidScalarArgument - Some array type juggling is happening in this method
     */
    protected function _callback_cmstextfield_imageparser($aMatch)
    {
        $tags = [
            'cmscaption' => '',
            'cmsshowcaption' => 0,
            'width' => 0,
            'height' => 0,
            'style' => '',
            'align' => '',
            'border' => 0,
            'cmsshowfull' => 0,
            'src' => '',
            'alt' => '',
            'title' => '',
            'usemap' => '',
            'class' => '',
        ];
        $parameterString = $aMatch[1].' '.$aMatch[3];
        $matchString = '/\\s*(.*?)\\s*=\\s*"(.*?)".*?/si';
        $parameters = '';
        preg_match_all($matchString, $parameterString, $parameters);

        foreach ($parameters[1] as $id => $paramname) {
            if (array_key_exists($paramname, $tags)) {
                $value = $parameters[2][$id];
                if (is_numeric($tags[$paramname])) {
                    $tags[$paramname] = (int) $value;
                } else {
                    $tags[$paramname] = $value;
                }
            }
        }

        $returnString = '';
        if ('/' !== substr($tags['src'], 0, 1)) {
            $tags['src'] = '/'.$tags['src'];
        }

        $oImage = new TCMSImage();
        $sCMSMediaID = $aMatch[2];
        $this->aEnclosedMediaIDs[] = $sCMSMediaID;
        if ($oImage->Load($sCMSMediaID)) {
            if ($tags['width'] <= 0) {
                $tags['width'] = $oImage->aData['width'];
            }
            if ($tags['height'] <= 0) {
                $tags['height'] = $oImage->aData['height'];
            }

            // the grid container may not allow images higher than $this->iMaxThumbWidth so we reduce the thumbnail if needed
            if (null !== $this->iMaxThumbWidth && $tags['width'] > $this->iMaxThumbWidth) {
                // need to reduce height in proportions
                $tags['height'] = round(($tags['height'] / $tags['width']) * $this->iMaxThumbWidth);
                $tags['width'] = $this->iMaxThumbWidth;
            }

            $sStyles = trim($tags['style']);
            if (!empty($sStyles) && ';' !== substr($sStyles, -1)) {
                $sStyles .= '; ';
            }

            if ('left' === $tags['align'] || 'right' === $tags['align'] || 'center' === $tags['align']) {
                $sStyles .= ' text-align:'.$tags['align'].';';
            }

            if ($this->isResponsiveImagesEnabled()) {
                // remove height and width styles from WYSIWYG editor and add width based on width property
                $sStyles = $this->removeAllOccurences($sStyles, 'height');
            }

            $sStyles = $this->removeAllOccurences($sStyles, 'width');

            $sStyles .= ' width: '.$tags['width'].'px;';

            $tags['style'] = $sStyles;

            if ($oImage->IsExternalMovie()) {
                $oThumb = $oImage;
                if ($oImage->aData['width'] != $tags['width'] || $oImage->aData['height'] != $tags['height']) {
                    $oThumb = $oImage->GetThumbnail($tags['width'], $tags['height'], true);
                }

                $returnString = $oThumb->GetExternalVideoEmbedCode();

                if (1 == $tags['cmsshowcaption']) {
                    $returnString = "<figure class=\"cssmedia cmsflv {$tags['class']}\" style=\"".$sStyles.'">
                    '.$returnString."<figcaption class=\"cssmediacaption\">{$tags['cmscaption']}</figcaption></figure>";
                }
            } else {
                $sFullImageURL = $this->GetFullImagePath($oImage, $tags);

                $oViewRender = $this->getViewRenderer();
                $oViewRender->AddMapper(new TPkgCmsTextfieldImage());
                $oViewRender->AddSourceObject('oImage', $oImage); // full image (not resized yet)
                $oViewRender->AddSourceObject('sFullImageURL', $sFullImageURL);
                $oViewRender->AddSourceObject('sImageGroupName', $this->sImageGroupName);
                $oViewRender->AddSourceObject('iThumbnailSizeThreshold', $this->iThumbnailSizeThreshold);
                $oViewRender->AddSourceObject('aEffects', $this->aEffects);
                $oViewRender->AddSourceObject('fromWYSIWYG', true);
                $oViewRender->AddSourceObject('isForceThumbnailGenerationOnFullSizeImagesEnabled', $this->isForceThumbnailGenerationOnFullSizeImagesEnabled());

                $sImageTagTemplatePath = '/common/media/pkgCmsTextFieldImage.html.twig';
                if (true === $this->isResponsiveImagesEnabled() && true === $this->isImageBiggerThanMobileScreenSize($oImage) && true === $this->isBiggerThanMobileScreenSize($tags['width'])) {
                    $sImageTagTemplatePath = '/common/media/pkgCmsTextFieldImageResponsive.html.twig';

                    if (isset($tags['class'])) {
                        if (stristr($tags['class'], 'img-responsive')) {
                            $tags['class'] = trim(str_replace('img-responsive', '', $tags['class']));
                        }
                    }
                }

                $oViewRender->AddSourceObject('aTagProperties', $tags);
                $returnString = $oViewRender->Render($sImageTagTemplatePath);
            }
            $returnString = $this->PostImageWraper($returnString, $sStyles, $tags['align'], $tags['width'], $tags['height'], $tags['border']);
        }

        return $returnString;
    }

    /**
     * @return bool
     */
    protected function isImageBiggerThanMobileScreenSize(TCMSImage $oImage)
    {
        return $this->isBiggerThanMobileScreenSize((int) $oImage->aData['width']);
    }

    /**
     * @param int $width
     *
     * @return bool
     */
    protected function isBiggerThanMobileScreenSize($width)
    {
        if (false === defined('IMAGE_RENDERING_RESPONSIVE_MOBILE_SCREEN_SIZE')) {
            return true;
        }

        if ($width > IMAGE_RENDERING_RESPONSIVE_MOBILE_SCREEN_SIZE) {
            return true;
        }

        return false;
    }

    /**
     * checks for constant: IMAGE_RENDERING_RESPONSIVE
     * you may overwrite this method to allow portal based en/disabling the responsive rendering.
     *
     * @return bool
     */
    protected function isResponsiveImagesEnabled()
    {
        return true === IMAGE_RENDERING_RESPONSIVE;
    }

    /**
     * callback method to add cms class to anchors.
     *
     * @param array $aMatch
     *
     * @return string
     */
    protected function _callback_cmstextfield_anchorparser($aMatch)
    {
        if ('' == trim($aMatch[1]) && (strstr($aMatch[0], 'name="') || strstr($aMatch[0], 'id="'))) {
            if (strstr($aMatch[0], 'class="')) {
                $sReturnString = preg_replace('#class="#', 'class="cmsanchor ', $aMatch[0]);
            } else {
                $sReturnString = preg_replace("#<a\s#", '<a class="cmsanchor" ', $aMatch[0]);
            }
        } else {
            $sReturnString = $aMatch[0];
        }

        return $sReturnString;
    }

    /**
     * callback method to add cms class to external links.
     *
     * @todo we maybe want to implement an additional domain check
     *
     * @param array $aMatch
     *
     * @return string
     */
    protected function _callback_cmstextfield_externallinkparser($aMatch)
    {
        $sPattern = '/\\'.PATH_CUSTOMER_FRAMEWORK_CONTROLLER.'\\?pagedef=([^&]+?)(&|&amp;)__treeNode=([^"]+?)"/si';
        $aTmpMatches = [];
        if (0 === preg_match($sPattern, trim($aMatch[0]), $aTmpMatches)) {
            if (strstr($aMatch[0], 'class="')) {
                $sReturnString = preg_replace('#class="#', 'class="external ', $aMatch[0]);
            } else {
                $sReturnString = preg_replace("#<a\s#", '<a class="external" ', $aMatch[0]);
            }
        } else {
            $sReturnString = $aMatch[0];
        }

        return $sReturnString;
    }

    /**
     * overwrite this method to change image tag properties.
     *
     * @param string $sImageString
     * @param string $sStyle
     * @param string $sAlign
     * @param string $width
     * @param string $height
     * @param string $border
     *
     * @return string
     */
    protected function PostImageWraper($sImageString, $sStyle, $sAlign, $width, $height, $border)
    {
        return $sImageString;
    }

    /**
     * returns the path to the zoom image.
     *
     * @param TCMSImage $oImage
     * @param array $tags
     *
     * @return string
     */
    protected function GetFullImagePath($oImage, $tags)
    {
        // check if we need to generate a 2nd thumbnail
        $maxWidth = $this->iMaxZoomImageWidth;
        $maxHeight = $this->iMaxZoomImageHeight;

        $globalZoomMaxWidth = CMS_MAX_IMAGE_ZOOM_WIDTH;
        $globalZoomMaxHeight = CMS_MAX_IMAGE_ZOOM_HEIGHT;

        if (!empty($globalZoomMaxWidth) || !empty($globalZoomMaxHeight)) {
            if ($this->iMaxZoomImageWidth > $globalZoomMaxWidth || 0 === $this->iMaxZoomImageWidth) {
                $maxWidth = $globalZoomMaxWidth;
            }
            if ($this->iMaxZoomImageHeight > $globalZoomMaxHeight || 0 === $this->iMaxZoomImageHeight) {
                $maxHeight = $globalZoomMaxHeight;
            }
        }

        if ($maxWidth > 0 && $maxHeight > 0) { // generate thumbnail
            if ($oImage->aData['width'] > $maxWidth || $oImage->aData['height'] > $maxHeight) {
                $oBigThumb = $oImage->GetThumbnail($maxWidth, $maxHeight);
                $sFullImagePath = $oBigThumb->GetFullURL();
            } else {
                // original image
                $sFullImagePath = $oImage->GetFullURL();
            }
        } else {
            if (true === $this->isForceThumbnailGenerationOnFullSizeImagesEnabled()) {
                $oBigThumb = $oImage->GetThumbnail($oImage->aData['width'], $oImage->aData['height']);
                $sFullImagePath = $oBigThumb->GetFullURL();
            } else {
                // original image
                $sFullImagePath = $oImage->GetFullURL();
            }
        }

        return $sFullImagePath;
    }

    /**
     * if the size difference between the thumbnail and the original image is smaller than 5 pixel
     * the extra link for the original image will not be rendered.
     *
     * @param int $iSize
     *
     * @return void
     */
    protected function SetThumbnailSizeThreshold($iSize)
    {
        $this->iThumbnailSizeThreshold = $iSize;
    }

    /**
     * @return int
     */
    protected function GetThumbnailSizeThreshold()
    {
        return $this->iThumbnailSizeThreshold;
    }

    /**
     * Hook for pkg CmsTextBlock to replace text blocks from string.
     *
     * @param string $sString
     * @param int $iWidth
     *
     * @return string
     */
    protected function _ReplaceCmsTextBlockInString($sString, $iWidth = 1200)
    {
        return $sString;
    }

    /**
     * @param string $sStyles
     * @param string $cssAttribute
     *
     * @return string
     */
    protected function removeAllOccurences($sStyles, $cssAttribute)
    {
        $pattern = '/(^| )'.$cssAttribute.':\s*\d*\s*(px|%);*/';
        $sStyles = preg_replace($pattern, '', $sStyles);

        return $sStyles;
    }

    /**
     * @return PageServiceInterface
     */
    private function getPageService()
    {
        return ServiceLocator::get('chameleon_system_core.page_service');
    }

    /**
     * @return UrlUtil
     */
    private function getUrlUtil()
    {
        return ServiceLocator::get('chameleon_system_core.util.url');
    }

    /**
     * @return ViewRenderer
     */
    private function getViewRenderer()
    {
        return ServiceLocator::get('chameleon_system_view_renderer.view_renderer');
    }

    /**
     * @return bool
     */
    protected function isForceThumbnailGenerationOnFullSizeImagesEnabled()
    {
        return false;
    }
}
