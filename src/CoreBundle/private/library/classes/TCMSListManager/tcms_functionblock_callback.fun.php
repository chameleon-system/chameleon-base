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
use ChameleonSystem\SecurityBundle\Service\SecurityHelperAccess;
use ChameleonSystem\SecurityBundle\Voter\CmsPermissionAttributeConstants;

/**
 * function displays the function icons (delete, copy, etc) for table lists
 * returns HTML.
 *
 * @deprecated the method moved to TCMSListManagerFullGroupTable to allow easier extension
 *
 * @param int $id
 * @param array $row
 *
 * @return string
 */
function tcms_functionblock_callback($id, $row)
{
    /** @var SecurityHelperAccess $securityHelper */
    $securityHelper = ServiceLocator::get(SecurityHelperAccess::class);

    $cms_user_new_record_right = $securityHelper->isGranted(CmsPermissionAttributeConstants::TABLE_EDITOR_NEW, $_SESSION['_tmpCurrentTableName']);
    $sDeleteButton = tcms_functionblock_deletebutton($id, $row);
    $showButtons = (!empty($sDeleteButton) || $cms_user_new_record_right || (isset($row['confirmed']) && !empty($row['confirmed'])));
    $returnValue = '<div class="tablelistfunctions">';
    if ($showButtons) {
        if ('cms_tpl_page' == $_SESSION['_tmpCurrentTableName'] && $securityHelper->isGranted('CMS_RIGHT_CMS_PAGE_PROPERTY')) {
            $returnValue .= '<img src="'.URL_CMS."/images/icons/application_edit.png\" onclick=\"document.cmsform._id.value='".$row['id']."';document.cmsform._mode.value='display';document.cmsform.submit();\" onMouseOver=\"$('#functionTitle_'+".$row['cmsident'].").html('".ServiceLocator::get('translator')->trans('chameleon_system_core.list.page_settings')."');\" onMouseOut=\"$('#functionTitle_'+".$row['cmsident'].").html('');\" />";
        }
        $returnValue .= $sDeleteButton;
        if (true == $cms_user_new_record_right && 'cms_tpl_page' != $_SESSION['_tmpCurrentTableName']) {
            $returnValue .= '<img src="'.URL_CMS."/images/icons/page_copy.png\" onclick=\"document.cmsform.elements['module_fnc[contentmodule]'].value='DatabaseCopy';document.cmsform.id.value='".$row['id']."';document.cmsform.submit();\" onMouseOver=\"$('#functionTitle_'+".$row['cmsident'].").html('".ServiceLocator::get('translator')->trans('chameleon_system_core.action.copy')."');\" onMouseOut=\"$('#functionTitle_'+".$row['cmsident'].").html('');\" />";
        }
    } else {
        $returnValue = '&nbsp;';
    }

    $returnValue .= '<div id="functionTitle_'.$row['cmsident'].'" class="functionTitle"></div>';
    $returnValue .= '</div>';

    return $returnValue;
}

/**
 * returns a filetype icon that is linked with the download file.
 *
 * @deprecated the method moved to TCMSListManagerDocumentManager CallBackGenerateDownloadLink() to allow easier extension
 *
 * @param string $id
 * @param array $row
 *
 * @return string
 */
function tcms_GenerateDownloadLink_callback($id, $row)
{
    $oFile = new TCMSDownloadFile();
    /* @var $oFile TCMSDownloadFile */
    $oFile->Load($row['id']);
    $sDownloadLink = $oFile->getDownloadHtmlTag(false, true, true);

    return $sDownloadLink;
}

/**
 * function displays the function icons (delete, copy, etc) for MLT table lists
 * returns HTML.
 *
 * @deprecated the method moved to TCMSListManagerMLT CallBackMLTFunctionBlock() to allow easier extension
 *
 * @param int $id
 * @param array $row
 *
 * @return string
 */
function tcms_MLTfunctionblock_callback($id, $row)
{
    return '<img src="'.URL_CMS.'/images/icons/link_break.png" onclick="deleteConnection(\''.TGlobal::OutJS($row['id']).'\');" title="'.ServiceLocator::get('translator')->trans('chameleon_system_core.action.remove_connection').'" />';
}

/**
 * returns a preview image with zoom on click.
 *
 * @deprecated the method moved to TCMSListManagerFullGroupTable CallBackImageWithZoom() to allow easier extension
 *
 * @param string $path
 * @param array $row
 *
 * @return string
 */
