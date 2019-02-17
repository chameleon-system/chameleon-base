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
 * @deprecated since 6.3.0 - unusable since decades.
 */
class TCMSFontImage extends TAdbCmsFontImage
{
    const FONT_IMAGE_DIR = 'chameleon/mediapool';
    const BG_IMAGE_FILE_DIR = 'images/';

    private $iWidth = 0;
    private $iHeight = 0;
    private $aTextLines = null;
    private $iLineHeight = 0;
    private $sLineHeight = 4;

    /**
     * Get a font image profile from  given profile name.
     *
     * @param string $sProfileName
     *
     * @return TdbCmsFontImage
     */
    public static function GetInstanceFromProfile($sProfileName)
    {
        static $aActiveItem = array();
        /* @var $oActiveItem TAdbCmsfontImage*/
        if (!array_key_exists($sProfileName, $aActiveItem)) {
            $aActiveItem[$sProfileName] = TdbCmsFontImage::GetNewInstance();
            /* @var $oActiveItem TdbCmsfontImage*/
            if (!$aActiveItem[$sProfileName]->LoadFromField('profile_name', $sProfileName)) {
                $aActiveItem[$sProfileName] = null;
            }
        }

        return $aActiveItem[$sProfileName];
    }

    /**
     * Get or generate Image with given text.
     *
     * @param string $sText
     * @param int    $iMaxWidth
     *
     * @return string $sFilePathName
     */
    public function GetFontImage($sText, $iMaxWidth = null)
    {
        $sText = $this->UmlWorkaround($sText);
        $sFilePathName = TdbCmsFontImage::FONT_IMAGE_DIR.'/'.$this->id.'/'.md5($sText.$iMaxWidth).'.'.$this->fieldImgType;
        if (!file_exists($sFilePathName) || !\ChameleonSystem\CoreBundle\ServiceLocator::getParameter('chameleon_system_core.cache.allow')) {
            $this->CheckProfileDir();
            $this->GenerateFontImage($sText, $sFilePathName, $iMaxWidth);
        }
        $sDomain = '';
        $oURLData = &TCMSSmartURLData::GetActive();
        $sDomain = $oURLData->sDomainName;
        $this->aTextLines = null;
        $sTransportPrefix = 'http://';
        $oMySmartURLData = TCMSSmartURLData::GetActive();
        if ($oMySmartURLData->bIsSSLCall) {
            $sTransportPrefix = 'https://';
        }

        return $sTransportPrefix.$sDomain.'/'.$sFilePathName;
    }

    /**
     * Get or generate image link with three different profiles with given text.
     *
     * @param string $sText
     * @param string $sHoverProfileName
     * @param string $sActiveProfileName
     *
     * @return string $sFilePathName
     */
    public function GetFontImageLink($sText, $sHoverProfileName, $sActiveProfileName)
    {
        $sFilePathName = TdbCmsFontImage::FONT_IMAGE_DIR.'/'.$this->id.'/'.md5($sText.$this->fieldName.$sHoverProfileName.$sActiveProfileName).'.'.$this->fieldImgType;
        if (!file_exists($sFilePathName) || !\ChameleonSystem\CoreBundle\ServiceLocator::getParameter('chameleon_system_core.cache.allow')) {
            $this->CheckProfileDir();
            $oHoverProfile = TdbCmsFontImage::GetInstanceFromProfile($sHoverProfileName);
            $oActiveProfile = TdbCmsFontImage::GetInstanceFromProfile($sActiveProfileName);
            if (!is_null($oHoverProfile) && !is_null($oActiveProfile)) {
                $this->GenerateFontImageLink($sText, $oHoverProfile, $oActiveProfile, $sFilePathName);
                $this->aTextLines = null;

                return '/'.$sFilePathName;
            } else {
                $this->aTextLines = null;

                return '/';
            }
        }
        $this->aTextLines = null;

        return '/'.$sFilePathName;
    }

