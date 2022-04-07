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
use ChameleonSystem\CoreBundle\Util\UrlNormalization\UrlNormalizationUtil;
use ChameleonSystem\CoreBundle\DatabaseAccessLayer\DatabaseAccessLayerCmsMedia;

/**
 * handles images and thumbnails of table cms_media including external videos.
 **/
class TCMSImageEndpoint
{
    /**
     * holds the sql record.
     *
     * @var array
     */
    public $aData = array();

    /**
     * id of the image.
     *
     * @var string
     */
    public $id = null;

    /**
     * caches the image type (gif, jpg, png...).
     *
     * @var string
     */
    public $_imageType = null;

    /**
     * caches the image size.
     *
     * @var string
     */
    public $_fileSize = null;

    /**
     * add lightbox tag.
     *
     * @var bool
     */
    public $useLightBox = true;

    /**
     * indicates if the image is a thumbnail object.
     *
     * @var bool
     */
    public $_isThumbnail = false;

    /**
     * add unsharp mask to get sharp images.
     *
     * @var bool
     */
    public $useUnsharpMask = true;

    /**
     * set this video as the big one, so we don`t show the zoom icon and set the width/height to 100%.
     *
     * @var bool
     */
    public $isZoomedVideo = false;

    /**
     * a unique id to allow multiple instances of an image on the same page.
     *
     * @var string
     */
    public $uniqueID = null;

    /**
     * if this static property is set to true the image urls will be forced to http even on https websites.
     *
     * @var bool
     */
    private static $bStatusForceNonSSLURLs = false;

    /**
     * create image object and load from database.
     *
     * @param string|null $id
     */
    public function __construct($id = null)
    {
        if (!is_null($id)) {
            $this->Load($id);
        }
    }

    /**
     * create a new image from the oFile Object.
     *
     * @param TCMSFile                $oFile
     * @param string                  $sCmsMediaCategoryId
     * @param string|null             $sDescription
     * @param string|null             $sPreviewImageId
     * @param IPkgCmsFileManager|null $filemanager
     *
     * @return TCMSImage
     */
    public static function CreateFromFileObject($oFile, $sCmsMediaCategoryId, $sDescription = null, $sPreviewImageId = null, $filemanager = null)
    {
        // first try to load it (imported images get the full path inserted into the metatags
        $sImageId = null;

        if (null === $filemanager) {
            $filemanager = self::getFileManager();
        }

        $oImage = TdbCmsMedia::GetNewInstance();
        if (!$oImage->LoadFromField('metatags', $oFile->sPath)) {
            $cachePath = PATH_CMS_CUSTOMER_DATA.'/mediaImport/';
            $sImgName = $oFile->sFileName; //$aImgLinkParts[count($aImgLinkParts)-1];
            $tmpFileName = $cachePath.'/'.$sImgName;

            $bTargetCreated = false;
            if ($oFile->bIsHTTPResource) {
                if ($imgFileP = @fopen($oFile->sPath, 'rb')) {
                    $imgFileContent = stream_get_contents($imgFileP);
                    $tmpFileName = $cachePath.'/'.$sImgName;
                    $newFile = $filemanager->fopen($tmpFileName, 'wb');
                    $filemanager->fwrite($newFile, $imgFileContent);
                    $filemanager->fclose($newFile);
                    fclose($imgFileP);
                    $bTargetCreated = true;
                }
            } elseif (file_exists($oFile->sPath)) {
                $bTargetCreated = $filemanager->copy($oFile->sPath, $tmpFileName);
            }
            if ($bTargetCreated) {
                $aImageInfos = getimagesize($tmpFileName);
                $iImageSize = filesize($tmpFileName);
                $FileArray = array('name' => $sImgName, 'type' => $aImageInfos[3], 'tmp_name' => $tmpFileName, 'error' => 0, 'size' => $iImageSize);
                if (!is_null($sDescription)) {
                    $FileArray['description'] = $sDescription;
                }
                if (!is_null($sPreviewImageId)) {
                    $FileArray['cms_media_id'] = $sPreviewImageId;
                }
                $FileArray['cms_media_tree_id'] = $sCmsMediaCategoryId;
                $FileArray['metatags'] = $oFile->sPath;
                try {
                    // since the editor deletes the source file, we need to make a backup IF the file was not pulled from https
                    if (!$oFile->bIsHTTPResource) {
                        $filemanager->copy($tmpFileName, $tmpFileName.'.tmp');
                    }
                    $iTableID_Media = TTools::GetCMSTableId('cms_media');
                    $oEditor = new TCMSTableEditorManager();
                    /** @var $oEditor TCMSTableEditorManager */
                    $oEditor->Init($iTableID_Media, null);
                    $oEditor->oTableEditor->SetUploadData($FileArray, true);
                    $oEditor->Save($FileArray);
                    if (!$oFile->bIsHTTPResource) {
                        $filemanager->move($tmpFileName.'.tmp', $tmpFileName);
                    }
                    $sImageId = $oEditor->oTableEditor->oTable->id;
                } catch (Exception $e) {
                    $sImageId = null;
                }
            } else {
                $sImageId = null;
            }
        } else {
            $sImageId = $oImage->id;
        }

        $oImageObject = null;
        if (!is_null($sImageId)) {
            $oImageObject = new TCMSImage();
            /** @var $oImageObject TCMSImage */
            $oImageObject->Load($sImageId);
        }

        return $oImageObject;
    }

    /**
     * Load the image data from the database.
     *
     * @param int $id
     *
     * @return bool
     */
    public function Load($id)
    {
        $bImageLoaded = false;
        $this->id = $id;

        $oCmsMedia = $this->getMediaDataAccessService()->loadMediaFromId($id);
        if (null !== $oCmsMedia && null !== $oCmsMedia->id && false !== $oCmsMedia->sqlData) {
            if (is_array($oCmsMedia->sqlData)) {
                $this->aData = $this->getFieldTranslationUtil()->copyTranslationsToDefaultFields($oCmsMedia->sqlData);
            } else {
                $this->aData = $oCmsMedia->sqlData;
            }
            $bImageLoaded = true;
            $this->SetUID();
        }

        return $bImageLoaded;
    }

    /**
     * return true if ImageMagick is used for conversion.
     *
     * @return bool
     */
    protected function UseImageMagick()
    {
        static $bUseImageMagic = null;
        if (null !== $bUseImageMagic) {
            return $bUseImageMagic;
        }

        if (DISABLE_IMAGEMAGICK) {
            $bUseImageMagic = false;
        } else {
            $bUseImageMagic = false !== TdbCmsConfig::GetInstance()->GetImageMagickObject();
        }

        return $bUseImageMagic;
    }

    /**
     * set this to true if you want image urls with http instead of https URLs
     * don`t forget to reset this property after usage!
     *
     * @var bool $bforceNonSSLURLs
     *
     * @return bool - returns the current state of the static property
     */
    final public static function ForceNonSSLURLs($bforceNonSSLURLs = null)
    {
        if (!is_null($bforceNonSSLURLs) && is_bool($bforceNonSSLURLs)) {
            self::$bStatusForceNonSSLURLs = $bforceNonSSLURLs;
        }

        return self::$bStatusForceNonSSLURLs;
    }

    /**
     * loads the imageMagick class.
     *
     * @return imageMagick
     */
    protected function &GetImageMagicObject()
    {
        static $oImageMagick = null;
        if (is_null($oImageMagick)) {
            $oConfig = &TdbCmsConfig::GetInstance();
            /** @var $oConfig TCMSConfig */
            $oImageMagick = $oConfig->GetImageMagickObject();
        }

        return $oImageMagick;
    }

    /**
     * generates a unique id to allow multiple instances of an image on the same page.
     */
    protected function SetUID()
    {
        $this->uniqueID = md5(uniqid(rand(), true));
    }

    /**
     * returns the full image path
     * If you want the local path - use GetFullLocalPath() instead.
     *
     * @return string
     */
    public function GetFullURL()
    {
        $sImageURL = '';

        if ($this->IsExternalMovie()) {
            $sImageURL = $this->aData['external_video_thumbnail'];
            $oRequest = \ChameleonSystem\CoreBundle\ServiceLocator::get('request_stack')->getCurrentRequest();
            if ($oRequest->isSecure()) {
                $sImageURL = str_replace('http://', 'https://', $sImageURL);
            }
        } else {
            $sBasePath = $this->GetImageUrlPathPrefix();
            $sBaseThumbPath = $sBasePath.'thumbs/';
            $bForceNonSSL = self::ForceNonSSLURLs();

            if (!$this->_isThumbnail) { // full image
                if (isset($this->aData['path'])) {
                    $sImageURL = TGlobal::GetStaticURL($sBasePath.$this->aData['path'], $bForceNonSSL);
                } else {
                    $sImageURL = '';
                }
            } else { // thumbnail
                $sImageURL = TGlobal::GetStaticURL($sBaseThumbPath.$this->GetThumbPathExtension().'/'.$this->aData['path'], $bForceNonSSL);
            }
        }

        // if the URL uses a relative path, then add the domain/protocol
        if ('http://' !== substr($sImageURL, 0, 7) && 'https://' !== substr($sImageURL, 0, 8) && '[{CMSSTATICURL' !== substr($sImageURL, 0, 14)) {
            $oSmartURLData = &TCMSSmartURLData::GetActive();
            $sProtocol = REQUEST_PROTOCOL;
            if (self::ForceNonSSLURLs()) {
                $sProtocol = 'http';
            }

            $sImageURL = $sProtocol.'://'.$oSmartURLData->sDomainName.$sImageURL;
        }

        $sImageURL = $this->addRefreshToken($sImageURL);

        return $sImageURL;
    }

    /**
     * adds a random token to the image url to prevent caching.
     *
     * @param string $plainURL
     *
     * @return string
     */
    protected function addRefreshToken($plainURL)
    {
        $token = (isset($this->aData['refresh_token']) && !empty($this->aData['refresh_token'])) ? ($this->aData['refresh_token']) : ('');

        if ('' !== $token) {
            $sep = '?';
            if (false !== strpos($plainURL, '?')) {
                $sep = '&';
            }
            $plainURL = $plainURL.$sep.'ck='.urlencode($token);
        }

        return $plainURL;
    }

