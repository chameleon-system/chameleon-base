<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

class TPkgSnippetRendererFilter
{
    /**
     * if $bReturnAbsoluteUrl is null the function decides what mode to use.
     * By default return relative urls, but if is set static url return absolute path.
     * If is set to true or false force absolute or relative url.
     *
     * @param string $cmsMediaId
     * @param int $maxWidth
     * @param int $maxHeight
     * @param bool $forceSize
     * @param bool|null $returnAbsoluteUrl
     *
     * @return string
     */
    public static function getThumbnail(
        $cmsMediaId,
        $maxWidth,
        $maxHeight = 2000,
        $forceSize = false,
        $returnAbsoluteUrl = null
    ) {
        if ('//' === substr($cmsMediaId, 0, 2)
            || 'http://' === substr($cmsMediaId, 0, 7)
            || 'https://' === substr($cmsMediaId, 0, 8)
        ) {
            $oString = new TPkgCmsStringUtilities_VariableInjection();

            return $oString->replace($cmsMediaId, ['width' => $maxWidth, 'height' => $maxHeight]);
        }

        $cmsMedia = new TCMSImage();
        if (false === $cmsMedia->Load($cmsMediaId)) {
            $cmsMedia->Load(-1);
        }

        if (true === $forceSize) {
            $thumbnail = $cmsMedia->GetCenteredFixedSizeThumbnail($maxWidth, $maxHeight, 0, 'ffffff', [], true);
        } else {
            $thumbnail = $cmsMedia->GetThumbnail($maxWidth, $maxHeight);
        }

        if (null === $thumbnail) {
            return '';
        }

        if (null !== $returnAbsoluteUrl) {
            return (true === $returnAbsoluteUrl) ? ($thumbnail->GetFullURL()) : ($thumbnail->GetRelativeURL());
        }

        return $thumbnail->GetFullURL();
    }
}
