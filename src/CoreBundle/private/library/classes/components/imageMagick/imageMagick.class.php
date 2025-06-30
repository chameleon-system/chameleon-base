<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use ChameleonSystem\CoreBundle\ServiceLocator;
use ChameleonSystem\CoreBundle\Util\UrlNormalization\UrlNormalizationUtil;
use esono\pkgCmsCache\CacheInterface;
use Symfony\Component\Filesystem\Exception\IOExceptionInterface;
use Symfony\Component\Filesystem\Filesystem;

class imageMagick
{
    /**
     * application directory of imageMagick.
     */
    protected string $sImageMagickDir = '/usr/local/bin/'; // ImageMagick binary files

    /**
     * temp directory with webserver write rights
     * will be set to upload_tmp_dir from php.ini on init.
     */
    protected string $sTempDir = CMS_TMP_DIR;

    /**
     * quality for JPG images.
     */
    protected int $iJPGQuality = 90;

    /**
     * strip the image of any profiles or comments.
     */
    protected bool $bStrip = true;

    /**
     * version of imageMagick.
     * like: 6.5.4.
     */
    protected string $sVersion = '';

    /**
     * temporary filename used to save the resized/converted image in
     * temp folder before it is moved to the target folder.
     */
    protected string $sTempFileName = '';

    protected ?TCMSFile $oSourceFile;

    /**
     * raw image data from identify --verbose.
     */
    protected array $aImageRawData = [];

    /**
     * parsed image data.
     */
    protected array $aImageData = [];

    /**
     * indicates if an error occured.
     */
    public bool $bHasErrors = false;

    /**
     * array of error messages.
     */
    public array $aErrorMessages = [];

    /**
     * path to the source image file.
     */
    protected string $sSourceFile = '';

    /**
     * path to the target image file.
     */
    protected string $sTargetFile = '';

    /**
     * if an image is animated (GIF), it defines the number of scenes.
     */
    protected int $iNumberOfScenes = 0;

    /**
     * the initialized TCMSImage object.
     */
    protected ?TCMSImage $oImage = null;

    /**
     * indicates if the PHP extension "Imagick" is available instead of shell usage.
     */
    public bool $bUsePHPLibrary = false;

    /**
     * holds the php extension Imagick if available.
     */
    protected ?Imagick $oIMagick = null;

    /**
     * width of the current thumbnail.
     *
     * @var int
     */
    protected $iThumbWidth;

    /**
     * height of the current thumbnail.
     *
     * @var int
     */
    protected $iThumbHeight;

    /**
     * check if the tmp dir already exists - if not create one.
     */
    public function Init()
    {
        $this->sTempDir = CMS_TMP_DIR;

        if (file_exists(CMS_TMP_DIR) && is_dir(CMS_TMP_DIR)) {
            $this->sTempDir = CMS_TMP_DIR;
        } else { // try to create a temp directory and use this instead
            $symfonyFileSystem = new Filesystem();
            try {
                $symfonyFileSystem->mkdir(CMS_TMP_DIR);
                $this->sTempDir = CMS_TMP_DIR;
            } catch (IOExceptionInterface $exception) {
            }
        }

        // image magick params

        // -quality value = JPEG/MIFF/PNG compression level.
        if (defined('IMAGEMAGICK_IMG_QUALITY') && is_numeric(IMAGEMAGICK_IMG_QUALITY)) {
            $this->iJPGQuality = IMAGEMAGICK_IMG_QUALITY;
        }
        // -strip = strip the image of any profiles or comments.
        if (defined('IMAGEMAGICK_STRIP') && is_bool(IMAGEMAGICK_STRIP)) {
            $this->bStrip = IMAGEMAGICK_STRIP;
        }

        $this->LocateImageMagick();
    }