    /**
     * return the SEO path prefix for an image.
     *
     * @return string
     */
    protected function GetImageUrlPathPrefix()
    {
        if (CMS_MEDIA_ENABLE_SEO_URLS) {
            static $aLocalCache = array();
            $aKeys = array('class' => __CLASS__, 'method' => 'GetImageUrlPathPrefix', 'cms_media_tree_id' => $this->aData['cms_media_tree_id']);
            $sPath = '';
            $sKey = TCacheManager::GetKey($aKeys);
            if (false == array_key_exists($sKey, $aLocalCache)) {
                if (!array_key_exists('cms_media_tree_id', $this->aData) || empty($this->aData['cms_media_tree_id'])) {
                    // use the first root category id
                    $query = "SELECT `id` FROM `cms_media_tree` WHERE `parent_id`='' LIMIT 0,1";
                    if ($aTmp = MySqlLegacySupport::getInstance()->fetch_assoc(MySqlLegacySupport::getInstance()->query($query))) {
                        $this->aData['cms_media_tree_id'] = $aTmp['id'];
                    } else {
                        $this->aData['cms_media_tree_id'] = '1';
                    }
                }
                $oCategory = TdbCmsMediaTree::GetNewInstance($this->aData['cms_media_tree_id']);
                if (false !== $oCategory->sqlData) {
                    $sPath = $oCategory->GetPathCache();
                }
                $sPath = $sPath.'/i/';
                $aLocalCache[$sKey] = $sPath;
            } else {
                $sPath = $aLocalCache[$sKey];
            }
        } else {
            $sPath = URL_MEDIA_LIBRARY_PATH;
        }
        if ('/' !== substr($sPath, -1)) {
            $sPath .= '/';
        }

        return $sPath;
    }

    /**
     * Returns the full local image directory path. If you want the URL to access the image - use GetFullURL instead.
     *
     * @return string
     */
    public function GetFullLocalPath()
    {
        $sPath = $this->GetLocalMediaDirectory();
        if ($this->_isThumbnail) {
            $sPath = $this->GetLocalMediaDirectory(true);
        }

        $sPath .= $this->aData['path'];

        return $sPath;
    }

    /**
     * returns the part of the thumb url relative to PATH_MEDIA_LIBRARY_THUMBS.
     *
     * @return string
     */
    protected function GetThumbPathExtension()
    {
        $sPathString = $this->id;
        if (strlen($this->id) < 36) {
            // create md5 of id and use that
            $sPathString = md5($this->id);
        }

        return substr($sPathString, 0, 1).'/'.substr($sPathString, 1, 2);
    }

    /**
     * returns the path to the media files directory.
     *
     * @param bool $bThumbs - set to true if you want the thumbs directory
     *
     * @return string
     */
    protected function GetLocalMediaDirectory($bThumbs = false)
    {
        static $cache = array();
        $isNotFound = !isset($this->aData['id']) || '-1' == $this->aData['id'];
        $key = $bThumbs ? 'thumb' : 'nothumb';
        $key = $key.':'.($isNotFound ? 'found' : 'notfound');
        $thumbPathExtension = '';
        if ($bThumbs) {
            $thumbPathExtension = $this->GetThumbPathExtension();
            $key = $key.':'.$thumbPathExtension;
        }

        if (isset($cache[$key])) {
            return $cache[$key];
        }

        if (!$bThumbs && $isNotFound) {
            $sPath = $_SERVER['DOCUMENT_ROOT'].'/';
        } else {
            $sPath = PATH_MEDIA_LIBRARY;
            if ($bThumbs) {
                $sPath = PATH_MEDIA_LIBRARY_THUMBS;
                // add sub path based on image id
                if ('/' !== substr($sPath, -1)) {
                    $sPath .= '/';
                }
                $sPath .= $thumbPathExtension;
                if (!is_dir($sPath)) {
                    self::getFileManager()->mkdir($sPath, 0777, true);
                }
                $sPath .= '/';
            }
        }

        $cache[$key] = $sPath;

        return $sPath;
    }

    /**
     * creates a thumbnail of fixed size with base image centered with border.
     *
     * @param int    $width
     * @param int    $height
     * @param int    $padding
     * @param string $bgcolor                 - hexadecimal 0-9 a-f
     * @param array  $aEffects
     * @param bool   $bStretchImageToFullsize
     *
     * @return TCMSImage
     */
    public function GetCenteredFixedSizeThumbnail($width, $height, $padding = 0, $bgcolor = 'ffffff', $aEffects = array(), $bStretchImageToFullsize = false)
    {
        $oThumb = null;
        // load connected image file if one exists
        if (is_array($this->aData) && array_key_exists('cms_media_id', $this->aData) && (intval($this->aData['cms_media_id']) >= 1000 || (!is_numeric($this->aData['cms_media_id']) && !empty($this->aData['cms_media_id']))) && $this->aData['cms_media_id'] != $this->aData['id']) {
            $oPreviewImage = new TCMSImage();
            $oPreviewImage->Load($this->aData['cms_media_id']);
            $oThumb = $oPreviewImage->GetCenteredFixedSizeThumbnail($width, $height, $padding, $bgcolor);
            $oThumb->_isThumbnail = true;
        } else {
            // set white if color isn`t a valid hex color
            if (!preg_match('/[0-9a-fA-F]{6}/', $bgcolor)) {
                $bgcolor = 'ffffff';
                $rgb[0] = 255;
                $rgb[1] = 255;
                $rgb[2] = 255;
            }

            $path = '';
            if (!empty($this->aData['path'])) {
                $path = $this->aData['path'];
            }
            $originalFile = $this->GetLocalMediaDirectory().$path;

            $allowThumbing = $this->CheckAllowThumbnailing();

            if ($allowThumbing) {
                // determine how large the thumbnail should be
                $oThumb = $this->InitThumbnailing();
                $iThumbWidth = $width - $padding;
                $iThumbHeight = $height - $padding;
                $this->GetThumbnailProportions($oThumb, $iThumbWidth, $iThumbHeight, $bStretchImageToFullsize); // set width height including padding
                $oThumb->_isThumbnail = true;

                // resize needed
                $sExtensions = 'jpg';
                if ($this->SupportsTransparency() && !$this->CheckImageTransformPngToJpg($oThumb)) {
                    $sExtensions = 'png';
                }
                $sEffectFileNamePart = '_bgcol_'.$bgcolor.'_pad'.$padding;
                $thumbName = $this->GenerateThumbName($width, $height, $sEffectFileNamePart, $sExtensions);
                $thumbPath = $this->GetLocalMediaDirectory(true).$thumbName;

                $oThumb->aData['path'] = $thumbName;

                // check if the thumbnail exists
                if (!file_exists($thumbPath)) {
                    if ($this->UseImageMagick() && 0 == count($aEffects)) {
                        $oImageMagick = &$this->GetImageMagicObject();
                        $oImageMagick->LoadImage($this->GetLocalMediaDirectory().'/'.$this->aData['path'], $this);
                        $oImageMagick->ResizeImage((int) $oThumb->aData['width'], (int) $oThumb->aData['height']);
                        $oImageMagick->CenterImage($width, $height, '#'.$bgcolor);
                        $oImageMagick->SaveToFile($thumbPath);
                        if (0 === filesize($thumbPath)) {
                            unlink($thumbPath);
                        } else {
                            $thumbnailRealPath = realpath($thumbPath);
                            if (false !== $thumbnailRealPath) {
                                $this->thumbnailCreatedHook($thumbnailRealPath, $sExtensions);
                            }
                        }
                    } else {
                        // now we need to resize the actual image
                        $imagePointer = $this->GetThumbnailPointer($oThumb, $aEffects);

                        if (!is_null($imagePointer)) {
                            $rDestImage = imagecreatetruecolor($width, $height);
                            if ($this->SupportsTransparency() && !$this->CheckImageTransformPngToJpg($oThumb)) {
                                imagealphablending($rDestImage, true);
                            }

                            // fetch rgb colors from hex
                            for ($i = 0; $i < 3; ++$i) {
                                $temp = substr($bgcolor, 2 * $i, 2);
                                $rgb[$i] = 16 * hexdec(substr($temp, 0, 1)) + hexdec(substr($temp, 1, 1));
                            }

                            $bgcolor = imagecolorallocate($rDestImage, $rgb[0], $rgb[1], $rgb[2]);
                            imagefilledrectangle($rDestImage, 0, 0, $width, $height, $bgcolor);

                            $iNewpaddingX = round($padding + ((($width - (2 * $padding)) - $oThumb->aData['width']) / 2));
                            $iNewpaddingY = round($padding + ((($height - (2 * $padding)) - $oThumb->aData['height']) / 2));

                            if ($this->SupportsTransparency() && !$this->CheckImageTransformPngToJpg($oThumb)) {
                                TCMSImage::imagecopymerge_alpha($rDestImage, $imagePointer, $iNewpaddingX, $iNewpaddingY, 0, 0, $oThumb->aData['width'], $oThumb->aData['height'], 100);
                            } else {
                                imagecopymerge($rDestImage, $imagePointer, $iNewpaddingX, $iNewpaddingY, 0, 0, $oThumb->aData['width'], $oThumb->aData['height'], 100);
                            }
                            if ($this->SupportsTransparency() && !$this->CheckImageTransformPngToJpg($oThumb)) {
                                imagepng($rDestImage, $thumbPath);
                            } else {
                                $iJPGQuality = $this->GetTransformJPGQuality($oThumb);
                                if (false === $iJPGQuality) {
                                    $iJPGQuality = 100;
                                }
                                imagejpeg($rDestImage, $thumbPath, $iJPGQuality);
                            }
                            if (0 === filesize($thumbPath)) {
                                unlink($thumbPath);
                            } else {
                                $thumbnailRealPath = realpath($thumbPath);
                                if (false !== $thumbnailRealPath) {
                                    $this->thumbnailCreatedHook($thumbnailRealPath, 'png');
                                }
                            }

                            imagedestroy($rDestImage);
                            imagedestroy($imagePointer);
                        }
                    }
                }
                $oThumb->aData['width'] = $width;
                $oThumb->aData['height'] = $height;
            } else {
                if ((!DISABLE_IMAGE_ERRORS_IN_DEVELOPMENT_MODE && _DEVELOPMENT_MODE) || !_DEVELOPMENT_MODE) {
                    trigger_error("Error reading original at: {$originalFile}", E_USER_WARNING);
                }
            }
        }

        return $oThumb;
    }

    /*
     * php imagecopymerge doese not support alpha... so use one provided on php.net
     */
    public static function imagecopymerge_alpha($dst_im, $src_im, $dst_x, $dst_y, $src_x, $src_y, $src_w, $src_h, $pct)
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
     * returns the relative URL of the image e.g. /chameleon/mediapool/1.jpg.
     *
     * @return string
     */
    public function GetRelativeURL()
    {
        if (false === $this->_isThumbnail) {
            $sImageURL = $this->GetImageUrlPathPrefix().$this->aData['path'];
        } else {
            $sImageURL = URL_MEDIA_LIBRARY_THUMBS_PATH.$this->GetThumbPathExtension().'/'.$this->aData['path'];
        }
        $sImageURL = $this->addRefreshToken($sImageURL);

        return $sImageURL;
    }