    /**
     * generate image object to all profiles , merge them and save the result image.
     *
     * @param string        $sText
     * @param TCMSFontImage $oHoverProfile
     * @param TCMSFontImage $oActiveProfile
     * @param string        $sFilePathName
     */
    protected function GenerateFontImageLink($sText, $oHoverProfile, $oActiveProfile, $sFilePathName)
    {
        $oNormalImageLink = $this->GenerateFontImageObject($sText);
        $oHoverImageLink = $oHoverProfile->GenerateFontImageObject($sText);
        $oActiveImageLink = $oActiveProfile->GenerateFontImageObject($sText);
        $oLinkImage = $this->MergeLinkImages($oHoverProfile, $oActiveProfile, $oNormalImageLink, $oHoverImageLink, $oActiveImageLink);
        $this->SaveHeaderImage($oLinkImage, $sFilePathName);
    }

    /**
     * merge different images together.
     *
     * @param TCMSFontImage $oHoverProfile
     * @param TCMSFontImage $oActiveProfile
     * @param string        $oNormalImageLink
     * @param string        $oHoverImageLink
     * @param string        $oActiveImageLink
     *
     * @return resource $LinkImage
     */
    protected function MergeLinkImages($oHoverProfile, $oActiveProfile, $oNormalImageLink, $oHoverImageLink, $oActiveImageLink)
    {
        $iHeight = $this->iHeight + $oHoverProfile->iHeight + $oActiveProfile->iHeight;
        $iWidth = $this->iWidth;
        if ($iWidth < $oHoverProfile->iWidth) {
            $iWidth = $oHoverProfile->iWidth;
        }
        if ($iWidth < $oActiveProfile->iWidth) {
            $iWidth = $oActiveProfile->iWidth;
        }
        $LinkImage = imagecreatetruecolor($iWidth, $iHeight);
        $aBG_RGB_Array = $this->HexToRGB('000000');
        imagealphablending($LinkImage, false);
        $oBackgroundColor = imagecolorallocatealpha($LinkImage, $aBG_RGB_Array[0], $aBG_RGB_Array[1], $aBG_RGB_Array[2], 127);
        imagefill($LinkImage, 0, 0, $oBackgroundColor);
        imagesavealpha($LinkImage, true);
        imagealphablending($LinkImage, true);
        // imageantialias($LinkImage, true);
        $this->imagecopymerge_alpha($LinkImage, $oNormalImageLink, 0, 0, 0, 0, $this->iWidth, $this->iHeight, 90);
        $this->imagecopymerge_alpha($LinkImage, $oHoverImageLink, 0, $this->iHeight, 0, 0, $oHoverProfile->iWidth, $oHoverProfile->iHeight, 90);
        $this->imagecopymerge_alpha($LinkImage, $oActiveImageLink, 0, $this->iHeight + $oHoverProfile->iHeight, 0, 0, $oActiveProfile->iWidth, $oActiveProfile->iHeight, 90);
        $this->iHeight = $iHeight;
        $this->iWidth = $iWidth;

        return $LinkImage;
    }