    /**
     * locate imageMagick and check version if found.
     *
     * @return bool
     */
    protected function LocateImageMagick()
    {
        $cache = $this->getCache();
        $cacheKey = $cache->getKey([
            __METHOD__,
        ], false);

        $path = $cache->get($cacheKey);
        if (null !== $path) {
            $this->sImageMagickDir = $path;

            return true;
        }

        $found = false;
        if (@file_exists(realpath($_SERVER['DOCUMENT_ROOT'].'/../bin/convert')) && @file_exists(realpath($_SERVER['DOCUMENT_ROOT'].'/../bin/identify'))) {
            $this->sImageMagickDir = realpath($_SERVER['DOCUMENT_ROOT'].'/../bin/');
            $found = true;
        }

        if (@file_exists('/usr/bin/convert') && @file_exists('/usr/bin/identify')) {
            $this->sImageMagickDir = '/usr/bin/';
            $found = true;
        }

        if (!$found) {
            if (@file_exists('/usr/local/bin/convert') && @file_exists('/usr/local/bin/identify')) {
                $this->sImageMagickDir = '/usr/local/bin/';
                $found = true;
            }
        }

        if (true === $found) {
            $cache->set($cacheKey, $this->sImageMagickDir, []);
        }

        return $found;
    }

    /**
     * tries to fetch the ImageMagick version number.
     *
     * @return string|bool - returns false or the version number as string
     */
    public function GetImageMagickVersion()
    {
        if ('' !== $this->sVersion) {
            return $this->sVersion;
        }
        $cache = $this->getCache();
        $cacheKey = $cache->getKey([
            __METHOD__,
        ], false);

        $version = $cache->get($cacheKey);
        if (null !== $version) {
            $this->sVersion = $version;

            return $this->sVersion;
        }

        if ($this->LocateImageMagick()) {
            $command = $this->sImageMagickDir.'/identify -version';
            exec($command, $returnarray, $returnvalue);
            if (count($returnarray) > 0) {
                if (preg_match('/^Version:/', $returnarray[0])) {
                    $this->sVersion = trim(substr($returnarray[0], strpos($returnarray[0], ' ')));
                    $arrTmp = explode(' ', $this->sVersion);
                    $this->sVersion = $arrTmp[1];
                }
            }
        }

        if (empty($this->sVersion)) {
            $this->sVersion = '';

            return false;
        }
        $cache->set($cacheKey, $this->sVersion, []);

        return $this->sVersion;
    }

    /**
     * loads the source image and copies it to the temp folder.
     *
     * @param string $sFilePath - path to the source image
     */
    public function LoadImage(string $sFilePath, ?TCMSImage $oImage = null)
    {
        $this->aErrorMessages = [];
        $this->bHasErrors = false;
        if (file_exists($sFilePath)) {
            $this->sSourceFile = $sFilePath;
            $this->oImage = $oImage;
            if ($this->bUsePHPLibrary) {
                $this->oIMagick = new Imagick($sFilePath);
                $this->oIMagick->autoOrient();
            }
        } else {
            $this->AddError('source file not found "'.$sFilePath.'"');
        }
        if (!$this->bHasErrors) {
            $this->GetTempFileName($sFilePath);

            $oFile = new TCMSFile();
            $oFile->Load($sFilePath);
            $this->oSourceFile = $oFile;

            $this->LoadImageData();
            $this->ParseImageData();
            $this->SetCorrectFileExtension();

            if ('gif' === $this->oSourceFile->sExtension) {
                $this->GetNumberOfScenes();
            }
        }
    }

    /**
     * adds an error message.
     *
     * @param string $message
     */
    protected function AddError($message)
    {
        $this->bHasErrors = true;
        $this->aErrorMessages[] = $message;
    }

    /**
     * returns a temporary filename with hostname + uid as prefix.
     */
    protected function GetTempFileName(string $sFilePath, string $suffix = ''): string
    {
        $sTempName = preg_replace("/[^a-zA-Z0-9_\.]/", '_', basename($sFilePath));
        $sFilteredHostName = $this->getUrlNormalizationUtil()->normalizeUrl($_SERVER['HTTP_HOST']);
        $sNewTempName = TTools::GetUUID($sFilteredHostName);
        if (!empty($suffix)) {
            $sNewTempName .= '_'.$suffix;
        }
        $sNewTempName .= '_'.$sTempName;

        $this->sTempFileName = $sNewTempName;

        return $sNewTempName;
    }