    /**
     * checks if the filetype allows thumbnailing.
     *
     * @return bool
     */
    public function CheckAllowThumbnailing()
    {
        $allowThumbing = false;

        if ($this->_isThumbnail) {
            $allowThumbing = true; // do not allow thumbnailing a thumbnail because the generated path will be broken (the user should always generate a thumb from the origianl)
            trigger_error('do not generate a thumb from a thumbnail. please generate the thumb from the original image only', E_USER_WARNING);
        } elseif ($this->IsExternalMovie()) {
            $allowThumbing = true;
        } else {
            $path = '';
            if (!empty($this->aData['path'])) {
                $path = $this->aData['path'];
            }
            $originalFile = $this->GetLocalMediaDirectory().$path;

            if (file_exists($originalFile) && is_file($originalFile)) {
                $fileType = $this->GetImageType();
                if ('jpg' == $fileType || 'gif' == $fileType || 'png' == $fileType) {
                    if ($this->CheckThumbnailFileSizeLimit($originalFile)) {
                        $allowThumbing = true;
                    }
                }
            }

            $tmpId = $this->id;

            if (!$allowThumbing) {
                // load error file instead...
                $this->Load(-1);
                $this->_imageType = 'jpg'; // force png for 404 image
                $this->aData['description'] = '404 image not found - ID '.$tmpId;
                $this->aData['path'] = CHAMELEON_404_IMAGE_PATH_BIG;
                $this->aData['width'] = 400;
                $this->aData['height'] = 400;
                $this->aData['time_stamp'] = 0;

                $originalFile = realpath($this->GetLocalMediaDirectory().$this->aData['path']);
                // handle custom 404 image
                if (file_exists($originalFile)) {
                    if (!stristr($originalFile, '/chameleon/blackbox/images/noImage')) {
                        $aImageData = getimagesize($originalFile);
                        $this->aData['width'] = $aImageData[0];
                        $this->aData['height'] = $aImageData[1];
                    }

                    $fileType = $this->GetImageType();
                    if ('jpg' == $fileType || 'gif' == $fileType || 'png' == $fileType) {
                        $allowThumbing = true;
                    }
                }
            }
        }

        return $allowThumbing;
    }

    /**
     * Creates an reflection effect for a given image resource.
     *
     * @param resource $rImageSource: The image resource (e.g. created via
     *                                imagecreatefromjpeg)
     * @param int      $iPercentage:  The percentage of the height of the
     *                                original image where the reflection effect should take place. For
     *                                example: if you put in 50 (50%) here, you'll get an mirrored reflection
     *                                that is 50% as high as your original image. So if your original image
     *                                was 100 pixel high - your image including mirrored reflection (which
     *                                will be returned will now be 150 pixel. The higher the percentage,
     *                                the longer the alpha gradient.
     * @param bool     $bImageline    - renders a line for the
     * @param int      $iAlphaStart   (possible values 0-126)
     * @param int      $iAlphaEnd     (possible values 1-127)
     *
     * @return resource
     */
    public function GetReflectedImage($rImageSource, $iPercentage, $bImageline = true, $iAlphaStart = 0, $iAlphaEnd = 127)
    {
        $bRenderReflectionWithImageMagick = false;
        /*
        if($this->UseImageMagick($rImageSource)) {
          $oImageMagick = $this->GetImageMagicObject();

          $sTempImage = tempnam(PATH_CMS_CUSTOMER_DATA."/tmp",'tempImageForIMagickReflection_');
          imagepng($rImageSource,$sTempImage,100);

          $oImageMagick->LoadImage($sTempImage,$this);
          //if($oImageMagick->bUsePHPLibrary)
          $bRenderReflectionWithImageMagick = true;
        }*/

        if ($bRenderReflectionWithImageMagick) {
            $oImageMagick = $this->GetImageMagicObject();
            $rImageReflection = $oImageMagick->AddReflection($bImageline, $iPercentage);
        } else {
            $iWidth = imagesx($rImageSource);
            $iHeight = imagesy($rImageSource);
            $dMultiplicator = $iPercentage / 100;
            $iHeightReflection = round($iHeight * $dMultiplicator);
            $rImageReflection = imagecreatetruecolor($iWidth, $iHeight + $iHeightReflection); // create new image with height of base image + reflection
            if ($bImageline) {
                $iReflectedStart = $iHeight;
            } else {
                $iReflectedStart = $iHeight - 1;
            } // optional: remove 1 pixel baseline
            imagealphablending($rImageReflection, false);
            $color = imagecolortransparent($rImageReflection, imagecolorallocatealpha($rImageReflection, 0, 0, 0, 127));
            imagefill($rImageReflection, 0, 0, $color);
            imagecopyresampled($rImageReflection, $rImageSource, 0, $iHeight, 0, $iReflectedStart, $iWidth, $iHeightReflection, $iWidth, $iHeightReflection * -1);
            imagecopyresampled($rImageReflection, $rImageSource, 0, 0, 0, 0, $iWidth, $iHeight, $iWidth, $iHeight);
            imagedestroy($rImageSource);
            $iAlphaRange = $iAlphaEnd - $iAlphaStart;
            $iAlphaRangeStep = $iAlphaRange / $iHeightReflection;
            // Creating alpha gradient
            imagesavealpha($rImageReflection, true);
            if (function_exists('imagelayereffect')) {
                imagelayereffect($rImageReflection, IMG_EFFECT_OVERLAY);
            }
            for ($y = 0; $y < $iHeightReflection; ++$y) {
                $iBrightness = round(($iAlphaRangeStep * $y) + $iAlphaStart);
                $iColor = imagecolorallocatealpha($rImageReflection, 127, 127, 127, $iBrightness);
                imageline($rImageReflection, 0, $y + $iHeight, $iWidth - 1, $y + $iHeight, $iColor);
            }
        }

        return $rImageReflection;
    }

    /**
     * Applies the current portal's watermark image to the given image.
     *
     * @param resource $rImageSource
     *
     * @return resource $rProcessedImage
     */
    protected function ApplyWatermark($rImageSource)
    {
        $activePortal = $this->getPortalDomainService()->getActivePortal();
        if (null === $activePortal) {
            return $rImageSource;
        }
        $oWaterMarkLogo = $activePortal->GetImage(0, 'watermark_logo');
        /** @var $oWaterMarkLogo TCMSImage */
        if (!is_null($oWaterMarkLogo)) {
            $sFileName = $oWaterMarkLogo->GetFullLocalPath();
            if (is_file($sFileName)) {
                $sFileType = strtolower(pathinfo($sFileName, PATHINFO_EXTENSION));
                $rImageWatermark = false;
                switch ($sFileType) {
                    case 'png':
                        $rImageWatermark = imagecreatefrompng($sFileName);
                        break;
                    case 'gif':
                        $rImageWatermark = imagecreatefromgif($sFileName);
                        break;
                }
                if (false != $rImageWatermark) {
                    imagealphablending($rImageSource, true);
                    imagealphablending($rImageWatermark, true);
                    $iImageSourceWidth = imagesx($rImageSource);
                    $iImageSourceHeight = imagesy($rImageSource);
                    $iImageWaterMarkWidth = imagesx($rImageWatermark);
                    $iImageWaterMarkHeight = imagesy($rImageWatermark);
                    $iImageWaterMarkWidthOriginal = $iImageWaterMarkWidth;
                    $iImageWaterMarkHeightOriginal = $iImageWaterMarkHeight;

                    $iMaxWatermarkWidthInPercent = 75;
                    $iMaxWatermarkWidthInPixel = round($iImageSourceWidth / 100 * $iMaxWatermarkWidthInPercent);

                    if ($iImageWaterMarkWidth > $iMaxWatermarkWidthInPixel) {
                        $dScaleFactor = $iMaxWatermarkWidthInPixel / $iImageWaterMarkWidth;
                        $iMaxWatermarkHeightInPixel = round($iImageWaterMarkHeight * $dScaleFactor);
                        $iImageWaterMarkHeight = $iMaxWatermarkHeightInPixel;
                        $iImageWaterMarkWidth = $iMaxWatermarkWidthInPixel;
                    }

                    $destX = (($iImageSourceWidth / 2) - ($iImageWaterMarkWidth / 2));
                    $destY = (($iImageSourceHeight - (round($iImageSourceHeight / 10) + $iImageWaterMarkHeight)));
                    imagecopyresampled($rImageSource, $rImageWatermark, $destX, $destY, 0, 0, $iImageWaterMarkWidth, $iImageWaterMarkHeight, $iImageWaterMarkWidthOriginal, $iImageWaterMarkHeightOriginal);
                }
            }
        }

        return $rImageSource;
    }

    /**
     * returns a png image with rounded corners.
     *
     * @param resource $rImageSource
     * @param int      $iCornerRadius
     * @param string   $sBackgroundColor - hex color
     *
     * @return resource
     */
    protected function RoundCornersOfImage($rImageSource, $iCornerRadius, $sBackgroundColor = 'FFFFFF')
    {
        $iWidth = imagesx($rImageSource);
        $iHeight = imagesy($rImageSource);

        $iFactor = 2;
        $iScaledVersionWidth = $iWidth * $iFactor;
        $iScaledVersionHeight = $iHeight * $iFactor;
        $iCornerRadius = $iCornerRadius * $iFactor;
        $srcImage = imagecreatetruecolor($iScaledVersionWidth, $iScaledVersionHeight);
        if (function_exists('imageantialias')) {
            imageantialias($srcImage, true);
        }
        imagecopyresampled($srcImage, $rImageSource, 0, 0, 0, 0, $iScaledVersionWidth, $iScaledVersionHeight, $iWidth, $iHeight);
        unset($rImageSource);
        $rImageSource = $srcImage;

        $corner_image = imagecreatetruecolor($iCornerRadius * 2, $iCornerRadius * 2);
        $colour = $sBackgroundColor;

        $clear_colour = imagecolorallocatealpha($corner_image, 0, 0, 0, 0);
        $solid_colour = imagecolorallocatealpha($corner_image, hexdec(substr($colour, 0, 2)), hexdec(substr($colour, 2, 2)), hexdec(substr($colour, 4, 2)), 127);

        imagecolortransparent($corner_image, $clear_colour);
        imagefill($corner_image, 0, 0, $solid_colour);
        imagefilledellipse($corner_image, $iCornerRadius, $iCornerRadius, $iCornerRadius * 2, $iCornerRadius * 2, $clear_colour);
        imagecopymerge($rImageSource, $corner_image, 0, 0, 0, 0, $iCornerRadius, $iCornerRadius, 100);
        imagecopymerge($rImageSource, $corner_image, $iScaledVersionWidth - $iCornerRadius, 0, $iCornerRadius, 0, $iCornerRadius, $iCornerRadius, 100);
        imagecopymerge($rImageSource, $corner_image, 0, $iScaledVersionHeight - $iCornerRadius, 0, $iCornerRadius, $iCornerRadius, $iCornerRadius, 100);

        imagecopymerge($rImageSource, $corner_image, $iScaledVersionWidth - $iCornerRadius, $iScaledVersionHeight - $iCornerRadius, $iCornerRadius, $iCornerRadius, $iCornerRadius, $iCornerRadius, 100);

        $destImage = imagecreatetruecolor($iWidth, $iHeight);
        if (function_exists('imageantialias')) {
            imageantialias($destImage, true);
        }

        imagecopyresampled($destImage, $rImageSource, 0, 0, 0, 0, $iWidth, $iHeight, $iScaledVersionWidth, $iScaledVersionHeight);

        return $destImage;
    }