    /**
     * generate imageobject.
     *
     * @param string $sText
     *
     * @return resource $buff
     */
    public function GenerateFontImageObject($sText)
    {
        $atextSizes = $this->GetImageSizes($sText);
        $this->iWidth = $atextSizes['width'];
        $this->iHeight = $atextSizes['height'];
        $buff = imagecreatetruecolor($this->iWidth, $this->iHeight);

        $aFont_RGB_Array = $this->HexToRGB($this->fieldFontColor);
        $oFontColor = imagecolorallocate($buff, $aFont_RGB_Array[0], $aFont_RGB_Array[1], $aFont_RGB_Array[2]);

        if (-1 == $this->fieldImgBackgroundColor) {
            $aBG_RGB_Array = $this->HexToRGB('000000');
            imagealphablending($buff, false);
            $oBackgroundColor = imagecolorallocatealpha($buff, $aBG_RGB_Array[0], $aBG_RGB_Array[1], $aBG_RGB_Array[2], 127);
            imagefill($buff, 0, 0, $oBackgroundColor);
            imagesavealpha($buff, true);
            imagealphablending($buff, true);
        } else {
            $aBG_RGB_Array = $this->HexToRGB($this->fieldImgBackgroundColor);
            $oBackgroundColor = imagecolorallocate($buff, $aBG_RGB_Array[0], $aBG_RGB_Array[1], $aBG_RGB_Array[2]);
            imagefill($buff, 0, 0, $oBackgroundColor);
        }
        if ('bold' == $this->fieldFontWeight) {
            $sText = $sText."\r".$sText;
        }
        //      imageantialias($buff, true);
        imagettftext($buff, $this->fieldFontSize, 0, $atextSizes['fontxstart'], $atextSizes['fontystart'], $oFontColor, PATH_CMS_FONTS.$this->fieldFontFilename, $sText);
        if ($this->fieldImgBackgroundImg) {
            $oBgImage = $this->GetBgImage();
            if ($oBgImage) {
                $this->imagecopymerge_alpha($oBgImage, $buff, $atextSizes['TextPositionX'], $atextSizes['TextPositionY'], 0, 0, $this->iWidth, $this->iHeight, 90);

                return $oBgImage;
            } else {
                return $buff;
            }
        } else {
            return $buff;
        }
    }

    /**
     * Check or Create the profiles image directory.
     */
    protected function CheckProfileDir()
    {
        if (!is_dir(TdbCmsFontImage::FONT_IMAGE_DIR.'/'.$this->id)) {
            mkdir(TdbCmsFontImage::FONT_IMAGE_DIR.'/'.$this->id);
        }
    }

    /**
     * Gernerate and save the font image.
     *
     * @param string $sText
     * @param int    $iMaxWidth
     * @param string $sSavePath
     */
    protected function GenerateFontImage($sText, $sSavePath, $iMaxWidth = null)
    {
        $atextSizes = $this->GetImageSizes($sText, $iMaxWidth);
        $this->iWidth = $atextSizes['width'];
        $this->iHeight = $atextSizes['height'];
        $iRealHeight = $this->iHeight;
        if (is_array($this->aTextLines) && count($this->iHeight) > 0) {
            $iRealHeight = count($this->iHeight) * $this->iLineHeight;
        }
        $buff = imagecreatetruecolor($this->iWidth, $this->iHeight);
        $aFont_RGB_Array = $this->HexToRGB($this->fieldFontColor);
        $oFontColor = imagecolorallocate($buff, $aFont_RGB_Array[0], $aFont_RGB_Array[1], $aFont_RGB_Array[2]);

        if (-1 == $this->fieldImgBackgroundColor) {
            $aBG_RGB_Array = $this->HexToRGB('000000');
            imagealphablending($buff, false);
            $oBackgroundColor = imagecolorallocatealpha($buff, $aBG_RGB_Array[0], $aBG_RGB_Array[1], $aBG_RGB_Array[2], 127);
            imagefill($buff, 0, 0, $oBackgroundColor);
            imagesavealpha($buff, true);
            imagealphablending($buff, true);
        } else {
            $aBG_RGB_Array = $this->HexToRGB($this->fieldImgBackgroundColor);
            $oBackgroundColor = imagecolorallocate($buff, $aBG_RGB_Array[0], $aBG_RGB_Array[1], $aBG_RGB_Array[2]);
            imagefill($buff, 0, 0, $oBackgroundColor);
        }
        if ('bold' == $this->fieldFontWeight) {
            $sText = $sText."\r".$sText;
        }
        // imageantialias($buff, true);
        if (!is_null($this->aTextLines)) {
            $sLineHeight = 0;
            foreach ($this->aTextLines as $sTextLine) {
                imagettftext($buff, $this->fieldFontSize, 0, $atextSizes['fontxstart'], $atextSizes['fontystart'] + $sLineHeight, $oFontColor, PATH_CMS_FONTS.$this->fieldFontFilename, trim($sTextLine));
                $sLineHeight = $sLineHeight + $this->iLineHeight;
            }
        } else {
            imagettftext($buff, $this->fieldFontSize, 0, $atextSizes['fontxstart'], $atextSizes['fontystart'], $oFontColor, PATH_CMS_FONTS.$this->fieldFontFilename, $sText);
        }
        if ($this->fieldImgBackgroundImg) {
            $oBgImage = $this->GetBgImage();
            if ($oBgImage) {
                $this->imagecopymerge_alpha($oBgImage, $buff, $atextSizes['TextPositionX'], $atextSizes['TextPositionY'], 0, 0, $this->iWidth, $this->iHeight, 90);
                $this->SaveHeaderImage($oBgImage, $sSavePath);
                imagedestroy($buff);
            } else {
                $this->SaveHeaderImage($buff, $sSavePath);
            }
        } else {
            $this->SaveHeaderImage($buff, $sSavePath);
        }
    }