function tcms_image_callback($path, $row)
{
    $oImage = new TCMSImage();
    /* @var $oImage TCMSImage */
    $oImage->Load($row['id']);
    $image = '';

    $oThumb = $oImage->GetThumbnail(80, 80);
    /** @var $oThumb TCMSImage */
    if (!is_null($oThumb)) {
        $oBigThumbnail = $oImage->GetThumbnail(400, 400);
        $imageZoomFnc = "CreateMediaZoomDialogFromImageURL('".$oBigThumbnail->GetFullURL()."','".TGlobal::OutHTML($oBigThumbnail->aData['width'])."','".TGlobal::OutHTML($oBigThumbnail->aData['height'])."');event.cancelBubble=true;return false;";
        $image = '<img src="'.$oThumb->GetFullURL()."\" id=\"cmsimage_{$row['id']}\" style=\"padding:3px\" width=\"{$oThumb->aData['width']}\" height=\"{$oThumb->aData['height']}\" border=\"0\" onclick=\"{$imageZoomFnc}\" />";
    }

    return $image;
}

/**
 * returns an image tag of the full image (not thumbnail)
 * not used in the core (maybe deprecated?).
 *
 * @param string $path
 * @param array $row
 *
 * @return string
 */
function tcms_Fullimage_callback($path, $row)
{
    $oImage = new TCMSImage();
    /* @var $oImage TCMSImage */
    $oImage->Load($row['id']);
    $image = '';

    $oThumb = $oImage->GetThumbnail(100, 100);

    if (null !== $oThumb) {
        $imageZoomFnc = "CreateMediaZoomDialogFromImageURL('".$oImage->GetFullURL()."','".TGlobal::OutHTML($oImage->aData['width'])."','".TGlobal::OutHTML($oImage->aData['height'])."');event.cancelBubble=true;return false;";
        $image = '<img src="'.$oThumb->GetFullURL()."\" id=\"cmsimage_{$row['id']}\" style=\"padding:3px\" width=\"{$oThumb->aData['width']}\" height=\"{$oThumb->aData['height']}\" border=\"0\" onclick=\"{$imageZoomFnc}\" />";
    }

    return $image;
}

/**
 * returns a checkbox field for image file selection with javascript onlick.
 *
 * @deprecated the method moved to TCMSListManagerFullGroupTable CallBackMediaSelectBox() to allow easier extension
 *
 * @param string $id
 * @param array $row
 *
 * @return string
 */
function tcms_mediaSelectBox_callback($id, $row)
{
    $html = "<input type=\"checkbox\" name=\"functionSelection[]\" value=\"{$id}\" onclick=\"parent.ChangeFileSelection('{$id}')\" />";

    return $html;
}

/**
 * returns a checkbox field for assigned file selection with javascript onlick.
 *
 * @deprecated the method moved to TCMSListManagerDocumentManagerSelected CallBackDocumentAssignedSelectBox() to allow easier extension
 *
 * @param string $id
 * @param array $row
 *
 * @return string
 */
function tcms_mediaAssignedSelectBox_callback($id, $row)
{
    $html = "<input type=\"checkbox\" name=\"functionSelection[]\" value=\"{$id}\" onclick=\"parent.ChangeAssignedFileSelection('{$id}')\" />";

    return $html;
}

/**
 * returns the filetype as remdered html.
 *
 * @deprecated the method moved to TCMSListManagerDocumentChooser CallBackDocumentFileType() to allow easier extension
 *
 * @param string $id
 * @param array $row
 *
 * @return string
 */
function tcms_documentFileType_callback($id, $row)
{
    $oFileDownload = new TCMSDownloadFile();
    /* @var $oFileDownload TCMSDownloadFile */
    $oFileDownload->Load($row['id']);

    $html = $oFileDownload->GetPlainFileTypeIcon();

    return $html;
}

/**
 * returns the document filename croped to 25 chars max.
 *
 * @deprecated the method moved to TCMSListManagerFullGroupTable CallBackFilenameShort() to allow easier extension
 *
 * @param string $filename
 * @param array $row
 *
 * @return string
 */
function tcms_documentFilenameShort_callback($filename, $row)
{
    $shortFilename = $filename;
    if (mb_strlen($shortFilename) > 25) {
        $shortFilename = mb_substr($shortFilename, 0, 25).'...';
    }

    return $shortFilename;
}

/**
 * returns the document filesize.
 *
 * @deprecated the method moved to TCMSListManagerFullGroupTable CallBackHumanRedableFileSize() to allow easier extension
 *
 * @param string $fileSize
 * @param array $row
 *
 * @return string
 */
function tcms_HumanRedableFileSize_callback($fileSize, $row)
{
    $fileSize = TCMSDownloadFile::GetHumanReadableFileSize($fileSize);

    return $fileSize;
}