    public function ApplyEffectHook($aEffects, &$oThumb)
    {
        return $aEffects;
    }

    /**
     * creates an GD Lib Image Object and resizes image to the thumbnail width height
     * $oThumb->aData['width'] / $oThumb->aData['height'].
     *
     * @param TCMSImage $oThumb
     * @param array     $aEffects: submit a list of effects to apply to the thumbnail.
     *                             available effects are:
     *                             - reflect => array(bImageline=>true) // second parameter tells the effect if it should render a tiny black line between image and reflection or not
     *                             - round => array(iCornerRadius=>20, sBackgroundColor=>FFFFFF) // second parameter is the radius in pixel, third parameter is background-color in hex
     *
     * @return resource
     */
    public function GetThumbnailPointer($oThumb, $aEffects = array())
    {
        $aEffects = $this->ApplyEffectHook($aEffects, $oThumb);
        // now we need to resize the current image
        $image_p = imagecreatetruecolor($oThumb->aData['width'], $oThumb->aData['height']);

        if ($this->SupportsTransparency()) {
            imagealphablending($image_p, false);
            imagesavealpha($image_p, true);
        }
        $image = null;

        $imageType = $this->GetImageType();

        if ('jpg' == $imageType) {
            $image = imagecreatefromjpeg($this->GetLocalMediaDirectory().$this->aData['path']);
        } elseif ('gif' == $imageType) {
            $image = imagecreatefromgif($this->GetLocalMediaDirectory().$this->aData['path']);
        } elseif ('png' == $imageType) {
            $image = imagecreatefrompng($this->GetLocalMediaDirectory().$this->aData['path']);
            imagealphablending($image, true);
        }

        if (!is_null($image)) {
            if ($this->SupportsTransparency()) {
                imagecopyresampled($image_p, $image, 0, 0, 0, 0, $oThumb->aData['width'], $oThumb->aData['height'], $this->aData['width'], $this->aData['height']);
            } else {
                $this->fastimagecopyresampled($image_p, $image, 0, 0, 0, 0, $oThumb->aData['width'], $oThumb->aData['height'], $this->aData['width'], $this->aData['height'], 3);
            }

            if (true === $this->useUnsharpMask && ('jpg' == $this->GetImageType() || 'png' == $this->GetImageType())) {
                $image_p = $this->UnsharpMask($image_p);
            }

            // Applying effects after resampling for performance reasons
            foreach ($aEffects as $sKeyName => $sEffect) {
                if (!is_array($sEffect)) {
                    $sKeyName = $sEffect;
                }
                switch ($sKeyName) {
                    case 'reflect':
                        $bImageline = true;
                        $iPercentage = 25;
                        $iAlphaStart = 0;
                        $iAlphaEnd = 127;
                        if (is_array($sEffect)) {
                            if (array_key_exists('bImageline', $sEffect)) {
                                $bImageline = $sEffect['bImageline'];
                            }
                            if (array_key_exists('iPercentage', $sEffect)) {
                                $iPercentage = $sEffect['iPercentage'];
                            }
                            if (array_key_exists('iAlphaStart', $sEffect)) {
                                $iAlphaStart = $sEffect['iAlphaStart'];
                            }
                            if (array_key_exists('iAlphaEnd', $sEffect)) {
                                $iAlphaEnd = $sEffect['iAlphaEnd'];
                            }
                        }
                        $image_p = $this->GetReflectedImage($image_p, $iPercentage, $bImageline, $iAlphaStart, $iAlphaEnd);
                        break;
                    case 'round':
                        $iRadius = 15;
                        $sBackgroundColor = 'FFFFFF';
                        if (is_array($sEffect) && array_key_exists('iCornerRadius', $sEffect)) {
                            $iRadius = $sEffect['iCornerRadius'];
                        }
                        if (array_key_exists('sBackgroundColor', $sEffect)) {
                            $sBackgroundColor = str_replace('#', '', $sEffect['sBackgroundColor']);
                        }
                        $image_p = $this->RoundCornersOfImage($image_p, $iRadius, $sBackgroundColor);
                        break;
                    case 'watermark':
                        $image_p = $this->ApplyWatermark($image_p);
                        break;
                }
            }
        }

        return $image_p;
    }

    /**
     * return true if the image type supports transparency (gif/png).
     *
     * @return bool
     */
    public function SupportsTransparency()
    {
        $sFileType = $this->GetImageType();

        return 'png' == $sFileType || 'gif' == $sFileType;
    }

    /**
     * initialises a new TCMSImage object for the thumbnail.
     *
     * @return TCMSImage
     */
    public function InitThumbnailing()
    {
        $oThumb = new TCMSImage();
        $oThumb->aData = $this->aData;
        $oThumb->aData['width'] = 0;
        $oThumb->aData['height'] = 0;
        $oThumb->id = $this->id;

        return $oThumb;
    }

    /**
     * return the real image size (check file property).
     *
     * @return array(x=>,y=>)
     */
    public function GetRealImageSize()
    {
        $path = $this->GetLocalMediaDirectory().$this->aData['path'];
        $size = getimagesize($path);

        return array('x' => $size[0], 'y' => $size[1]);
    }

    /**
     * calculates the new dimensions for the thumbnail.
     *
     * @param TCMSImage $oThumb
     * @param int       $maxWidth
     * @param int       $maxHeight
     * @param bool      $bStretchImageToFullsize
     *
     * @return bool
     */
    protected function GetThumbnailProportions(&$oThumb, $maxWidth, $maxHeight, $bStretchImageToFullsize = false)
    {
        $returnVal = false;
        if (0 == $this->aData['width'] || 0 == $this->aData['height']) {
            // fetch real size from file
            $path = $this->GetLocalMediaDirectory().$this->aData['path'];

            if (file_exists($path)) {
                $size = getimagesize($path);
                $this->aData['width'] = $size[0];
                $this->aData['height'] = $size[1];
            }
        }
        $widthConstraint = 0;
        if ($maxWidth > 0) {
            $widthConstraint = (($this->aData['width'] / $maxWidth));
        }
        $heightConstraint = 0;
        if ($maxHeight > 0) {
            $heightConstraint = (($this->aData['height'] / $maxHeight));
        }

        if (false == $bStretchImageToFullsize && $this->aData['width'] <= $maxWidth && $this->aData['height'] <= $maxHeight) {
            // no resize needed...
            $oThumb->aData['width'] = $this->aData['width'];
            $oThumb->aData['height'] = $this->aData['height'];
        } elseif ($widthConstraint > $heightConstraint || 0 == $heightConstraint) {
            // max width counts
            $oThumb->aData['width'] = $maxWidth;
            $oThumb->aData['height'] = round(($this->aData['height'] / $this->aData['width']) * $maxWidth);
        } else {
            // max height counts
            $oThumb->aData['height'] = $maxHeight;
            $oThumb->aData['width'] = round(($this->aData['width'] / $this->aData['height']) * $maxHeight);
        }

        // make sure that width and height do not fall below 1
        if ($oThumb->aData['width'] < 1) {
            $oThumb->aData['width'] = 1;
        }
        if ($oThumb->aData['height'] < 1) {
            $oThumb->aData['height'] = 1;
        }

        if ($oThumb->aData['width'] != $this->aData['width'] || $oThumb->aData['height'] != $this->aData['height'] || $this->IsExternalMovie()) {
            $oThumb->_isThumbnail = true;
            $returnVal = true;
        }

        return $returnVal;
    }

    /**
     * if a thumbnail for the image does not exist, we will create one.
     * the thumbnail will be scaled to be at most maxWith and maxHeight
     * (proportion is kept). if you want to ignore one dimension, then just set it
     * to null.
     * will return the path to the thumbnail.
     *
     * @param int   $maxWidth
     * @param int   $maxHeight       - defaults to 2000 so maxWidth value is enough info for resizing
     * @param bool  $usePreviewImage - if set to true then the thumbnail will be generated using the image under cms_media_id
     * @param array $aEffects:       submit a list of effects to apply to the thumbnail. See method GetThumbnailPointer for available effects
     *
     * @return TCMSImage
     */
    public function GetThumbnail($maxWidth, $maxHeight = 2000, $usePreviewImage = true, $aEffects = array())
    {
        $oThumb = null;

        // load connected image file if one exists
        if ($usePreviewImage && is_array($this->aData) && array_key_exists('cms_media_id', $this->aData) && (intval($this->aData['cms_media_id']) >= 1000 || (!is_numeric($this->aData['cms_media_id']) && !empty($this->aData['cms_media_id']))) && ($this->aData['cms_media_id'] != $this->aData['id'] && !empty($this->aData['cms_media_id']))) {
            $oPreviewImage = new TCMSImage();
            $oPreviewImage->Load($this->aData['cms_media_id']);
            $oThumb = $oPreviewImage->GetThumbnail($maxWidth, $maxHeight);
        } else { // standard behaviour... generate thumbnail
            $allowThumbing = $this->CheckAllowThumbnailing();

            $path = '';
            if (!empty($this->aData['path'])) {
                $path = $this->aData['path'];
            }
            $originalFile = realpath($this->GetLocalMediaDirectory().$path);

            if ($allowThumbing) {
                // determine how large the thumbnail should be
                $oThumb = $this->InitThumbnailing();
                $thumbnailNeeded = $this->GetThumbnailProportions($oThumb, $maxWidth, $maxHeight);

                $bTransFormToJPG = $this->CheckImageTransformPngToJpg($oThumb);
                if ($thumbnailNeeded || (count($aEffects) > 0) || $bTransFormToJPG) {
                    $oThumb->_isThumbnail = true;

                    if (!$this->IsExternalMovie()) {
                        // resize needed
                        $iThumbWidth = $oThumb->aData['width'];
                        $iThumbHeight = $oThumb->aData['height'];

                        $sOriginalExtension = 'jpg';
                        if ($this->SupportsTransparency()) {
                            $sOriginalExtension = 'png';
                        }
                        $sEffectFileNamePart = '';
                        if (count($aEffects) > 0) {
                            $sEffectFileNamePart = '-'.md5(serialize($aEffects)); // because the paramaters may lead to very long filenames we shorten them to md5
                            $sOriginalExtension = 'png';
                        }
                        if ('jpg' != strtolower($sOriginalExtension) && 'jpeg' != strtolower($sOriginalExtension) && $bTransFormToJPG) {
                            $sOriginalExtension = 'jpg';
                        }

                        $thumbName = $this->GenerateThumbName($iThumbWidth, $iThumbHeight, $sEffectFileNamePart, $sOriginalExtension);
                        $thumbPath = $this->GetLocalMediaDirectory(true).$thumbName;

                        $oThumb->aData['path'] = $thumbName;

                        // check if the thumbnail exists
                        if (!file_exists($thumbPath)) {
                            //define what image effects are supported by imagemagick
                            $aSupportedEffectsUsingImageMagick = array('round');

                            $bAllImageMagickEffectsSupported = true;
                            //check if all needed effects are supported by imagemagic
                            foreach ($aEffects as $sEffectName => $aEffectParams) {
                                if (!is_array($aEffectParams)) {
                                    $sEffectName = $aEffectParams;
                                }
                                if (!in_array($sEffectName, $aSupportedEffectsUsingImageMagick)) {
                                    $bAllImageMagickEffectsSupported = false;
                                }
                            }

                            if ($this->UseImageMagick() && ($bAllImageMagickEffectsSupported || (($this->aData['width'] * $this->aData['height']) > (1200 * 800)))) {
                                $this->ResizeImageUsingImageMagick($originalFile, $thumbPath, $iThumbWidth, $iThumbHeight, $aEffects);
                            } else {
                                // now we need to resize the actual image
                                $image_p = $this->GetThumbnailPointer($oThumb, $aEffects);
                                $this->CreateThumbnail($image_p, $sOriginalExtension, $thumbPath, $aEffects, $oThumb);
                            }
                        }
                    }
                } else {
                    // no resize needed... return original
                    $oThumb = clone $this;
                }
            } else {
                if ((!DISABLE_IMAGE_ERRORS_IN_DEVELOPMENT_MODE && _DEVELOPMENT_MODE) || !_DEVELOPMENT_MODE) {
                    trigger_error("Error reading original at: {$originalFile}", E_USER_WARNING);
                }
                $oThumb = clone $this;
                $oTmpThumb = clone $this;
                $this->GetThumbnailProportions($oTmpThumb, $maxWidth, $maxHeight);
                $oThumb->aData['width'] = $oTmpThumb->aData['width'];
                $oThumb->aData['height'] = $oTmpThumb->aData['height'];
            }
        }

        return $oThumb;
    }

