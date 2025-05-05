<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * @deprecated since 6.3.0 - method moved to TCMSListManagerCMSUser::callbackCmsUserWithImage(). Use 'callbackCmsUserWithImage' for field callback configuration.
 */
function gcf_CMSUserWithImage($name, $row, $fieldName)
{
    $imageTag = '<i class="fas fa-user"></i> ';

    $imageId = $row['images'];
    if ($imageId >= 1000 || !is_numeric($imageId)) {
        $oImage = new TCMSImage();
        /** @var $oImage TCMSImage */
        if (!is_null($oImage)) {
            $oImage->Load($imageId);
            $oThumbnail = $oImage->GetThumbnail(16, 16);
            if (!is_null($oThumbnail)) {
                $oBigThumbnail = $oImage->GetThumbnail(400, 400);
                $imageTag = '<img src="'.TGlobal::OutHTML($oThumbnail->GetFullURL()).'" width="'.TGlobal::OutHTML($oThumbnail->aData['width']).'" height="'.TGlobal::OutHTML($oThumbnail->aData['height'])."\" hspace=\"0\" vspace=\"0\" border=\"0\" onclick=\"CreateMediaZoomDialogFromImageURL('".$oBigThumbnail->GetFullURL()."','".TGlobal::OutHTML($oBigThumbnail->aData['width'])."','".TGlobal::OutHTML($oBigThumbnail->aData['height'])."')\" style=\"cursor: hand; cursor: pointer; margin-right:10px\" align=\"left\" />";
            }
        }
    }

    if ('' !== $row['firstname']) {
        $name .= ', '.$row['firstname'];
    }

    $returnVal = "{$imageTag}".TGlobal::OutHTML($name);

    return $returnVal;
}