/**
 * the function block for the template engine (my webpages).
 *
 * @deprecated  the method moved to TCMSListManagerWebpages CallBackWebpageFunctionBlock() to allow easier extension
 *
 * @param string $id - id of the current page
 * @param array $row - all field/value pairs of the page
 */
function tcms_webpagefunctionblock_callback($id, $row)
{
    $returnValue = '<div class="tablelistfunctions">';
    $returnValue .= tcms_functionblock_deletebutton($id, $row);
    /** @var SecurityHelperAccess $securityHelper */
    $securityHelper = ServiceLocator::get(SecurityHelperAccess::class);
    if ($securityHelper->isGranted('CMS_RIGHT_CMS_PAGE_PROPERTY')) {
        $returnValue .= '<img src="'.URL_CMS."/images/icons/application_edit.png\" onclick=\"EditRecordInList('".TGlobal::OutHTML($row['id'])."');\" onMouseOver=\"$('#functionTitle_'+".$row['cmsident'].").html('".ServiceLocator::get('translator')->trans('chameleon_system_core.list.page_settings')."');\" onMouseOut=\"$('#functionTitle_'+".$row['cmsident'].").html('');\" />";
    }

    $returnValue .= '<div id="functionTitle_'.$row['cmsident'].'" class="functionTitle"></div>';
    $returnValue .= '</div>';

    return $returnValue;
}

/**
 * shows the delete button.
 *
 * @deprecated the method moved to TCMSListManagerFullGroupTable to allow easier extension
 *
 * @param string $id
 * @param array $row
 *
 * @return string
 */
function tcms_functionblock_deletebutton($id, $row)
{
    $returnValue = '';
    /** @var SecurityHelperAccess $securityHelper */
    $securityHelper = ServiceLocator::get(SecurityHelperAccess::class);
    $cms_user_delete_right = $securityHelper->isGranted(CmsPermissionAttributeConstants::TABLE_EDITOR_DELETE, $_SESSION['_tmpCurrentTableName']);
    if (true == $cms_user_delete_right) {
        $returnValue .= '<img src="'.URL_CMS."/images/icons/cross.png\" onclick=\"DeleteRecord('{$row['id']}');\" onMouseOver=\"$('#functionTitle_'+".$row['cmsident'].").html('".ServiceLocator::get('translator')->trans('chameleon_system_core.action.delete')."');\" onMouseOut=\"$('#functionTitle_'+".$row['cmsident'].").html('');\" />";
    }

    return $returnValue;
}

/**
 * return the delete button for mlt records
 * not used in the core anymore, so maybe deprecated.
 *
 * @param string $id
 * @param array $row
 *
 * @return string
 */
function tcms_MLTfunctionblock_deletebutton($id, $row)
{
    $returnValue = '';
    /** @var SecurityHelperAccess $securityHelper */
    $securityHelper = ServiceLocator::get(SecurityHelperAccess::class);
    $cms_user_delete_right = $securityHelper->isGranted(CmsPermissionAttributeConstants::TABLE_EDITOR_DELETE, $_SESSION['_tmpCurrentTableName']);
    if (true == $cms_user_delete_right) {
        $returnValue .= '<img src="'.URL_CMS."/images/icons/cross.png\" onclick=\"if (confirm('".TGlobal::OutJS(ServiceLocator::get('translator')->trans('chameleon_system_core.action.confirm_delete'))."')) {document.cmsformdel.elements['module_fnc[contentmodule]'].value='Delete';document.cmsformdel.id.value='{$row['id']}';document.cmsformdel.submit();}\" onMouseOver=\"$('#functionTitle_'+".$row['cmsident'].").html('".ServiceLocator::get('translator')->trans('chameleon_system_core.action.delete')."');\" onMouseOut=\"$('#functionTitle_'+".$row['cmsident'].").html('');\" />";
    }

    return $returnValue;
}

/**
 * returns checkbox field for multiple file selections.
 *
 * @deprecated the method moved to TCMSListManagerFullGroupTable CallBackDrawListItemSelectbox() to allow easier extension
 *
 * @param string $id
 * @param array $row
 * @param string $sFieldName
 *
 * @return string
 */
function tcms_drawlistitemselectbox($id, $row, $sFieldName)
{
    $html = '';
    /** @var SecurityHelperAccess $securityHelper */
    $securityHelper = ServiceLocator::get(SecurityHelperAccess::class);
    if ($securityHelper->isGranted(CmsPermissionAttributeConstants::TABLE_EDITOR_DELETE, $_SESSION['_tmpCurrentTableName'])) {
        $html = '<input type="checkbox" name="aInputIdList[]" value="'.TGlobal::OutHTML($id).'" />';
    }

    return $html;
}