    /**
     * Checks if png thumbnail should be transform to jpg.
     * Check with imagemagick configured in cms config.
     *
     * @param TCMSImage $oThumb
     *
     * @return bool
     */
    protected function CheckImageTransformPngToJpg($oThumb)
    {
        $bCheckImageTransformPngToJpg = false;
        if ($this->SupportsTransparency() && $oThumb) {
            TdbCmsConfigImagemagick::SetEnableEffects(true);
            $oImageMagickConfig = TdbCmsConfigImagemagick::GetActiveInstance($oThumb->aData['width'], $oThumb->aData['height']);
            if (!is_null($oImageMagickConfig) && $oImageMagickConfig->fieldForceJpeg) {
                $bCheckImageTransformPngToJpg = true;
            }
        } else {
            $bCheckImageTransformPngToJpg = true;
        }

        return $bCheckImageTransformPngToJpg;
    }

    /**
     * Get transform jpg quality from imagmagick configuration.
     *
     * @param TCMSImage $oThumb
     *
     * @return bool|string
     */
    protected function GetTransformJPGQuality($oThumb)
    {
        $iQuality = false;
        if ($oThumb) {
            TCmsConfigImagemagick::SetEnableEffects(true);
            $oImageMagickConfig = TdbCmsConfigImagemagick::GetActiveInstance($oThumb->aData['width'], $oThumb->aData['height']);
            if ($oImageMagickConfig && $oImageMagickConfig->fieldForceJpeg) {
                $iQuality = $oImageMagickConfig->fieldQuality;
            }
        }

        return $iQuality;
    }

    /**
     * generates a filename for the thumbnail
     * the description is used for the filename for SEO reasons and is cutted by 150 chars
     * non allowed characters are stripped.
     *
     * @param int    $iThumbWidth
     * @param int    $iThumbHeight
     * @param string $sEffectFileNamePart
     * @param string $sOriginalExtension
     *
     * @return string
     */
    protected function GenerateThumbName($iThumbWidth, $iThumbHeight, $sEffectFileNamePart, $sOriginalExtension)
    {
        $sFileName = trim($this->aData['description']);
        if (!empty($sFileName)) {
            $sFileName = TTools::StripTextWordSave(150, $this->aData['description']);
        }
        $sIDPart = '';
        $sMD5Parts = $iThumbWidth.'x'.$iThumbHeight.$sEffectFileNamePart.$this->aData['time_stamp'];

        // to prevent masses of error images of the same size we add the image id only on real ids to the url and md5 key
        if (CHAMELEON_404_IMAGE_PATH_SMALL !== $this->aData['path'] && CHAMELEON_404_IMAGE_PATH_BIG !== $this->aData['path']) {
            $sIDPart = '-ID'.$this->aData['cmsident'];
            $sMD5Parts = $this->id.'_'.$sMD5Parts;
        }

        $sFileName = TTools::sanitizeFilename($this->getUrlNormalizationUtil()->normalizeUrl($sFileName)).'_'.$iThumbWidth.'x'.$iThumbHeight.$sIDPart.'-'.md5($sMD5Parts).'.'.$sOriginalExtension;

        return $sFileName;
    }

    /**
     * creates the thumbnail.
     *
     * @param resource $image_p
     * @param string   $sOriginalExtension
     * @param string   $thumbPath
     * @param array    $aEffects
     */
    protected function CreateThumbnail($image_p, $sOriginalExtension, $thumbPath, $aEffects, $oThumb = null)
    {
        $iJpegQuality = 100;
        $iTransformJpegQuality = $this->GetTransformJPGQuality($oThumb);
        if (false !== $iTransformJpegQuality) {
            $iJpegQuality = $iTransformJpegQuality;
        } else {
            if (defined('JPEG_IMAGE_QUALITY') && is_numeric(JPEG_IMAGE_QUALITY)) {
                $iJpegQuality = JPEG_IMAGE_QUALITY;
            }
        }
        if (!is_null($image_p)) {
            if ('png' == $sOriginalExtension || count($aEffects) > 0) {
                $sOriginalExtension = 'png';
                imagepng($image_p, $thumbPath);
            } elseif ('jpg' == $sOriginalExtension) {
                imagejpeg($image_p, $thumbPath, $iJpegQuality);
            } else {
                imagejpeg($image_p, $thumbPath, $iJpegQuality);
            }
            $thumbnailCreatedType = 'jpg';
            if ('png' == $sOriginalExtension) {
                $thumbnailCreatedType = 'png';
            }
            imagedestroy($image_p);
            if (0 === filesize($thumbPath)) {
                unlink($thumbPath);
            } else {
                $thumbnailRealPath = realpath($thumbPath);
                if (false !== $thumbnailRealPath) {
                    $this->thumbnailCreatedHook($thumbnailRealPath, $thumbnailCreatedType);
                }
            }
        }
    }

    /**
     * resizes image using the imageMagick library.
     *
     * @param string $sSourceFilePath
     * @param string $sTargetFilePath
     * @param int    $iThumbWidth
     * @param int    $iThumbHeight
     * @param array  $aEffects
     */
    protected function ResizeImageUsingImageMagick($sSourceFilePath, $sTargetFilePath, $iThumbWidth, $iThumbHeight, $aEffects = array())
    {
        $oImageMagick = &$this->GetImageMagicObject();
        $oImageMagick->LoadImage($sSourceFilePath, $this);
        $oImageMagick->ResizeImage($iThumbWidth, $iThumbHeight);
        foreach ($aEffects as $sEffectName => $aEffectParams) {
            if ('round' == $sEffectName) {
                $iRadius = 15;
                if (is_array($aEffectParams) && array_key_exists('iCornerRadius', $aEffectParams)) {
                    $iRadius = $aEffectParams['iCornerRadius'];
                }
                $oImageMagick->AddRoundedCorners($iRadius);
            }
            if ('reflect' == $sEffectName) {
                $bImageline = true;
                $iPercentage = 25;
                if (is_array($aEffectParams) && array_key_exists('iPercentage', $aEffectParams)) {
                    $iPercentage = $aEffectParams['iPercentage'];
                }
                if (is_array($aEffectParams) && array_key_exists('bImageline', $aEffectParams)) {
                    $bImageline = $aEffectParams['bImageline'];
                }
                $oImageMagick->AddReflection($bImageline, $iPercentage);
            }
        }

        $oImageMagick->SaveToFile($sTargetFilePath);
        if ($oImageMagick->bHasErrors) {
            if (file_exists($sTargetFilePath)) {
                unlink($sTargetFilePath);
            }
            if (is_array($oImageMagick->aErrorMessages)) {
                foreach ($oImageMagick->aErrorMessages as $sErrorMessage) {
                    if ((!DISABLE_IMAGE_ERRORS_IN_DEVELOPMENT_MODE && _DEVELOPMENT_MODE) || !_DEVELOPMENT_MODE) {
                        trigger_error($sErrorMessage, E_USER_WARNING);
                    }
                }
            } else {
                if ((!DISABLE_IMAGE_ERRORS_IN_DEVELOPMENT_MODE && _DEVELOPMENT_MODE) || !_DEVELOPMENT_MODE) {
                    trigger_error($oImageMagick->aErrorMessages, E_USER_WARNING);
                }
            }
        } else {
            if (0 === filesize($sTargetFilePath)) {
                unlink($sTargetFilePath);
            } else {
                $thumbnailType = strtolower(substr($sTargetFilePath, -3));
                $thumbnailRealPath = realpath($sTargetFilePath);
                if (false !== $thumbnailRealPath) {
                    $this->thumbnailCreatedHook($thumbnailRealPath, $thumbnailType);
                }
            }
        }
    }

