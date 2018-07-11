<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

function gcf_ShowImage($field, $row, $fieldName)
{
    $imageTag = '';
    // make sure we only fetch one.. if there is more than one
    $imageId = $field;
    if (false !== strpos($field, ',')) {
        $tmp = explode(',', $field);
        if (is_array($tmp) && count($tmp) > 0) {
            $imageId = $tmp[0];
        }
    }
    if ($imageId >= 1000 || !is_numeric($imageId)) {
        $oImage = new TCMSImage();
        /** @var $oImage TCMSImage */
        if (isset($oImage) && !is_null($oImage) && is_object($oImage)) {
            $oImage->useUnsharpMask = false;
            $oImage->Load($imageId);

            if ($oImage->IsFlashMovie() || $oImage->IsExternalMovie()) {
                $imageTag = $oImage->GetThumbnailTag(100, 75, null, null, '');
            } else {
                $oBigThumbnail = $oImage->GetThumbnail(400, 400);
                $oThumbnail = $oImage->GetThumbnail(100, 100);
                $imageZoomFnc = "CreateMediaZoomDialogFromImageURL('".$oBigThumbnail->GetFullURL()."','".TGlobal::OutHTML($oBigThumbnail->aData['width'])."','".TGlobal::OutHTML($oBigThumbnail->aData['height'])."');event.cancelBubble=true;return false;";
                $imageTag = '<img src="'.TGlobal::OutHTML($oThumbnail->GetFullURL()).'" id="cmsimage_'.TGlobal::OutHTML($imageId).'" width="'.TGlobal::OutHTML($oThumbnail->aData['width']).'" height="'.TGlobal::OutHTML($oThumbnail->aData['height'])."\" hspace=\"0\" vspace=\"0\" border=\"0\" style=\"cursor: hand; cursor: pointer;\" onclick=\"{$imageZoomFnc}\" />";
            }
        }
    }

    return $imageTag;
}
