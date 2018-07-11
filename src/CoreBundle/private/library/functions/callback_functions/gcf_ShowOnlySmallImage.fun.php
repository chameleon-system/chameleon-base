<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

function gcf_ShowOnlySmallImage($field, $row, $fieldName)
{
    $oImage = new TCMSImage();
    /** @var $oImage TCMSImage */
    // make sure we only fetch one.. if there is more than one
    $imageId = $field;
    if (false !== strpos($field, ',')) {
        $tmp = explode(',', $field);
        if (is_array($tmp) && count($tmp) > 0) {
            $imageId = $tmp[0];
        }
    }
    $oImage->Load($imageId);
    $oImage->useLightBox = false;
    $oImage->useUnsharpMask = false;

    $sImageType = $oImage->GetImageType();
    if ('flv' === $sImageType || 'f4v' === $sImageType) {
        $imageTag = $oImage->GetThumbnailTag(100, 75, null, null, '');
    } else {
        $oThumbnail = $oImage->GetThumbnail(100, 100);
        $imageTag = '<img src="'.TGlobal::OutHTML($oThumbnail->GetFullURL()).'" id="cmsimage_'.TGlobal::OutHTML($imageId).'" width="'.TGlobal::OutHTML($oThumbnail->aData['width']).'" height="'.TGlobal::OutHTML($oThumbnail->aData['height']).'" hspace="0" vspace="0" border="0" />';
    }

    return $imageTag;
}