    /**
     * returns thumbnail object forced to the given
     * dimensions (by trim, not by distort).
     *
     * @param int  $iMaxWidth
     * @param int  $iMaxHeight
     * @param bool $bCenter
     *
     * @return TCMSImage
     */
    public function GetForcedSizeThumbnail($iMaxWidth, $iMaxHeight, $bCenter = true)
    {
        $oThumb = null;
        $allowThumbing = $this->CheckAllowThumbnailing();

        if ($allowThumbing) {
            // determine how large the thumbnail should be

            if (0 == $this->aData['width'] || 0 == $this->aData['height']) {
                // fetch real size from file
                $path = $this->GetLocalMediaDirectory().$this->aData['path'];

                if (file_exists($path)) {
                    $size = getimagesize($path);
                    $this->aData['width'] = $size[0];
                    $this->aData['height'] = $size[1];
                }
            }

            $widthConstraint = (($this->aData['width'] / $iMaxWidth));
            $heightConstraint = (($this->aData['height'] / $iMaxHeight));

            $x = 0;
            $y = 0;

            if ($widthConstraint > $heightConstraint || 0 == $heightConstraint) {
                // max width counts
                $height = $iMaxHeight;
                $width = round(($this->aData['width'] / $this->aData['height']) * $iMaxHeight);
                if ($bCenter) {
                    $x = ceil(($width - $iMaxWidth) / 2);
                }
            } else {
                // max height counts
                $width = $iMaxWidth;
                $height = round(($this->aData['height'] / $this->aData['width']) * $iMaxWidth);
                if ($bCenter) {
                    $y = ceil(($height - $iMaxHeight) / 2);
                }
            }

            // get thumbnail object
            $oThumb = $this->InitThumbnailing();
            $oThumb->_isThumbnail = true;
            $oThumb->aData['width'] = $width;
            $oThumb->aData['height'] = $height;

            $sOriginalExtension = 'jpg';
            if ($this->SupportsTransparency()) {
                $sOriginalExtension = 'png';
            }
            $sEffectFileNamePart = '-'.md5('square');
            $thumbName = $this->GenerateThumbName($iMaxWidth, $iMaxHeight, $sEffectFileNamePart, $sOriginalExtension);
            $thumbPath = $this->GetLocalMediaDirectory(true).$thumbName;

            // set new squaresized thumbnail name
            $oThumb->aData['path'] = $thumbName;

            // check if the thumbnail exists
            if (!file_exists($thumbPath)) {
                if ($this->UseImageMagick()) {
                    $oImageMagick = &$this->GetImageMagicObject();
                    $oImageMagick->LoadImage($this->GetLocalMediaDirectory().'/'.$this->aData['path'], $this);
                    $oImageMagick->ResizeImage($width, $height);
                    $oImageMagick->CropImage($iMaxWidth, $iMaxHeight, $bCenter);
                    if (false === $oImageMagick->SaveToFile($thumbPath)) {
                        return $oThumb;
                    }

                    if (0 === filesize($thumbPath)) {
                        unlink($thumbPath);
                    } else {
                        $thumbnailRealPath = realpath($thumbPath);
                        if (false !== $thumbnailRealPath) {
                            $this->thumbnailCreatedHook($thumbnailRealPath, $sOriginalExtension);
                        }
                    }
                } else {
                    $uncuttedImagePointer = $this->GetThumbnailPointer($oThumb);
                    // now we need to resize the actual image
                    $image_p = imagecreatetruecolor($iMaxWidth, $iMaxHeight);

                    if (!is_null($uncuttedImagePointer)) {
                        imagecopy($image_p, $uncuttedImagePointer, 0, 0, $x, $y, $width, $height);

                        if (true === $this->useUnsharpMask || ('jpg' == $this->GetImageType() && 'png' == $this->GetImageType())) {
                            $image_p = $this->UnsharpMask($image_p);
                        }

                        $iJPGQuality = $this->GetTransformJPGQuality($oThumb);
                        if (false === $iJPGQuality) {
                            $iJPGQuality = 100;
                        }
                        imagejpeg($image_p, $thumbPath, $iJPGQuality);
                        if (0 === filesize($thumbPath)) {
                            unlink($thumbPath);
                        } else {
                            $thumbnailRealPath = realpath($thumbPath);
                            if (false !== $thumbnailRealPath) {
                                $this->thumbnailCreatedHook($thumbnailRealPath, 'jpg');
                            }
                        }
                        imagedestroy($image_p);

                        // set new dimension
                        $oThumb->aData['width'] = $iMaxWidth;
                        $oThumb->aData['height'] = $iMaxHeight;
                    }
                }
            }
        } else {
            $oThumb = clone $this;
            $oTmpThumb = clone $this;
            $this->GetThumbnailProportions($oTmpThumb, $iMaxWidth * 2, $iMaxHeight);
            $oThumb->aData['width'] = $oTmpThumb->aData['width'];
            $oThumb->aData['height'] = $oTmpThumb->aData['height'];
        }

        return $oThumb;
    }

    /**
     * returns thumbnail object with square size.
     *
     * @param int $maxThumbWidthHeight
     *
     * @return TCMSImage
     */
    public function GetSquareThumbnail($maxThumbWidthHeight)
    {
        return $this->GetForcedSizeThumbnail($maxThumbWidthHeight, $maxThumbWidthHeight);
    }

    /**
     * checks if a given image has more than 3MB of size.
     *
     * @param string $imagePath
     *
     * @return bool
     */
    public function CheckThumbnailFileSizeLimit($imagePath)
    {
        $returnVal = false;

        $iMaxFileSize = 3145728;
        if ($this->UseImageMagick()) {
            $iMaxFileSize = 1024 * 1024 * 10; // 10MB
        }

        if (filesize($imagePath) <= $iMaxFileSize) {
            $returnVal = true;
        }

        return $returnVal;
    }

    /**
     * returns a html image tag of a thumbnail.
     *
     * @param int    $maxThumbWidth  - max width of the thumbnail
     * @param int    $maxThumbHeight - max height of the thumbnail
     * @param int    $maxZoomWidth   - max width of the full size image
     * @param int    $maxZoomHeight  - max height of the full size image
     * @param string $sCSSClass      - optional CSS class for the image tag
     * @param string $sNameOfSeries  - name of the image series (needed for back/forward buttons of lightbox)
     * @param string $caption
     * @param bool   $bForceSquare   - default: false, forces an thumbnail to be square format
     * @param string $sZoomHTMLTag   - is included in the <a> tag if zoom is shown (you can use this to overlay a zoom symbol
     *
     * @return string
     */
    public function renderImage($maxThumbWidth, $maxThumbHeight, $maxZoomWidth = null, $maxZoomHeight = null, $sCSSClass = '', $sNameOfSeries = null, $caption = null, $bForceSquare = false, $sZoomHTMLTag = '')
    {
        if (is_null($maxZoomWidth)) {
            $maxZoomWidth = CMS_MAX_IMAGE_ZOOM_WIDTH;
        }
        if (is_null($maxZoomHeight)) {
            $maxZoomHeight = CMS_MAX_IMAGE_ZOOM_HEIGHT;
        }
        $oThumb = null;
        $oGlobal = TGlobal::instance();
        if (!$bForceSquare) {
            $oThumb = $this->GetThumbnail($maxThumbWidth, $maxThumbHeight, true);
        } else {
            $oThumb = $this->GetSquareThumbnail($maxThumbWidth);
        }

        if (!is_null($oThumb)) {
            if (is_null($maxZoomWidth)) {
                $maxZoomWidth = $this->aData['width'];
            }
            if (is_null($maxZoomHeight)) {
                $maxZoomHeight = $this->aData['height'];
            }

            $oZoomThumb = $this->GetThumbnail($maxZoomWidth, $maxZoomHeight, false);

            $returnString = '';
            $sEmbedCode = '';
            if ($this->IsExternalMovie()) {
                $sEmbedCode = $oThumb->GetExternalVideoEmbedCode();
                if ($maxThumbWidth > 250) {
                    $returnString = $sEmbedCode;
                }
            }

            if (empty($returnString)) {
                $lightBoxClass = '';
                $ajaxTag = '';
                if ($this->useLightBox) {
                    $ajaxTag = '';
                    $lightBoxClass = ' thickbox';
                    if (!empty($sNameOfSeries)) {
                        $ajaxTag = ' rel="'.TGlobal::OutHTML($sNameOfSeries).'"';
                    }
                }

                // get custom caption or show description from media database
                if (is_null($caption)) {
                    $caption = $this->aData['description'];
                }

                $thumbTag = '<img class="'.TGlobal::OutHTML($sCSSClass).'" src="'.$oThumb->GetFullURL()."\" width=\"{$oThumb->aData['width']}\" height=\"{$oThumb->aData['height']}\" alt=\"".TGlobal::OutHTML($caption).'" title="'.TGlobal::OutHTML($caption).'" />';
                $showZoom = ($this->aData['width'] > $oThumb->aData['width'] || $this->aData['height'] > $oThumb->aData['height']);
                if ($this->useLightBox && $showZoom) {
                    if (TGlobal::CMSUserDefined() && $oGlobal->IsCMSMode()) {
                        $returnString = '<div class="cmsimage"><img class=\"'.TGlobal::OutHTML($sCSSClass).'\" width="'.$oThumb->aData['width'].'" height="'.$oThumb->aData['height'].'" onclick="CreateMediaZoomDialogFromImageURL(\''.$oZoomThumb->GetFullURL().'\',\''.$oZoomThumb->aData['width'].'\',\''.$oZoomThumb->aData['height'].'\');event.cancelBubble=true;return false;" style="padding: 3px;" id="cmsimage_'.$this->id.'" src="'.$oThumb->GetFullURL().'" />';
                        if ($this->IsExternalMovie() && empty($sEmbedCode)) {
                            $returnString .= '<div class="videoprocessing">'.TGlobal::OutHTML(TGlobal::Translate('chameleon_system_core.text.wait_for_processing')).'</div>';
                        }
                        $returnString .= '</div>';
                    } else {
                        $returnString = '<a class="'.TGlobal::OutHTML($sCSSClass.' '.$lightBoxClass).'" href="'.$oZoomThumb->GetFullURL()."\"{$ajaxTag} title=\"".TGlobal::OutHTML($caption)."\">{$thumbTag}{$sZoomHTMLTag}</a>";
                    }
                } else {
                    $returnString = $thumbTag;
                }
            }
        }

        return $returnString;
    }

    /**
     * returns the video embed code for videos that are hosted external
     * replaces the width/height of the embed code with given values.
     *
     * @param int|null $iWidth
     * @param int|null $iHeight
     *
     * @return string
     */
    public function GetExternalVideoEmbedCode($iWidth = null, $iHeight = null)
    {
        $sEmbedCode = $this->aData['external_embed_code'];
        if (!is_int($iWidth)) {
            $iWidth = $this->aData['width'];
        }
        if (!is_int($iHeight)) {
            $iHeight = $this->aData['height'];
        }

        $sEmbedCode = preg_replace('/width="[0-9]*"/i', 'width="'.$iWidth.'"', $sEmbedCode);
        $sEmbedCode = preg_replace('/height="[0-9]*"/i', 'height="'.$iHeight.'"', $sEmbedCode);

        return $sEmbedCode;
    }

    /**
     * returns an <img> html tag of current image or image object via parameter
     * does not handle zoom images and other media types
     * use GetThumbnailTag instead.
     *
     * @param TCMSImage $oImage    - default NULL = $this
     * @param string    $sCaption
     * @param string    $sCSSClass
     *
     * @return string
     */
    public function GetImageTag($oImage = null, $sCaption = '', $sCSSClass = '')
    {
        if (is_null($oImage)) {
            $oImage = $this;
        }
        if (empty($sCaption)) {
            $sCaption = TGlobal::OutHTML($oImage->aData['description']);
        }
        $sImageTag = '<img class="'.TGlobal::OutHTML($sCSSClass).'" src="'.$oImage->GetFullURL()."\" width=\"{$oImage->aData['width']}\" height=\"{$oImage->aData['height']}\" alt=\"".TGlobal::OutHTML($sCaption).'" title="'.TGlobal::OutHTML($sCaption).'" />';

        return $sImageTag;
    }