    /**
     * Loads the image data into internal array
     * must be executed for each image.
     *
     * @return bool
     */
    protected function LoadImageData()
    {
        $returnVal = false;

        if (!$this->bUsePHPLibrary && is_null($this->oImage) && !$this->bHasErrors) {
            $command = $this->sImageMagickDir.'/identify -verbose '.escapeshellarg($this->sSourceFile);
            exec($command, $returnarray, $returnvalue);
            if ($returnvalue || 0 == count($returnarray)) {
                $this->AddError('image format not in the right format');
            } else {
                for ($i = 0; $i < count($returnarray); ++$i) {
                    $returnarray[$i] = trim($returnarray[$i]);
                }
                $this->aImageRawData = $returnarray;
                $returnVal = true;
            }
        } elseif (!is_null($this->oImage)) {
            $returnVal = true;
        }

        return $returnVal;
    }

    /**
     * parse the image data and get geometry and format.
     *
     * @return bool
     */
    protected function ParseImageData()
    {
        $returnVal = false;
        if (is_null($this->oImage)) {
            if ($this->bUsePHPLibrary) {
                if (!$this->bHasErrors) {
                    $this->aImageData['width'] = $this->oIMagick->getImageWidth();
                    $this->aImageData['height'] = $this->oIMagick->getImageHeight();
                    $this->aImageData['format'] = $this->oIMagick->getFormat();
                    $returnVal = true;
                }
            } else {
                if (!$this->bHasErrors && count($this->aImageRawData) > 0) {
                    for ($i = 0; $i < count($this->aImageRawData); ++$i) {
                        if (preg_match('/^Geometry/i', $this->aImageRawData[$i])) {
                            $tmp1 = explode(' ', $this->aImageRawData[$i]);
                            $tmp2 = explode('x', $tmp1[1]);
                            $this->aImageData['width'] = $tmp2[0];
                            $this->aImageData['height'] = $tmp2[1];
                        }
                        if (preg_match('/^Format/i', $this->aImageRawData[$i])) {
                            $tmp1 = explode(' ', $this->aImageRawData[$i]);
                            $format = $tmp1[1];
                            $this->aImageData['format'] = $format;
                        }
                    }
                    $returnVal = true;
                }
            }
        } else {
            $this->aImageData['width'] = $this->oImage->aData['width'];
            $this->aImageData['height'] = $this->oImage->aData['height'];
            $returnVal = true;
        }

        return $returnVal;
    }

    /**
     * forces the file extension to the one that is identified by imageMagick.
     */
    protected function SetCorrectFileExtension()
    {
        if (is_null($this->oImage)) {
            if (!$this->bHasErrors) {
                if (array_key_exists('format', $this->aImageData)) {
                    $format = strtolower($this->aImageData['format']);
                    if ('jpeg' == $format) {
                        $format = 'jpg';
                    }

                    $this->oSourceFile->sExtension = $format;
                }
            }
        } else {
            $this->oSourceFile->sExtension = $this->oImage->GetImageType();
        }
    }

    /**
     * Get the number of scenes in the image to determine if it is animated.
     *
     * @return bool
     */
    protected function GetNumberOfScenes()
    {
        $returnVal = false;
        if (!$this->bHasErrors) {
            if ($this->bUsePHPLibrary) {
                // not yet implemented
            } else {
                $command = $this->sImageMagickDir.'/identify -format "%n" '.escapeshellarg($this->sSourceFile);
                exec($command, $returnarray, $returnvalue);

                if ($returnvalue || 0 == count($returnarray)) {
                    $this->AddError('GetNumberOfScenes(): Incorrect Image Format or ImageMagick not found');
                } else {
                    $this->iNumberOfScenes = (int) trim($returnarray[0]);
                    $returnVal = true;
                }
            }
        }

        return $returnVal;
    }