    /**
     * meger two images with alphachannel.
     *
     * @param string $dst_im
     * @param string $src_im
     * @param string $dst_x
     * @param string $dst_y
     * @param string $src_x
     * @param string $src_y
     * @param string $src_w
     * @param string $src_h
     * @param int    $pct
     */
    protected function imagecopymerge_alpha($dst_im, $src_im, $dst_x, $dst_y, $src_x, $src_y, $src_w, $src_h, $pct)
    {
        if (!isset($pct)) {
            return false;
        }
        $pct /= 100;
        // Get image width and height
        $w = imagesx($src_im);
        $h = imagesy($src_im);
        // Turn alpha blending off
        imagealphablending($src_im, false);
        // Find the most opaque pixel in the image (the one with the smallest alpha value)
        $minalpha = 127;
        for ($x = 0; $x < $w; ++$x) {
            for ($y = 0; $y < $h; ++$y) {
                $alpha = (imagecolorat($src_im, $x, $y) >> 24) & 0xFF;
                if ($alpha < $minalpha) {
                    $minalpha = $alpha;
                }
            }
        }
        //loop through image pixels and modify alpha for each
        for ($x = 0; $x < $w; ++$x) {
            for ($y = 0; $y < $h; ++$y) {
                //get current alpha value (represents the TANSPARENCY!)
                $colorxy = imagecolorat($src_im, $x, $y);
                $alpha = ($colorxy >> 24) & 0xFF;
                //calculate new alpha
                if (127 !== $minalpha) {
                    $alpha = 127 + 127 * $pct * ($alpha - 127) / (127 - $minalpha);
                } else {
                    $alpha += 127 * $pct;
                }
                //get the color index with new alpha
                $alphacolorxy = imagecolorallocatealpha($src_im, ($colorxy >> 16) & 0xFF, ($colorxy >> 8) & 0xFF, $colorxy & 0xFF, $alpha);
                //set pixel with the new color + opacity
                if (!imagesetpixel($src_im, $x, $y, $alphacolorxy)) {
                    return false;
                }
            }
        }
        // The image copy
        imagecopy($dst_im, $src_im, $dst_x, $dst_y, $src_x, $src_y, $src_w, $src_h);
    }

    /**
     * save an imageobject to given path.
     *
     * @param resource $oImageToSave
     * @param string   $sSavePath
     */
    protected function SaveHeaderImage($oImageToSave, $sSavePath)
    {
        if ('png' == $this->fieldImgType) {
            imagepng($oImageToSave, $sSavePath);
        } elseif ('jpg' == $this->fieldImgType) {
            imagejpeg($oImageToSave, $sSavePath);
        } elseif ('gif' == $this->fieldImgType) {
            imagegif($oImageToSave, $sSavePath);
        }
        imagedestroy($oImageToSave);
    }

