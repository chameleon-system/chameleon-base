<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

class TPkgCmsTextfieldImage extends AbstractViewMapper
{
    /**
     * {@inheritdoc}
     */
    public function GetRequirements(IMapperRequirementsRestricted $oRequirements): void
    {
        $oRequirements->NeedsSourceObject('aTagProperties', 'array', []);
        $oRequirements->NeedsSourceObject('oImage', 'TCMSImage', null);
        $oRequirements->NeedsSourceObject('sFullImageURL', 'string', null, true);
        $oRequirements->NeedsSourceObject('sImageGroupName', 'string', null, true);
        $oRequirements->NeedsSourceObject('iThumbnailSizeThreshold', 'integer', 5, true);
        $oRequirements->NeedsSourceObject('aEffects', 'array', [], true);
        $oRequirements->NeedsSourceObject('isForceThumbnailGenerationOnFullSizeImagesEnabled', 'bool', false, false);
    }

    /**
     * {@inheritdoc}
     */
    public function Accept(IMapperVisitorRestricted $oVisitor, $bCachingEnabled, IMapperCacheTriggerRestricted $oCacheTriggerManager): void
    {
        /** @var TCMSImage $oImage */
        $oImage = $oVisitor->GetSourceObject('oImage');

        /** @var array{width: int, height: int} $aTagProperties */
        $aTagProperties = $oVisitor->GetSourceObject('aTagProperties');
        $oVisitor->SetMappedValue('aTagProperties', $aTagProperties);

        /** @var string $sFullImageURL */
        $sFullImageURL = $oVisitor->GetSourceObject('sFullImageURL');
        $oVisitor->SetMappedValue('sFullImageURL', $sFullImageURL);

        /** @var string $sImageGroupName */
        $sImageGroupName = $oVisitor->GetSourceObject('sImageGroupName');
        $oVisitor->SetMappedValue('sImageGroupName', $sImageGroupName);

        /**
         * if the size difference between the thumbnail and the original image is smaller than x pixel (5 by default)
         * the extra link for the original image should not be rendered.
         *
         * @var int
         */
        $iThumbnailSizeThreshold = $oVisitor->GetSourceObject('iThumbnailSizeThreshold');

        // check if full size image link (lightbox) is needed.
        // if the thumbnail has the full size we don`t need a big image
        $bFullsizeImageBiggerThanThumbnail = true;
        $iWidth = 0;
        $iHeight = 0;
        if (isset($oImage->aData) && isset($oImage->aData['height'])) {
            $iHeight = $oImage->aData['height'];
        }
        if (isset($oImage->aData) && isset($oImage->aData['width'])) {
            $iWidth = $oImage->aData['width'];
        }
        if (0 == $iHeight) {
            $iHeight = 150;
        }
        if (0 == $iWidth) {
            $iWidth = 150;
        }
        // if image should be bigger than original full size image we change original image sizes
        if ($aTagProperties['width'] > $iWidth) {
            $iWidth = $aTagProperties['width'];
        }
        if ($aTagProperties['height'] > $iHeight) {
            $iHeight = $aTagProperties['height'];
        }
        if (abs($aTagProperties['width'] - $iWidth) < $iThumbnailSizeThreshold && abs($aTagProperties['height'] - $iHeight) < $iThumbnailSizeThreshold) {
            $bFullsizeImageBiggerThanThumbnail = false;
        }
        $oVisitor->SetMappedValue('bFullsizeImageBiggerThanThumbnail', $bFullsizeImageBiggerThanThumbnail);

        /** @var string[] $aEffects */
        $aEffects = $oVisitor->GetSourceObject('aEffects');

        /** @var bool $isForceThumbnailGenerationOnFullSizeImagesEnabled */
        $isForceThumbnailGenerationOnFullSizeImagesEnabled = $oVisitor->GetSourceObject('isForceThumbnailGenerationOnFullSizeImagesEnabled');

        if (true === $isForceThumbnailGenerationOnFullSizeImagesEnabled) {
            $oThumb = $oImage->GetThumbnail($aTagProperties['width'], $aTagProperties['height'], true, $aEffects);
        } else {
            if ($iWidth != $aTagProperties['width'] || $iHeight != $aTagProperties['height'] || count($aEffects) > 0) {
                $oThumb = $oImage->GetThumbnail($aTagProperties['width'], $aTagProperties['height'], true, $aEffects);
            } else {
                $oThumb = $oImage;
            }
        }

        $sThumbnailURL = $oThumb->GetFullURL();

        $oVisitor->SetMappedValue('sThumbnailURL', $sThumbnailURL);

        if (true === IMAGE_RENDERING_RESPONSIVE) {
            $iMediumSize = 800; // based on bootstrap 3 @screen-sm-min

            if (defined('IMAGE_RENDERING_RESPONSIVE_TABLET_SCREEN_SIZE')) {
                $iMediumSize = IMAGE_RENDERING_RESPONSIVE_TABLET_SCREEN_SIZE;
            }

            $oVisitor->SetMappedValue('iMediumScreenSize', $iMediumSize);

            $thumbWidth = $oThumb->aData['width'] ?? 0;
            if ($thumbWidth > $iMediumSize) {
                $oThumb = $oImage->GetThumbnail($iMediumSize, 2000, true, $aEffects);
                $sMediumThumbnailURL = $oThumb->GetFullURL();
            } else {
                $sMediumThumbnailURL = $sThumbnailURL;
            }

            $oVisitor->SetMappedValue('sMediumThumbnailURL', $sMediumThumbnailURL);

            $iSmallSize = 500; // based on bootstrap 3 @screen-xs-min

            if (defined('IMAGE_RENDERING_RESPONSIVE_MOBILE_SCREEN_SIZE')) {
                $iSmallSize = IMAGE_RENDERING_RESPONSIVE_MOBILE_SCREEN_SIZE;
            }

            $oVisitor->SetMappedValue('iSmallScreenSize', $iSmallSize);

            if ($thumbWidth > $iSmallSize) {
                $oThumb = $oImage->GetThumbnail($iSmallSize, 2000, true, $aEffects);
                $sSmallThumbnailURL = $oThumb->GetFullURL();
            } else {
                $sSmallThumbnailURL = $sThumbnailURL;
            }

            $oVisitor->SetMappedValue('sSmallThumbnailURL', $sSmallThumbnailURL);
        }
    }
}