    /**
     * returns an image in Yahoo RSS Media tag format
     * http://search.yahoo.com/mrss/.
     *
     * @param int $maxThumbWidth
     * @param int $maxThumbHeight
     *
     * @return string|bool
     */
    public function GetRSSMediaXMLTags($maxThumbWidth, $maxThumbHeight)
    {
        if (!$this->_isThumbnail) {
            $oThumb = $this->GetThumbnail($maxThumbWidth, $maxThumbHeight, true);
        } else {
            $oThumb = $this;
        }

        $returnString = false;

        if (!is_null($oThumb)) {
            $oImageType = TdbCmsFiletype::GetNewInstance();
            $oImageType->Load($this->aData['cms_filetype_id']);
            $sThumbURL = $oThumb->GetFullURL();
            if ('/' == substr($sThumbURL, 0, 1)) {
                $oSmartURLData = &TCMSSmartURLData::GetActive();
                $sPrefix = '';
                if ($oSmartURLData->bIsSSLCall) {
                    $sPrefix = 'https';
                } else {
                    $sPrefix = 'http';
                }
                $sThumbURL = $sPrefix.'://'.$oSmartURLData->sOriginalDomainName.$sThumbURL;
            }
            $returnString = '<media:content url="'.$sThumbURL.'" type="'.$oImageType->fieldContentType.'" height="'.$oThumb->aData['height'].'" width="'.$oThumb->aData['width'].'" />'."\n";
        }

        return $returnString;
    }

    /**
     * returns an image in Atom RSS link enclosure format
     * http://tools.ietf.org/html/rfc4287.
     *
     * @param int $maxThumbWidth
     * @param int $maxThumbHeight
     *
     * @return string|bool
     */
    public function getAtomMediaTag($maxThumbWidth, $maxThumbHeight)
    {
        if (!$this->_isThumbnail) {
            $oThumb = $this->GetThumbnail($maxThumbWidth, $maxThumbHeight, true);
        } else {
            $oThumb = $this;
        }

        $returnString = false;

        if (!is_null($oThumb)) {
            $returnString = '';
            $oImageType = TdbCmsFiletype::GetNewInstance();
            $oImageType->Load($this->aData['cms_filetype_id']);

            $sThumbURL = $oThumb->GetFullURL();

            if ('/' == substr($sThumbURL, 0, 1)) {
                $oSmartURLData = &TCMSSmartURLData::GetActive();
                if ($oSmartURLData->bIsSSLCall) {
                    $sPrefix = 'https';
                } else {
                    $sPrefix = 'http';
                }
                $sThumbURL = $sPrefix.'://'.$oSmartURLData->sOriginalDomainName.$sThumbURL;
            }

            $returnString .= '<link rel="enclosure" type="'.$oImageType->fieldContentType.'" length="'.$oThumb->_fileSize.'" href="'.$sThumbURL.'" />';
        }

        return $returnString;
    }

    /**
     * returns a string describing the imageType (gif, jpg, png).
     *
     * @return string
     */
    public function GetImageTypeFromFile()
    {
        if (is_null($this->_imageType)) {
            $path = $this->GetLocalMediaDirectory().$this->aData['path'];
            $size = getimagesize($path);
            $this->_imageType = null;
            switch ($size[2]) {
                case 1:
                    $this->_imageType = 'gif';
                    break;
                case 2:
                    $this->_imageType = 'jpg';
                    break;
                case 3:
                    $this->_imageType = 'png';
                    break;
            }
        }

        return $this->_imageType;
    }

    /**
     * return true if the image is an external hosted video file (MP4 or FLV video).
     *
     * @return bool
     */
    public function IsExternalMovie()
    {
        $bIsExternalMovie = false;
        if (!empty($this->aData['external_video_id'])) {
            $bIsExternalMovie = true;
        }

        return $bIsExternalMovie;
    }

    /**
     * returns a string describing the imageType (gif, jpg, png)
     * returns mp4 for all external videos.
     *
     * @return string
     */
    public function GetImageType()
    {
        if (is_null($this->_imageType)) {
            if (!empty($this->aData['external_video_id'])) {
                $this->_imageType = 'mp4';
            } else {
                $fileTypeID = '';
                if (!empty($this->aData['cms_filetype_id'])) {
                    $fileTypeID = $this->aData['cms_filetype_id'];
                }
                $fileType = $this->getFileType($fileTypeID);
                $this->_imageType = $fileType->fieldFileExtension;
            }
        }

        return $this->_imageType;
    }

    /**
     * @param $id
     *
     * @return TdbCmsFiletype
     */
    protected function getFileType($id)
    {
        return \ChameleonSystem\CoreBundle\ServiceLocator::get('chameleon_system_core.database_access_layer_file_types')->getFileType($id);
    }

    /**
     * returns a string with the image filesize in kb/MB string Format or bytes.
     *
     * @param bool $bytes
     *
     * @return string
     */
    public function GetImageSize($bytes = false)
    {
        if (is_null($this->_fileSize) || 0 == $this->_fileSize) {
            $path = $this->GetLocalMediaDirectory().$this->aData['path'];
            $this->_fileSize = filesize($path);
            $updateQuery = "UPDATE `cms_media` SET `filesize` = '".$this->_fileSize."' WHERE `id` = '".$this->id."'";
            MySqlLegacySupport::getInstance()->query($updateQuery);
        }

        if ($bytes) {
            return $this->_fileSize;
        } else {
            $size = $this->_fileSize;
            $i = 0;
            $iec = array('b', 'kb', 'MB', 'GB');
            while (($size / 1024) > 1) {
                $size = $size / 1024;
                ++$i;
            }

            return round($size).' '.$iec[$i];
        }
    }

    /**
     * @return DatabaseAccessLayerCmsMedia
     */
    public function getMediaDataAccessService()
    {
        static $mediaDataAccessService;
        if (null === $mediaDataAccessService) {
            $mediaDataAccessService = \ChameleonSystem\CoreBundle\ServiceLocator::get('chameleon_system_core.database_access_layer_media');
        }

        return $mediaDataAccessService;
    }

    /*  port to PHP by John Jensen July 10 2001 -- original code (in C, for the PHP GD Module) by jernberg@fairytale.se
      Anwendung ist extrem langsam, deshalb nur bei uploads verwenden. Nicht bei dynamischem Content
    */
    protected function ImageCopyResampleBicubic($dst_img, $src_img, $dst_x, $dst_y, $src_x, $src_y, $dst_w, $dst_h, $src_w, $src_h)
    {
        for ($i = 0; $i < 256; ++$i) { // get palette. Is this algorithm correct?
            $colors = @imagecolorsforindex($src_img, $i);
            imagecolorallocate($dst_img, $colors['red'], $colors['green'], $colors['blue']);
        }

        $scaleX = ($src_w - 1) / $dst_w;
        $scaleY = ($src_h - 1) / $dst_h;

        $scaleX2 = $scaleX / 2.0;
        $scaleY2 = $scaleY / 2.0;

        for ($j = $src_y; $j < $dst_h; ++$j) {
            $sY = $j * $scaleY;
            for ($i = $src_x; $i < $dst_w; ++$i) {
                $sX = $i * $scaleX;
                $c1 = imagecolorsforindex($src_img, imagecolorat($src_img, (int) $sX, (int) $sY + $scaleY2));
                $c2 = imagecolorsforindex($src_img, imagecolorat($src_img, (int) $sX, (int) $sY));
                $c3 = imagecolorsforindex($src_img, imagecolorat($src_img, (int) $sX + $scaleX2, (int) $sY + $scaleY2));
                $c4 = imagecolorsforindex($src_img, imagecolorat($src_img, (int) $sX + $scaleX2, (int) $sY));

                $red = (int) (($c1['red'] + $c2['red'] + $c3['red'] + $c4['red']) / 4);
                $green = (int) (($c1['green'] + $c2['green'] + $c3['green'] + $c4['green']) / 4);
                $blue = (int) (($c1['blue'] + $c2['blue'] + $c3['blue'] + $c4['blue']) / 4);

                $color = imagecolorclosest($dst_img, $red, $green, $blue);
                imagesetpixel($dst_img, $i + $dst_x, $j + $dst_y, $color);
            }
        }
    }

    /**
     * returns a thumbnail image tag for use in CMS backend context (wysiwyg)
     * img tag includes properties: cmsmedia, cmsshowfull, cmsshowcaption.
     *
     * @param int  $width
     * @param int  $height
     * @param bool $showCaption
     * @param bool $showFull
     *
     * @return string
     */
    public function GetCMSImageTag($width, $height, $showCaption, $showFull)
    {
        $sImgTag = '';
        $src = $this->GetFullURL();
        if ($width >= $this->aData['width'] || $height >= $this->aData['height']) {
            $sImgTag = '<img alt="'.$this->aData['description'].'" title="'.$this->aData['description'].'" cmsmedia="'.$this->id.'" cmsshowfull="0" cmsshowcaption="'.$showCaption.'" src="'.$src.'"  height="'.$this->aData['height'].'" width="'.$this->aData['width'].'" />';
        } else {
            $sImgTag = '<img alt="'.$this->aData['description'].'" title="'.$this->aData['description'].'" cmsmedia="'.$this->id.'" cmsshowfull="'.$showFull.'" cmsshowcaption="'.$showCaption.'" src="'.$src.'"  height="'.$height.'" width="'.$width.'" />';
        }

        return $sImgTag;
    }

    /**
     * get the filetype icon as plain IMG TAG.
     *
     * @return string
     */
    public function GetPlainFileTypeIcon()
    {
        return '<span class="'.$this->getFileTypeIconCssStyle().TGlobalBase::OutHTML($this->GetImageType()).'"></span>';
    }

    protected function getFileTypeIconCssStyle(): string
    {
        return 'fiv-sqo fiv-icon-';
    }

    /**
     * returns an array describing the allowed media types (gif, jpg, png).
     *
     * this function is used static
     *
     * @return array
     */
    public static function GetAllowedMediaTypes()
    {
        return ['gif', 'jpg', 'jpeg', 'jpe', 'png'];
    }

    /**
     * delete all thumbnails.
     *
     * @return bool
     */
    public function ClearThumbnails()
    {
        // delete thumbnails
        $sDir = PATH_MEDIA_LIBRARY_THUMBS.$this->GetThumbPathExtension();
        if (is_dir($sDir)) {
            if ($handle = opendir($sDir)) {
                $sPatter = '-ID'.$this->aData['cmsident'].'-';
                $filemanager = self::getFileManager();
                while (false !== ($file = readdir($handle))) {
                    //somename-XxY-IDxxx-
                    if (false !== strpos($file, $sPatter)) {
                        $filemanager->unlink($sDir.'/'.$file);
                    }
                }
                closedir($handle);
            }
        }

        return true;
    }

