<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Symfony\Component\Filesystem\Exception\IOExceptionInterface;
use Symfony\Component\Filesystem\Filesystem;

class TCMSFile
{
    /**
     * full image path.
     */
    public ?string $sPath = null;

    public string $sDir = '';
    public string $sFileName = '';
    public string $sExtension = '';
    public int $dSizeByte = 0;
    public string $sCreated = '';
    public array $aMatches = [];

    /**
     * set to true if the file is on a remote server (ie http:// url is given).
     */
    public bool $bIsHTTPResource = false;

    public static function GetInstance(string $sPath): TCMSFile
    {
        $oItem = new self();
        if (!$oItem->Load($sPath)) {
            $oItem = false;
        }

        return $oItem;
    }

    /**
     * rename the file.
     *
     * @param string $sNewName
     *
     * @return bool
     */
    public function Rename($sNewName)
    {
        $sTarget = realpath($this->sDir.'/'.$sNewName);

        try {
            $this->getFileManager()->rename($this->sDir.'/'.$this->sFileName, $sTarget);

            return true;
        } catch (IOExceptionInterface $exception) {
            return false;
        }
    }

    /**
     * get url to file (if it is in the document root). return false if the file is not
     * within the document root.
     *
     * @return string
     */
    public function GetURL()
    {
        $sURL = false;
        $oURLData = TCMSSmartURLData::GetActive();
        $sBase = $_SERVER['DOCUMENT_ROOT'];
        if (substr($this->sPath, 0, strlen($sBase)) == $sBase) {
            $sURL = REQUEST_PROTOCOL.'/'.$oURLData->sDomainName.'/'.substr($this->sPath, strlen($sBase));
        }

        return $sURL;
    }

    /**
     * load the file. returns true if it was loaded, else false.
     *
     * @param string $sPath - full server path to file
     *
     * @return bool
     */
    public function Load($sPath)
    {
        $bLoaded = false;
        if ('http://' == substr($sPath, 0, 7)) {
            $this->bIsHTTPResource = true;
            $this->sPath = $sPath;
            $this->sDir = dirname($sPath);
            $this->sFileName = basename($sPath);
            $iExtensionPos = strrpos($this->sFileName, '.');
            if (false !== $iExtensionPos) {
                $this->sExtension = strtolower(substr($this->sFileName, $iExtensionPos + 1));
                if ('jpeg' == $this->sExtension) {
                    $this->sExtension = 'jpg';
                } elseif ('jpe' == $this->sExtension) {
                    $this->sExtension = 'jpg';
                } elseif ('tiff' == $this->sExtension) {
                    $this->sExtension = 'tif';
                }
            }

            $this->dSizeByte = null;
            $this->sCreated = date('Y-m-d H:i:s');
            $bLoaded = true;
        } else {
            if (file_exists($sPath)) {
                $this->sPath = $sPath;
                $aFileData = pathinfo($this->sPath);

                if (array_key_exists('dirname', $aFileData)) {
                    $this->sDir = $aFileData['dirname'];
                }
                if (array_key_exists('basename', $aFileData)) {
                    $this->sFileName = $aFileData['basename'];
                }
                if (array_key_exists('extension', $aFileData)) {
                    $this->sExtension = strtolower($aFileData['extension']);
                }
                if ('jpeg' == $this->sExtension) {
                    $this->sExtension = 'jpg';
                } elseif ('jpe' == $this->sExtension) {
                    $this->sExtension = 'jpg';
                } elseif ('tiff' == $this->sExtension) {
                    $this->sExtension = 'tif';
                }

                $this->dSizeByte = filesize($sPath);
                $this->sCreated = date('Y-m-d H:i:s', filectime($this->sPath));
                $bLoaded = true;
            }
        }

        return $bLoaded;
    }

    /**
     * return true if the file is a valid cms file.
     *
     * @return bool
     */
    public function IsValidCMSImage()
    {
        $allowedFileTypes = TCMSImage::GetAllowedMediaTypes();
        $bValidType = in_array($this->sExtension, $allowedFileTypes);

        if ($this->bIsHTTPResource) {
            $bIsRGB = true;
        } else {
            $bIsRGB = false;

            $imageInfo = null;
            if ($bValidType) {
                try {
                    $imageInfo = @getimagesize($this->sPath);
                } catch (Exception $e) {
                    // file is no php supported image type
                }

                if (isset($imageInfo) && is_array($imageInfo)) {
                    if (function_exists('image_type_to_extension')) {
                        $realFileExtension = image_type_to_extension($imageInfo[2]);
                    } else {
                        $realFileExtension = $this->sExtension;
                    }
                    $realFileExtension = strtolower($realFileExtension);

                    $realFileExtension = str_replace('.', '', $realFileExtension);
                    if ('jpeg' == $realFileExtension || 'jpe' == $realFileExtension) {
                        $realFileExtension = 'jpg';
                    }

                    if (in_array($realFileExtension, $allowedFileTypes)) {
                        // check for CMYK images
                        if (isset($imageInfo['channels']) && 4 == $imageInfo['channels']) {
                            $bIsRGB = false;
                        } else {
                            $bIsRGB = true;
                        }
                    } else {
                        $bValidType = false;
                    }
                } else {
                    $bValidType = false;
                }
            }
        }

        return $bValidType && $bIsRGB;
    }

    public function __construct()
    {
    }

    private function getFileManager(): Filesystem
    {
        return new Filesystem();
    }
}