    /**
     * Resizes image to the specified width and height.
     * Takes in account the image orientation set in exif or metadata and rotates the image if necessary.
     */
    public function ResizeImage(int $iWidth, int $iHeight): bool
    {
        $this->iThumbWidth = $iWidth;
        $this->iThumbHeight = $iHeight;

        $aParameter = [];

        $sImParamStrip = '';
        if ($this->bStrip) {
            $sImParamStrip = ' -strip';
        }

        $orientation = $this->getImageOrientation();

        if (null !== $orientation) {
            $aParameter[] = $this->getRotationCommandForOrientation($orientation);
        }

        $returnVal = false;
        if (!$this->bHasErrors) {
            if ($this->bUsePHPLibrary) {
                $this->oIMagick->resizeImage($iWidth, $iHeight, Imagick::FILTER_LANCZOS, 1);
                $this->oIMagick->writeImage($this->sTempDir.'/'.$this->sTempFileName);
                $this->oIMagick->destroy();
            } else {
                $oImageMagickConfig = TdbCmsConfigImagemagick::GetActiveInstance($iWidth, $iHeight);
                $iQuality = $this->iJPGQuality;
                if ($oImageMagickConfig) {
                    if (1 !== $oImageMagickConfig->fieldGamma) {
                        $aParameter[] = '-gamma '.escapeshellarg($oImageMagickConfig->fieldGamma);
                    }
                    if ($oImageMagickConfig->fieldScharpen) {
                        $aParameter[] = '-adaptive-sharpen 0x1';
                    }
                    $iQuality = $oImageMagickConfig->fieldQuality;
                    if ($oImageMagickConfig->fieldForceJpeg) {
                        $this->sTempFileName = substr($this->sTempFileName, 0, strrpos($this->sTempFileName, '.')).'.jpg';
                        $aParameter[] = '-background white -flatten';
                    }
                }
                $command = $this->sImageMagickDir.'/convert '.$sImParamStrip.' -quality '.escapeshellarg($iQuality).' -limit memory 160MiB -geometry '.escapeshellarg($iWidth.'x'.$iHeight).' '.implode(' ', $aParameter).' '.escapeshellarg($this->oSourceFile->sPath).' '.escapeshellarg($this->sTempDir.'/'.$this->sTempFileName);
                exec($command, $returnarray, $returnvalue);

                if ($returnvalue) {
                    $this->AddError('couldn`t resize image');
                } else {
                    $returnVal = true;
                }
            }
        }

        if ($returnVal) {
            $this->oSourceFile->sPath = $this->sTempDir.'/'.$this->sTempFileName;
        }

        return $returnVal;
    }

    protected function getImageOrientation(): ?string
    {
        if (function_exists('exif_read_data')) {
            $exif = @exif_read_data($this->oSourceFile->sPath);
            if (!empty($exif['Orientation'])) {
                return (string) $exif['Orientation'];
            }
        }

        $command = $this->sImageMagickDir.'/identify -format "%[EXIF:Orientation]" '.escapeshellarg($this->oSourceFile->sPath);
        exec($command, $output, $returnCode);

        if (0 === $returnCode && isset($output[0])) {
            return trim($output[0]) ?: null;
        }

        return null;
    }

    protected function getRotationCommandForOrientation(string $orientation): ?string
    {
        $command = null;

        switch ($orientation) {
            case '1':
            case 'TopLeft':
                break;
            case '2':
            case 'TopRight':
                $command = '-flop ';
                break;
            case '3':
            case 'BottomRight':
                $command = '-rotate 180 ';
                break;
            case '4':
            case 'BottomLeft':
                $command = '-flip ';
                break;
            case '5':
            case 'LeftTop':
                $command = '-transpose ';
                break;
            case '6':
            case 'RightTop':
                $command = '-rotate 90 ';
                break;
            case '7':
            case 'RightBottom':
                $command = '-transverse ';
                break;
            case '8':
            case 'LeftBottom':
                $command = '-rotate -90 ';
                break;
            default:
                return null;
        }

        return $command;
    }

    /**
     * Returns certain exif data, but at high performance cost.
     */
    public function getExifData(): ?array
    {
        $command = $this->sImageMagickDir.'/identify -verbose '.escapeshellarg($this->oSourceFile->sPath);
        exec($command, $imageProperties, $returnValue);

        $exifData = [];
        if (is_array($imageProperties)) {
            foreach ($imageProperties as $key => $val) {
                if (str_contains($val, 'exif:') || str_contains($val, 'Orientation:')) {
                    $exifRowString = trim(str_replace('exif:', '', $val));
                    $exifRowArray = explode(':', $exifRowString);

                    if (isset($exifRowArray[1])) {
                        $exifData[trim($exifRowArray[0])] = trim($exifRowArray[1]);
                    }
                }
            }
        }

        return $exifData;
    }