    /*
    New:
    - In version 2.1 (February 26 2007) Tom Bishop has done some important speed enhancements.
    - From version 2 (July 17 2006) the script uses the imageconvolution function in PHP
    version >= 5.1, which improves the performance considerably.

    Unsharp masking is a traditional darkroom technique that has proven very suitable for
    digital imaging. The principle of unsharp masking is to create a blurred copy of the image
    and compare it to the underlying original. The difference in colour values
    between the two images is greatest for the pixels near sharp edges. When this
    difference is subtracted from the original image, the edges will be
    accentuated.

    The Amount parameter simply says how much of the effect you want. 100 is 'normal'.
    Radius is the radius of the blurring circle of the mask. 'Threshold' is the least
    difference in colour values that is allowed between the original and the mask. In practice
    this means that low-contrast areas of the picture are left unrendered whereas edges
    are treated normally. This is good for pictures of e.g. skin or blue skies.

    Any suggenstions for improvement of the algorithm, expecially regarding the speed
    and the roundoff errors in the Gaussian blur process, are welcome.
    */

    protected function UnsharpMask($img, $amount = 40, $radius = 0.2, $threshold = 2)
    {
        ////////////////////////////////////////////////////////////////////////////////////////////////
        ////
        ////                  Unsharp Mask for PHP - version 2.1
        ////
        ////    Unsharp mask algorithm by Torstein Hønsi 2003-06.
        ////             thoensi_at_netcom_dot_no.
        ////               Please leave this notice.
        ////
        ///////////////////////////////////////////////////////////////////////////////////////////////

        // $img is an image that is already created within php using
        // imgcreatetruecolor. No url! $img must be a truecolor image.

        // Attempt to calibrate the parameters to Photoshop:
        if ($amount > 500) {
            $amount = 500;
        }
        $amount = $amount * 0.016;
        if ($radius > 50) {
            $radius = 50;
        }
        $radius = $radius * 2;
        if ($threshold > 255) {
            $threshold = 255;
        }

        $radius = abs(round($radius)); // Only integers make sense.
        if (0 == $radius) {
            return $img;
        }
        $w = imagesx($img);
        $h = imagesy($img);
        $imgCanvas = imagecreatetruecolor($w, $h);
        $imgBlur = imagecreatetruecolor($w, $h);

        // Gaussian blur matrix:
        //
        //    1    2    1
        //    2    4    2
        //    1    2    1
        //
        //////////////////////////////////////////////////

        if (function_exists('imageconvolution')) { // PHP >= 5.1
            $matrix = array(array(1, 2, 1), array(2, 4, 2), array(1, 2, 1));
            imagecopy($imgBlur, $img, 0, 0, 0, 0, $w, $h);
            imageconvolution($imgBlur, $matrix, 16, 0);
        } else {
            // Move copies of the image around one pixel at the time and merge them with weight
            // according to the matrix. The same matrix is simply repeated for higher radii.
            for ($i = 0; $i < $radius; ++$i) {
                imagecopy($imgBlur, $img, 0, 0, 1, 0, $w - 1, $h); // left
                imagecopymerge($imgBlur, $img, 1, 0, 0, 0, $w, $h, 50); // right
                imagecopymerge($imgBlur, $img, 0, 0, 0, 0, $w, $h, 50); // center
                imagecopy($imgCanvas, $imgBlur, 0, 0, 0, 0, $w, $h);

                imagecopymerge($imgBlur, $imgCanvas, 0, 0, 0, 1, $w, $h - 1, 33.33333); // up
                imagecopymerge($imgBlur, $imgCanvas, 0, 1, 0, 0, $w, $h, 25); // down
            }
        }

        if ($threshold > 0) {
            // Calculate the difference between the blurred pixels and the original
            // and set the pixels
            for ($x = 0; $x < $w; ++$x) { // each row
                for ($y = 0; $y < $h; ++$y) { // each pixel
                    $rgbOrig = imagecolorat($img, $x, $y);
                    $rOrig = (($rgbOrig >> 16) & 0xFF);
                    $gOrig = (($rgbOrig >> 8) & 0xFF);
                    $bOrig = ($rgbOrig & 0xFF);

                    $rgbBlur = imagecolorat($imgBlur, $x, $y);

                    $rBlur = (($rgbBlur >> 16) & 0xFF);
                    $gBlur = (($rgbBlur >> 8) & 0xFF);
                    $bBlur = ($rgbBlur & 0xFF);

                    // When the masked pixels differ less from the original
                    // than the threshold specifies, they are set to their original value.
                    $rNew = (abs($rOrig - $rBlur) >= $threshold) ? max(0, min(255, ($amount * ($rOrig - $rBlur)) + $rOrig)) : $rOrig;
                    $gNew = (abs($gOrig - $gBlur) >= $threshold) ? max(0, min(255, ($amount * ($gOrig - $gBlur)) + $gOrig)) : $gOrig;
                    $bNew = (abs($bOrig - $bBlur) >= $threshold) ? max(0, min(255, ($amount * ($bOrig - $bBlur)) + $bOrig)) : $bOrig;

                    if (($rOrig != $rNew) || ($gOrig != $gNew) || ($bOrig != $bNew)) {
                        $pixCol = imagecolorallocate($img, $rNew, $gNew, $bNew);
                        imagesetpixel($img, $x, $y, $pixCol);
                    }
                }
            }
        } else {
            for ($x = 0; $x < $w; ++$x) { // each row
                for ($y = 0; $y < $h; ++$y) { // each pixel
                    $rgbOrig = imagecolorat($img, $x, $y);
                    $rOrig = (($rgbOrig >> 16) & 0xFF);
                    $gOrig = (($rgbOrig >> 8) & 0xFF);
                    $bOrig = ($rgbOrig & 0xFF);

                    $rgbBlur = imagecolorat($imgBlur, $x, $y);

                    $rBlur = (($rgbBlur >> 16) & 0xFF);
                    $gBlur = (($rgbBlur >> 8) & 0xFF);
                    $bBlur = ($rgbBlur & 0xFF);

                    $rNew = ($amount * ($rOrig - $rBlur)) + $rOrig;
                    if ($rNew > 255) {
                        $rNew = 255;
                    } elseif ($rNew < 0) {
                        $rNew = 0;
                    }
                    $gNew = ($amount * ($gOrig - $gBlur)) + $gOrig;
                    if ($gNew > 255) {
                        $gNew = 255;
                    } elseif ($gNew < 0) {
                        $gNew = 0;
                    }
                    $bNew = ($amount * ($bOrig - $bBlur)) + $bOrig;
                    if ($bNew > 255) {
                        $bNew = 255;
                    } elseif ($bNew < 0) {
                        $bNew = 0;
                    }
                    $rgbNew = ($rNew << 16) + ($gNew << 8) + $bNew;
                    imagesetpixel($img, $x, $y, $rgbNew);
                }
            }
        }
        imagedestroy($imgCanvas);
        imagedestroy($imgBlur);

        return $img;
    }

    /**
     * Plug-and-Play fastimagecopyresampled function replaces much slower imagecopyresampled.
     * Just include this function and change all "imagecopyresampled" references to "fastimagecopyresampled".
     * Typically from 30 to 60 times faster when reducing high resolution images down to thumbnail size using the default quality setting.
     * Author: Tim Eckel - Date: 12/17/04 - Project: FreeRingers.net - Freely distributable.
     *
     * Optional "quality" parameter (defaults is 3).  Fractional values are allowed, for example 1.5.
     * 1 = Up to 600 times faster.  Poor results, just uses imagecopyresized but removes black edges.
     * 2 = Up to 95 times faster.  Images may appear too sharp, some people may prefer it.
     * 3 = Up to 60 times faster.  Will give high quality smooth results very close to imagecopyresampled.
     * 4 = Up to 25 times faster.  Almost identical to imagecopyresampled for most images.
     * 5 = No speedup.  Just uses imagecopyresampled, highest quality but no advantage over imagecopyresampled.
     */
    protected function fastimagecopyresampled(&$dst_image, $src_image, $dst_x, $dst_y, $src_x, $src_y, $dst_w, $dst_h, $src_w, $src_h, $quality = 3)
    {
        if (empty($src_image) || empty($dst_image)) {
            return false;
        }
        if ($quality <= 1) {
            $temp = imagecreatetruecolor($dst_w + 1, $dst_h + 1);
            imagecopyresized($temp, $src_image, $dst_x, $dst_y, $src_x, $src_y, $dst_w + 1, $dst_h + 1, $src_w, $src_h);
            imagecopyresized($dst_image, $temp, 0, 0, 0, 0, $dst_w, $dst_h, $dst_w, $dst_h);
            imagedestroy($temp);
        } elseif ($quality < 5 && (($dst_w * $quality) < $src_w || ($dst_h * $quality) < $src_h)) {
            $tmp_w = $dst_w * $quality;
            $tmp_h = $dst_h * $quality;
            $temp = imagecreatetruecolor($tmp_w + 1, $tmp_h + 1);
            imagecopyresized($temp, $src_image, $dst_x * $quality, $dst_y * $quality, $src_x, $src_y, $tmp_w + 1, $tmp_h + 1, $src_w, $src_h);
            imagecopyresampled($dst_image, $temp, 0, 0, 0, 0, $dst_w, $dst_h, $tmp_w, $tmp_h);
            imagedestroy($temp);
        } else {
            imagecopyresampled($dst_image, $src_image, $dst_x, $dst_y, $src_x, $src_y, $dst_w, $dst_h, $src_w, $src_h);
        }

        return true;
    }

    public function __toString()
    {
        return $this->GetImageTag();
    }

    /**
     * @param $createdThumbnailPath - the full path to the thumbnail-image
     * @param string $thumbnailType - jpg, png, gif
     */
    protected function thumbnailCreatedHook($createdThumbnailPath, $thumbnailType)
    {
    }

    /**
     * @return UrlNormalizationUtil
     */
    private function getUrlNormalizationUtil()
    {
        return \ChameleonSystem\CoreBundle\ServiceLocator::get('chameleon_system_core.util.url_normalization');
    }

    /**
     * @return \ChameleonSystem\CoreBundle\Util\FieldTranslationUtil
     */
    private function getFieldTranslationUtil()
    {
        return \ChameleonSystem\CoreBundle\ServiceLocator::get('chameleon_system_core.util.field_translation');
    }

    /**
     * @return PortalDomainServiceInterface
     */
    private function getPortalDomainService()
    {
        return \ChameleonSystem\CoreBundle\ServiceLocator::get('chameleon_system_core.portal_domain_service');
    }

    /**
     * @return IPkgCmsFileManager
     */
    private static function getFileManager()
    {
        return \ChameleonSystem\CoreBundle\ServiceLocator::get('chameleon_system_core.filemanager');
    }
}
