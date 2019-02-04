<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ChameleonSystem\ImageCropBundle\Twig;

use ChameleonSystem\CoreBundle\Service\LanguageServiceInterface;
use ChameleonSystem\CoreBundle\Service\RequestInfoServiceInterface;
use ChameleonSystem\ImageCrop\Interfaces\CropImageServiceInterface;
use ChameleonSystem\ImageCrop\Interfaces\ImageCropPresetDataAccessInterface;
use TCMSImage;
use Twig_Extension;
use Twig_SimpleFilter;

class CropImageExtension extends Twig_Extension
{
    /**
     * @var ImageCropPresetDataAccessInterface
     */
    private $imageCropPresetDataAccess;

    /**
     * @var CropImageServiceInterface
     */
    private $cropImageService;

    /**
     * @var LanguageServiceInterface
     */
    private $languageService;

    /**
     * @var RequestInfoServiceInterface
     */
    private $requestInfoService;

    /**
     * @param ImageCropPresetDataAccessInterface $imageCropPresetDataAccess
     * @param CropImageServiceInterface          $cropImageService
     * @param LanguageServiceInterface           $languageService
     * @param RequestInfoServiceInterface        $requestInfoService
     */
    public function __construct(
        ImageCropPresetDataAccessInterface $imageCropPresetDataAccess,
        CropImageServiceInterface $cropImageService,
        LanguageServiceInterface $languageService,
        RequestInfoServiceInterface $requestInfoService
    ) {
        $this->imageCropPresetDataAccess = $imageCropPresetDataAccess;
        $this->cropImageService = $cropImageService;
        $this->languageService = $languageService;
        $this->requestInfoService = $requestInfoService;
    }

    /**
     * {@inheritdoc}
     */
    public function getFilters()
    {
        return array(
            new Twig_SimpleFilter('imageUrlWithCropFallbackPreset', array($this, 'imageUrlWithCropFallbackPreset')),
            new Twig_SimpleFilter('imageUrlWithCropFallbackSize', array($this, 'imageUrlWithCropFallbackSize')),
            new Twig_SimpleFilter('imageHasCropDataForPreset', array($this, 'imageHasCropDataForPreset')),
            new Twig_SimpleFilter('imageUrlWithCropSize', array($this, 'imageUrlWithCropSize')),
        );
    }

    /**
     * @param string      $imageId
     * @param string|null $cropId
     * @param string      $presetIdOrSystemName
     *
     * @return string|null
     */
    public function imageUrlWithCropFallbackPreset($imageId, $cropId, $presetIdOrSystemName)
    {
        if (null !== $cropId && '' !== $cropId) {
            $image = $this->cropImageService->getCroppedImageForCmsMediaIdAndCropId(
                $imageId,
                $cropId,
                $this->getLanguageId()
            );
        } else {
            $presetId = $presetIdOrSystemName;
            $preset = $this->imageCropPresetDataAccess->getPresetBySystemName($presetIdOrSystemName);
            if (null !== $preset) {
                $presetId = $preset->getId();
            }

            $image = $this->cropImageService->getCroppedImageForCmsMediaIdAndPresetId(
                $imageId,
                $presetId,
                $this->getLanguageId()
            );
        }

        if (null === $image) {
            return null;
        }

        return $image->getImageUrl();
    }

    /**
     * @return string
     */
    private function getLanguageId()
    {
        if ($this->requestInfoService->isBackendMode()) {
            return $this->languageService->getActiveEditLanguage()->id;
        }

        return $this->languageService->getActiveLanguageId();
    }

    /**
     * @param string $imageId
     * @param string $presetIdOrSystemName
     *
     * @return bool
     */
    public function imageHasCropDataForPreset($imageId, $presetIdOrSystemName)
    {
        $presetId = $presetIdOrSystemName;
        $preset = $this->imageCropPresetDataAccess->getPresetBySystemName($presetIdOrSystemName);
        if (null !== $preset) {
            $presetId = $preset->getId();
        }
        $image = $this->cropImageService->getCroppedImageForCmsMediaIdAndPresetId(
            $imageId,
            $presetId,
            $this->getLanguageId(),
            false
        );

        return null !== $image;
    }

    /**
     * @param string $imageId
     * @param string $cropId
     * @param int    $width
     * @param int    $height
     *
     * @return string
     */
    public function imageUrlWithCropFallbackSize($imageId, $cropId, $width = 100, $height = 100)
    {
        $image = $this->cropImageService->getCroppedImageForCmsMediaIdAndCropId(
            $imageId,
            $cropId,
            $this->getLanguageId()
        );

        if (null === $image) {
            return $this->getTrimmedImage($imageId, $width, $height);
        }

        return $image->getImageUrl();
    }

    /**
     * @param string $imageId
     * @param int    $width
     * @param int    $height
     *
     * @return string
     */
    private function getTrimmedImage($imageId, $width, $height)
    {
        $image = new TCMSImage();
        if (false === $image->Load($imageId)) {
            $image->Load(-1); //error image
        }

        $thumb = $image->GetForcedSizeThumbnail($width, $height);

        return $thumb->GetFullURL();
    }

    /**
     * @param string      $imageId
     * @param string|null $cropId
     * @param int         $targetWidth
     * @param int         $targetHeight
     *
     * @return null|string
     */
    public function imageUrlWithCropSize($imageId, $cropId, $targetWidth = 0, $targetHeight = 0)
    {
        if (null === $cropId || '' === $cropId) {
            return null;
        }

        $image = $this->cropImageService->getCroppedImageForCmsMediaIdAndCropId(
            $imageId,
            $cropId,
            $this->getLanguageId(),
            $targetWidth,
            $targetHeight
        );
        if (null === $image) {
            return null;
        }

        return $image->getImageUrl();
    }
}