    /**
     * Crop the image to the specified width and height.
     *
     * @param int $iWidth the desired width of the cropped image
     * @param int $iHeight the desired height of the cropped image
     * @param bool $bCenter (Optional) Whether to center the crop. Default is true.
     *
     * @return bool true if the image was successfully cropped, false otherwise
     *
     * @throws ImagickException
     */
    public function CropImage(int $iWidth, int $iHeight, bool $bCenter = true): bool
    {
        $iXViewPoint = 0;
        $iYViewPoint = 0;
        if ($bCenter) {
            if ($this->iThumbWidth > $iWidth) {
                $iXViewPoint = round(($this->iThumbWidth - $iWidth) / 2);
            }
            if ($this->iThumbHeight > $iHeight) {
                $iYViewPoint = round(($this->iThumbHeight - $iHeight) / 2);
            }
        }

        $this->iThumbWidth = $iWidth;
        $this->iThumbHeight = $iHeight;

        $returnVal = false;
        if (!$this->bHasErrors) {
            if ($this->bUsePHPLibrary) {
                $this->oIMagick->cropImage($iWidth, $iHeight, $iXViewPoint, $iYViewPoint);
                $this->oIMagick->writeImage($this->sTempDir.'/'.$this->sTempFileName);
                $this->oIMagick->destroy();
            } else {
                $command = $this->sImageMagickDir.'/convert '.escapeshellarg($this->oSourceFile->sPath).' -auto-orient -crop '.escapeshellarg($iWidth.'x'.$iHeight.'+'.$iXViewPoint.'+'.$iYViewPoint).' +repage '.escapeshellarg($this->sTempDir.'/'.$this->sTempFileName);
                exec($command, $returnarray, $returnvalue);

                if ($returnvalue) {
                    $this->AddError('could not crop image');
                } else {
                    $returnVal = true;
                }
            }
        }

        if ($returnVal) {
            $this->oSourceFile->sPath = $this->sTempDir.'/'.$this->sTempFileName;
        }

        return $returnVal;
    }

    /**
     * Center image in canvas.
     */
    public function CenterImage(int $iWidthCanvas, int $iHeightCanvas, $sBackGroundColor = '#ffffff'): bool
    {
        $this->iThumbWidth = $iWidthCanvas;
        $this->iThumbHeight = $iHeightCanvas;

        $returnVal = false;
        if (!$this->bHasErrors) {
            if ($this->bUsePHPLibrary) {
                exit('center image using Imagick php extension is not ready yet');
            } else {
                $command = $this->sImageMagickDir.'/convert '.escapeshellarg($this->oSourceFile->sPath).' -auto-orient -background '.escapeshellarg($sBackGroundColor).' -gravity center -extent '.escapeshellarg($iWidthCanvas.'x'.$iHeightCanvas).' '.escapeshellarg($this->sTempDir.'/'.$this->sTempFileName);
                exec($command, $returnarray, $returnvalue);

                if ($returnvalue) {
                    $this->AddError('could not center image');
                } else {
                    $returnVal = true;
                }
            }
        }

        if ($returnVal) {
            $this->oSourceFile->sPath = $this->sTempDir.'/'.$this->sTempFileName;
        }

        return $returnVal;
    }

    /**
     * Saves image to file path.
     */
    public function SaveToFile(string $sTargetFile): bool
    {
        $returnVal = false;
        if (!$this->bHasErrors && !empty($sTargetFile)) {
            $this->sTargetFile = $sTargetFile;
            if (file_exists($sTargetFile)) {
                if (!is_writable($sTargetFile)) {
                    $this->AddError('copy to targetfile "'.$sTargetFile.'" failed because target file already exists and is not writable.');
                }
            }

            if (!$this->bHasErrors) {
                $symfonyFileSystem = new Filesystem();
                try {
                    $symfonyFileSystem->rename($this->sTempDir.'/'.$this->sTempFileName, $sTargetFile, true);
                } catch (IOExceptionInterface $exception) {
                    try {
                        $symfonyFileSystem->remove($this->sTempDir.'/'.$this->sTempFileName);
                    } catch (IOExceptionInterface $exception) {
                    }
                }
            }
        }

        return $returnVal;
    }