    /**
     * load background image.
     *
     * @return resource|bool
     */
    protected function GetBgImage()
    {
        $Fileinfos = pathinfo(TdbCmsFontImage::BG_IMAGE_FILE_DIR.$this->fieldBackgroundImgFile);
        if ('png' == $Fileinfos['extension'] || 'gif' == $Fileinfos['extension'] || 'jpg' == $Fileinfos['extension'] || 'jpng' == $Fileinfos['extension']) {
            if ('png' == $Fileinfos['extension']) {
                $oBgImage = imagecreatefrompng(TdbCmsFontImage::BG_IMAGE_FILE_DIR.$this->fieldBackgroundImgFile);
            } elseif ('gif' == $Fileinfos['extension']) {
                $oBgImage = imagecreatefromgif(TdbCmsFontImage::BG_IMAGE_FILE_DIR.$this->fieldBackgroundImgFile);
            } elseif ('jpg' == $Fileinfos['extension'] || 'jpng' == $Fileinfos['extension']) {
                $oBgImage = imagecreatefromjpeg(TdbCmsFontImage::BG_IMAGE_FILE_DIR.$this->fieldBackgroundImgFile);
            }

            return $oBgImage;
        } else {
            return false;
        }
    }

    /**
     * Get the dimensions of the new image.
     *
     * @param string $sText
     * @param int    $iMaxWidth
     *
     * @return array
     */
    protected function GetImageSizes($sText, $iMaxWidth = null)
    {
        $aImageSizes = array();
        $aTextSizes = imagettfbbox($this->fieldFontSize, 0, PATH_CMS_FONTS.$this->fieldFontFilename, $sText);
        $aNewTextSizes = $this->convertBoundingBox($aTextSizes);
        if ($this->fieldImgHeight < 0) {
            $aImageSizes['height'] = $aNewTextSizes['height'];
        } else {
            $aImageSizes['height'] = $this->fieldImgHeight;
        }
        if ($this->fieldImgWidth < 0) {
            $aImageSizes['width'] = $aNewTextSizes['width'];
        } else {
            $aImageSizes['width'] = $this->fieldImgWidth;
        }
        if (isset($iMaxWidth)) {
            if ($iMaxWidth < $aImageSizes['width']) {
                $iNewWidth = $this->GetNewImageSizes($sText, $iMaxWidth);
                $aImageSizes['width'] = $iNewWidth;
                $this->iLineHeight = $aImageSizes['height'];
                $aImageSizes['height'] = ($aImageSizes['height'] * count($this->aTextLines)) + $this->sLineHeight;
            }
        }
        if ($this->fieldImgHeight >= 0) {
            if ('top' == $this->fieldFontVerticalAlign) {
                $aImageSizes['fontystart'] = $aNewTextSizes['aboveBasepoint'];
            } elseif ('middle' == $this->fieldFontVerticalAlign) {
                $aImageSizes['fontystart'] = (($this->fieldImgHeight / 2) + ($aNewTextSizes['height'] / 2)) - $aNewTextSizes['belowBasepoint'];
            } elseif ('bottom' == $this->fieldFontVerticalAlign) {
                $aImageSizes['fontystart'] = $aImageSizes['height'] - $aNewTextSizes['belowBasepoint'];
            }
        } else {
            $aImageSizes['fontystart'] = $aNewTextSizes['aboveBasepoint'];
        }
        if ($this->fieldImgWidth >= 0) {
            if ('center' == $this->fieldFontAlign) {
                $aImageSizes['fontxstart'] = ($this->fieldImgWidth / 2) - ($aNewTextSizes['width'] / 2);
            } elseif ('right' == $this->fieldFontAlign) {
                $aImageSizes['fontxstart'] = $this->fieldImgWidth - $aNewTextSizes['rightOfBasepoint'];
            } elseif ('left' == $this->fieldFontAlign) {
                $aImageSizes['fontxstart'] = 0;
            }
        } else {
            $aImageSizes['fontxstart'] = 0;
        }

        return $aImageSizes;
    }

