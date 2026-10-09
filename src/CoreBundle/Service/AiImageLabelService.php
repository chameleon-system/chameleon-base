<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace ChameleonSystem\CoreBundle\Service;

class AiImageLabelService
{
    private const MAX_WIDTH_PERCENT = 10;
    private const PADDING_PERCENT = 2;
    private const MIN_WIDTH = 5;
    private const CACHE_VERSION = 3;

    public function isEnabled(array $mediaData): bool
    {
        return isset($mediaData['ai_generated_image']) && '1' === (string) $mediaData['ai_generated_image'];
    }

    public function getVariant(array $mediaData): string
    {
        return isset($mediaData['ai_label_variant']) && 'light' === $mediaData['ai_label_variant'] ? 'light' : 'dark';
    }

    public function getAssetPath(string $variant): ?string
    {
        $assetPath = dirname(__DIR__).'/Resources/public/images/ai-label-'.$variant.'.png';

        return is_file($assetPath) ? $assetPath : null;
    }

    public function getCacheKeySuffix(bool $enabled, string $variant): string
    {
        if (!$enabled) {
            return '';
        }

        return '_ai-label-'.$variant
            .'-w'.self::MAX_WIDTH_PERCENT
            .'-p'.self::PADDING_PERCENT
            .'-min'.self::MIN_WIDTH
            .'-v'.self::CACHE_VERSION;
    }

    /**
     * @return array{assetPath: string, width: int, height: int, padding: int}|null
     */
    public function getPlacement(array $mediaData, int $canvasWidth, int $canvasHeight): ?array
    {
        if (!$this->isEnabled($mediaData)) {
            return null;
        }

        return $this->calculatePlacement($this->getAssetPath($this->getVariant($mediaData)), $canvasWidth, $canvasHeight);
    }

    /**
     * @return array{assetPath: string, width: int, height: int, padding: int}|null
     */
    public function calculatePlacement(?string $assetPath, int $canvasWidth, int $canvasHeight): ?array
    {
        if (null === $assetPath) {
            return null;
        }

        $labelSize = getimagesize($assetPath);
        if (false === $labelSize || 0 === $labelSize[0] || 0 === $labelSize[1]) {
            return null;
        }

        $labelWidth = min($labelSize[0], (int) floor($canvasWidth / 100 * self::MAX_WIDTH_PERCENT));
        if ($labelWidth < self::MIN_WIDTH) {
            return null;
        }

        $labelHeight = (int) round($labelSize[1] * ($labelWidth / $labelSize[0]));
        $padding = max(4, (int) round(min($canvasWidth, $canvasHeight) / 100 * self::PADDING_PERCENT));
        if ($labelWidth + (2 * $padding) > $canvasWidth || $labelHeight + (2 * $padding) > $canvasHeight) {
            return null;
        }

        return [
            'assetPath' => $assetPath,
            'width' => $labelWidth,
            'height' => $labelHeight,
            'padding' => $padding,
        ];
    }

    /**
     * @param array{assetPath: string, width: int, height: int, padding: int}|null $placement
     */
    public function applyToGd(\GdImage $imageResource, ?array $placement): \GdImage
    {
        if (null === $placement) {
            return $imageResource;
        }

        $labelResource = imagecreatefrompng($placement['assetPath']);
        if (false === $labelResource) {
            return $imageResource;
        }

        $scaledLabel = imagecreatetruecolor($placement['width'], $placement['height']);
        imagealphablending($scaledLabel, false);
        imagesavealpha($scaledLabel, true);
        $transparent = imagecolorallocatealpha($scaledLabel, 0, 0, 0, 127);
        imagefill($scaledLabel, 0, 0, $transparent);
        imagecopyresampled(
            $scaledLabel,
            $labelResource,
            0,
            0,
            0,
            0,
            $placement['width'],
            $placement['height'],
            imagesx($labelResource),
            imagesy($labelResource)
        );

        imagealphablending($imageResource, true);
        imagecopy(
            $imageResource,
            $scaledLabel,
            imagesx($imageResource) - $placement['width'] - $placement['padding'],
            imagesy($imageResource) - $placement['height'] - $placement['padding'],
            0,
            0,
            $placement['width'],
            $placement['height']
        );

        imagedestroy($scaledLabel);
        imagedestroy($labelResource);

        return $imageResource;
    }

    /**
     * @param array{assetPath: string, width: int, height: int, padding: int}|null $placement
     */
    public function applyToImagick(\Imagick $image, ?array $placement): void
    {
        if (null === $placement) {
            return;
        }

        $label = new \Imagick($placement['assetPath']);
        try {
            $label->resizeImage($placement['width'], $placement['height'], \Imagick::FILTER_LANCZOS, 1);
            $image->compositeImage(
                $label,
                \Imagick::COMPOSITE_OVER,
                $image->getImageWidth() - $placement['width'] - $placement['padding'],
                $image->getImageHeight() - $placement['height'] - $placement['padding']
            );
        } finally {
            $label->clear();
        }
    }
}