    /**
     * Adds a reflection to the image.
     *
     * @param bool $bImageline Draws a imageline for seperating the original
     *                         image and the reflection
     * @param int $iReflectionPercentage The percentage of the height of the
     *                                   original image where the reflection effect should take place. For
     *                                   example: if you put in 50 (50%) here, you'll get an mirrored reflection
     *                                   that is 50% as high as your original image. So if your original image
     *                                   was 100 pixel high - your image including mirrored reflection (which
     *                                   will be returned will now be 150 pixel. The higher the percentage,
     *                                   the longer the alpha gradient.
     */
    public function AddReflection(bool $bImageline = true, int $iReflectionPercentage): bool
    {
        /** !!!!! IMPORTANT NOTE !!!!! **/
        /**
         *  a) we need a working non shell (library) solution for this
         *  b) the shell version works only standalone - not with rounded corners (you'll get a black background on the reflection)
         *     so we need a solution that works also with rounded corners.
         **/
        $returnVal = false;
        $iOriginalImageHeight = $this->iThumbHeight;
        $iOriginalImageWidth = $this->iThumbWidth;
        $dMultiplicator = $iReflectionPercentage / 100;
        $iHeightReflection = round($iOriginalImageHeight * $dMultiplicator);
        $iHeightWithReflection = $iHeightReflection + $iOriginalImageHeight;
        $this->iThumbHeight = $iHeightWithReflection;

        if ($this->bUsePHPLibrary) { // library version
            exit('reflection using Imagick php extension is not ready yet');
        } else { // shell version
            $sOldTmpFileName = $this->sTempDir.'/'.$this->sTempFileName;

            $sNewFileName = str_replace(['.gif', '.jpeg', '.jpg'], '.png', $this->sSourceFile);
            $sNewTmpFileName = $this->sTempDir.'/'.$this->GetTempFileName($sNewFileName, 'reflect');

            // $command = $this->sImageMagickDir."convert ".escapeshellarg($sOldTmpFileName)." -alpha on \( +clone -flip -channel A -evaluate multiply .35 +channel \) -append -size ".escapeshellarg($iOriginalImageWidth)."x".escapeshellarg($iHeightWithReflection)." xc:transparent +swap -gravity North -geometry +0+5 -composite ".escapeshellarg($sNewTmpFileName);
            $command = $this->sImageMagickDir.'convert '.escapeshellarg($sOldTmpFileName)." -alpha on \( +clone -flip -size ".escapeshellarg($iOriginalImageWidth.'x'.$iHeightReflection)." gradient:gray40-black -alpha off -compose CopyOpacity -composite \) -append -gravity North -crop ".escapeshellarg($iOriginalImageWidth.'x'.$iHeightWithReflection)."+0-5\! -background transparent -compose Over -flatten -auto-orient ".escapeshellarg($sNewTmpFileName);

            exec($command, $returnarray, $returnvalue);
            if ($returnvalue) {
                $this->AddError('couldn`t add reflection to image');
            } else {
                $returnVal = true;
            }
            if (file_exists($sOldTmpFileName) && !is_dir($sOldTmpFileName)) {
                unlink($sOldTmpFileName);
            }
        }

        return $returnVal;
    }