    /**
     * Get new image width from maxwidth.
     *
     * @param string $sText
     * @param int    $iMaxWidth
     *
     * @return int
     */
    protected function GetNewImageSizes($sText, $iMaxWidth)
    {
        $aTextArray = explode(' ', $sText);
        $sLineText = '';
        $aLines = array();
        $bTextToBig = false;
        foreach ($aTextArray as $iIndex => $sWord) {
            $sLineBeforeText = $sLineText;
            $sLineText .= ' '.$sWord;
            $aTextSizes = imagettfbbox($this->fieldFontSize, 0, PATH_CMS_FONTS.$this->fieldFontFilename, $sLineText);
            $aNewTextSizes = $this->convertBoundingBox($aTextSizes);
            if ($aNewTextSizes['width'] > $iMaxWidth) {
                $aLines[] = $sLineBeforeText;
                $sLineText = $sWord;
                $bTextToBig = true;
            }
        }
        $aLines[] = $sLineText;
        if ($bTextToBig) {
            $this->aTextLines = $aLines;

            return $iMaxWidth;
        } else {
            return $aNewTextSizes['width'];
        }
    }

    /**
     * Get text box sizes.
     *
     * @param string $sText
     * @param int    $iMaxWidth
     *
     * @return array
     */
    protected function convertBoundingBox($aImageTTFBox)
    {
        if ($aImageTTFBox[0] >= -1) {
            $sLeftOfBasepoint = abs($aImageTTFBox[0] + 1);
        } else {
            $sLeftOfBasepoint = -abs($aImageTTFBox[0] + 2);
        }
        $sRightOfBasepoint = abs($aImageTTFBox[2] - $aImageTTFBox[0]);
        if ($aImageTTFBox[0] < -1) {
            $sRightOfBasepoint = abs($aImageTTFBox[2]) + abs($aImageTTFBox[0]) - 1;
        }
        $sAboveBasePoint = abs($aImageTTFBox[5] + 1);
        $height = abs($aImageTTFBox[7]) - abs($aImageTTFBox[1]);
        if ($aImageTTFBox[3] > 0) {
            $height = abs($aImageTTFBox[7] - $aImageTTFBox[1]) - 1;
        }
        $width = $sLeftOfBasepoint + $sRightOfBasepoint;
        $sBelowBasePoint = $height - $sAboveBasePoint;

        return array('width' => $width, 'height' => $height, 'leftOfBasepoint' => $sLeftOfBasepoint, 'rightOfBasepoint' => $sRightOfBasepoint, 'aboveBasepoint' => $sAboveBasePoint, 'belowBasepoint' => $sBelowBasePoint);
    }

    /**
     * convert an hex value to an array of RGB values.
     *
     * @param string $sHex
     *
     * @return array|int
     */
    protected function HexToRGB($sHex)
    {
        if (!preg_match('/[0-9a-fA-F]{6}/', $sHex)) {
            echo 'Error : input is not a valid hexadecimal number';

            return 0;
        }

        $rgb = array();
        for ($i = 0; $i < 3; ++$i) {
            $temp = substr($sHex, 2 * $i, 2);
            $rgb[$i] = 16 * hexdec(substr($temp, 0, 1)) + hexdec(substr($temp, 1, 1));
        }

        return $rgb;
    }

    /**
     * delete all generated images and profile dirs.
     */
    public static function ClearCachImages()
    {
        /** @var $oFontImageList TdbCmsFontImageList */
        $oFontImageList = TdbCmsFontImageList::GetList();
        /** @var $oFontImageList TdbCmsFontImage */
        while ($oFontImage = &$oFontImageList->Next()) {
            $sFile = TdbCmsFontImage::FONT_IMAGE_DIR.'/'.$oFontImage->id.'/';
            if (file_exists($sFile)) {
                $d = dir($sFile);
                while (false !== ($entry = $d->read())) {
                    if ('.png' == substr($entry, -4) || '.gif' == substr($entry, -4) || '.jpg' == substr($entry, -4)) {
                        unlink($sFile.$entry);
                    }
                }
                $d->close();
                rmdir($sFile);
            }
        }
    }

    public function UmlWorkaround($text)
    {
        $aReplace = array('ä' => '&#228;', 'ü' => '&#252;', 'ö' => '&#246;', 'Ä' => '&#196;', 'Ü' => '&#220;', 'Ö' => '&#214;', 'ß' => '&#223;');

        return str_replace(array_keys($aReplace), array_values($aReplace), $text);
    }
}
