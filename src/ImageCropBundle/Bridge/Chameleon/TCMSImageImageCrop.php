<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ChameleonSystem\ImageCropBundle\Bridge\Chameleon;

use ChameleonSystem\CoreBundle\ServiceLocator;
use ChameleonSystemImageCropBundleBridgeChameleonTCMSImageImageCropAutoParent;
use IPkgCmsFileManager;
use TCMSImage;

class TCMSImageImageCrop extends ChameleonSystemImageCropBundleBridgeChameleonTCMSImageImageCropAutoParent
{
    /**
     * @param int $targetWidth
     * @param int $targetHeight
     * @param int $cropWidth
     * @param int $cropHeight
     * @param int $x
     * @param int $y
     *
     * @return TCMSImage|null
     */
    public function getCroppedImage($targetWidth, $targetHeight, $cropWidth, $cropHeight, $x = 0, $y = 0)
    {
        if (false === $this->CheckAllowThumbnailing()) {
            return null;
        }

        $thumbnail = $this->InitThumbnailing();
        $thumbnail->_isThumbnail = true;
        $thumbnail->aData['width'] = $targetWidth;
        $thumbnail->aData['height'] = $targetHeight;

        $originalExtension = 'jpg';
        if (true === $this->SupportsTransparency()) {
            $originalExtension = 'png';
        }
        $sEffectFileNamePart = '-'.md5('cropped'.$cropWidth.$cropHeight.$x.$y);
        $thumbName = $this->GenerateThumbName($targetWidth, $targetHeight, $sEffectFileNamePart, $originalExtension);
        $thumbPath = $this->GetLocalMediaDirectory(true).$thumbName;
        $thumbnail->aData['path'] = $thumbName;

        $originalSizeImagePointer = null;

        if (true === file_exists($thumbPath)) {
            list($width, $height) = getimagesize($thumbPath);
            $thumbnail->aData['width'] = $width;
            $thumbnail->aData['height'] = $height;

            return $thumbnail;
        }

        if (true === $this->UseImageMagick()) {
            $this->cropImageUsingImageMagick(
                $targetWidth,
                $targetHeight,
                $cropWidth,
                $cropHeight,
                $x,
                $y,
                $thumbPath,
                $originalExtension
            );
        } else {
            $jpgQuality = $this->GetTransformJPGQuality($thumbnail);
            if (false === $jpgQuality) {
                $jpgQuality = 100;
            }

            $this->cropImageUsingGdLib(
                $thumbPath,
                $targetWidth,
                $targetHeight,
                $cropWidth,
                $cropHeight,
                $x,
                $y,
                $jpgQuality
            );
        }

        return $thumbnail;
    }

    /**
     * @param int    $targetWidth
     * @param int    $targetHeight
     * @param int    $cropWidth
     * @param int    $cropHeight
     * @param int    $x
     * @param int    $y
     * @param string $thumbPath
     * @param string $originalExtension
     *
     * @return void
     */
    protected function cropImageUsingImageMagick(
        $targetWidth,
        $targetHeight,
        $cropWidth,
        $cropHeight,
        $x = 0,
        $y = 0,
        $thumbPath,
        $originalExtension
    ) {
        $oImageMagick = $this->GetImageMagicObject();
        $oImageMagick->LoadImage($this->GetLocalMediaDirectory().'/'.$this->aData['path'], $this);

        if (0 === $cropWidth || 0 === $cropHeight) {
            $resizeSizes = $this->getThumbnailProportionsForImageCrop((int) $targetWidth, (int) $targetHeight);
            $oImageMagick->ResizeImage($resizeSizes['width'], $resizeSizes['height']);
            $oImageMagick->cropImageWithCoordinates($targetWidth, $targetHeight, $x, $y);
        } else {
            $oImageMagick->cropImageWithCoordinates($cropWidth, $cropHeight, $x, $y);
            $oImageMagick->ResizeImage($targetWidth, $targetHeight);
        }

        $oImageMagick->SaveToFile($thumbPath);
        $thumbnailRealPath = realpath($thumbPath);
        if (false !== $thumbnailRealPath) {
            $this->thumbnailCreatedHook($thumbnailRealPath, $originalExtension);
        }
    }

    /**
     * @return ImageCropImageMagick
     */
    protected function &GetImageMagicObject()
    {
        $imageMagick = new ImageCropImageMagick();
        $imageMagick->GetImageMagickVersion();
        $imageMagick->setFileManager($this->getPrivateFileManager());
        $imageMagick->Init();

        return $imageMagick;
    }

    /**
     * @return IPkgCmsFileManager
     */
    private function getPrivateFileManager()
    {
        return ServiceLocator::get('chameleon_system_core.filemanager');
    }

    /**
     * @param int $targetWidth
     * @param int $targetHeight
     *
     * @return array - returns keys: width and height with integer values
     */
    private function getThumbnailProportionsForImageCrop($targetWidth, $targetHeight)
    {
        $targetHeightForProportionCalc = $targetHeight;
        $targetWidthForProportionCalc = $targetWidth;

        $thumbDummy = $this->InitThumbnailing();
        $this->GetThumbnailProportions($thumbDummy, $targetWidthForProportionCalc, $targetHeightForProportionCalc);
        $resizeWidth = $thumbDummy->aData['width'];
        $resizeHeight = $thumbDummy->aData['height'];

        return array('width' => $resizeWidth, 'height' => $resizeHeight);
    }

    /**
     * @param string $targetThumbPath
     * @param int    $targetWidth
     * @param int    $targetHeight
     * @param int    $cropWidth
     * @param int    $cropHeight
     * @param int    $x
     * @param int    $y
     * @param int    $jpgQuality
     *
     * @return void
     */
    protected function cropImageUsingGdLib(
        $targetThumbPath,
        $targetWidth,
        $targetHeight,
        $cropWidth,
        $cropHeight,
        $x = 0,
        $y = 0,
        $jpgQuality = 100
    ) {
        $originalSizeImagePointer = $this->GetThumbnailPointer($this);
        $imagePointer = imagecreatetruecolor($targetWidth, $targetHeight);

        if (null === $originalSizeImagePointer) {
            return;
        }

        $this->fastimagecopyresampled(
            $imagePointer,
            $originalSizeImagePointer,
            0,
            0,
            $x,
            $y,
            $targetWidth,
            $targetHeight,
            $cropWidth,
            $cropHeight
        );

        if (true === $this->useUnsharpMask || ('jpg' === $this->GetImageType() || 'png' === $this->GetImageType())) {
            $imagePointer = $this->UnsharpMask($imagePointer);
        }

        imagejpeg($imagePointer, $targetThumbPath, $jpgQuality);
        $thumbnailRealPath = realpath($targetThumbPath);
        if (false !== $thumbnailRealPath) {
            $this->thumbnailCreatedHook($thumbnailRealPath, 'jpg');
        }
        imagedestroy($imagePointer);
        imagedestroy($originalSizeImagePointer);
    }
}