    /**
     * adds a rounded corner with transparency, target file will be forced to PNG.
     */
    public function AddRoundedCorners(int $iRadius): bool
    {
        $returnVal = false;
        if ($this->bUsePHPLibrary) { // library version
            exit('rounded corners using Imagick php extension is not ready yet');
        } else { // shell version
            $sOldTmpFileName = $this->sTempDir.'/'.$this->sTempFileName;

            $sNewFileName = str_replace(['.gif', '.jpeg', '.jpg'], '.png', $this->sSourceFile);
            $sNewTmpFileName = $this->sTempDir.'/'.$this->GetTempFileName($sNewFileName, 'radius');

            $cmd = $this->sImageMagickDir.'/convert';
            $cmd .= ' -size '.escapeshellarg($this->iThumbWidth.'x'.$this->iThumbHeight).' xc:none -fill white -draw '.escapeshellarg('roundRectangle 0,0 '.$this->iThumbWidth.','.$this->iThumbHeight.' '.$iRadius.','.$iRadius).' '.escapeshellarg($sOldTmpFileName).' -compose SrcIn -composite -auto-orient '.escapeshellarg($sNewTmpFileName);

            exec($cmd, $returnarray, $returnvalue);
            if ($returnvalue) {
                $this->AddError('couldn`t add rounded corners to image');
            } else {
                $returnVal = true;
            }
            if (file_exists($sOldTmpFileName) && !is_dir($sOldTmpFileName)) {
                unlink($sOldTmpFileName);
            }
        }

        return $returnVal;
    }

    /**
     * Convert a tiff image to a jpg image.
     *
     * @param string $sJPGPath if is not set then save jgp image in same dir like the tiff image
     * @param array $aColorProfiles - array of filenames pointing to the color profile(s) that should be used for converting
     *                              Important: They are used for converting only and dropped afterwards. This does NOT make your resulting image
     *                              have color profiles
     */
    public function ConvertTiffToJpg(string $sTiffPath, string $sTiffFileName, string $sJPGPath = '', array $aColorProfiles = []): bool
    {
        $returnVal = false;
        if ($this->bUsePHPLibrary) { // library version
            exit('converting .tiff to .jpg using Imagick php extension is not ready yet');
        } else { // shell version
            if ('/' == substr($sTiffPath, strlen($sTiffPath) - 1)) {
                $sTiffPath = substr($sTiffPath, 0, -1);
            }
            $sTiffFilePath = $sTiffPath.'/'.$sTiffFileName;
            if (file_exists($sTiffFilePath)) {
                $sNewFileName = str_replace(['.tif'], '.jpg', $sTiffFileName);
                if (!empty($sJPGPath)) {
                    if ('/' == substr($sJPGPath, strlen($sJPGPath) - 1)) {
                        $sJPGPath = substr($sJPGPath, 0, -1);
                    }
                    $sNewFilePath = $sJPGPath.'/'.$sNewFileName;
                } else {
                    $sNewFilePath = $sTiffPath.'/'.$sNewFileName;
                }
                $cmd = $this->sImageMagickDir.'/convert';

                $sImParamStrip = ' -auto-orient ';
                if ($this->bStrip) {
                    $sImParamStrip .= ' -strip ';
                }
                if (is_array($aColorProfiles) && count($aColorProfiles) > 0) {
                    // ColorProfiles given
                    foreach (array_keys($aColorProfiles) as $iColorProfileIndex) {
                        $aColorProfiles[$iColorProfileIndex] = escapeshellarg($aColorProfiles[$iColorProfileIndex]);
                    }
                    $sColorProfiles = implode(' -profile ', $aColorProfiles);
                    $cmd .= ' -resize 600x600 -quality '.$this->iJPGQuality.' '.escapeshellarg($sTiffFilePath).' '.$sImParamStrip.' -profile '.$sColorProfiles.' '.escapeshellarg($sNewFilePath);
                } else {
                    // No colorProfiles given
                    $cmd .= ' -colorspace rgb -resize 600x600 '.$sImParamStrip.' -quality '.$this->iJPGQuality.' '.escapeshellarg($sTiffFilePath).' '.escapeshellarg($sNewFilePath);
                }
                exec($cmd, $returnarray, $returnvalue);
                if ($returnvalue) {
                    $this->AddError('couldn`t convert tiff to jpg image');
                } else {
                    $returnVal = true;
                }
            }
        }

        return $returnVal;
    }

    private function getCache(): CacheInterface
    {
        return ServiceLocator::get('chameleon_system_cms_cache.cache');
    }

    private function getUrlNormalizationUtil(): UrlNormalizationUtil
    {
        return ServiceLocator::get('chameleon_system_core.util.url_normalization');
    }
}
