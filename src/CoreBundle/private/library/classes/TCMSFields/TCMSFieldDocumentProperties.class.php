<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

class TCMSFieldDocumentProperties extends TCMSField
{
    public function GetHTML()
    {
        $oFileType = new TCMSRecord('cms_filetype', $this->oTableRow->sqlData['cms_filetype_id']);
        $fileTypeIconPath = TGlobal::GetStaticURLToWebLib(URL_FILETYPE_ICONS_LOW_QUALITY.$oFileType->sqlData['file_extension'].'.png');

        $fileSize = TCMSDownloadFile::GetHumanReadableFileSize($this->oTableRow->sqlData['filesize']);

        $oDownloadItem = new TCMSDownloadFile();
        /** @var $oDownloadItem TCMSDownloadFile */
        $oDownloadItem->table = 'cms_document';
        $oDownloadItem->Load($this->oTableRow->sqlData['id']);
        $sShowDownloadURL = $oDownloadItem->getBackendDownloadLink(false);
        $sDownloadURL = $oDownloadItem->getBackendDownloadLink(true);

        $html = '<input type="hidden" id="'.TGlobal::OutHTML($this->name).'" name="'.TGlobal::OutHTML($this->name).'" value="'.TGlobal::OutHTML($this->data).'" />';
        $html .= '<table border="0" class="table table-sm table-striped">
        <tr>
          <td width="60">ID:</td>
          <td>'.$oDownloadItem->id.'</td>
        </tr>
        <tr>
          <td width="60">'.TGlobal::Translate('chameleon_system_core.text.file_type').':</td>
          <td><img src ="'.$fileTypeIconPath.'" width="16" height="16" style="float: left; margin-right: 10px;" /><div style="float: left;">'.TGlobal::OutHTML($oFileType->GetName()).' (.'.TGlobal::OutHTML($oFileType->sqlData['file_extension']).")</div></td>
        </tr>\n";

        if (!empty($this->oTableRow->sqlData['hidden_image_width']) && !empty($this->oTableRow->sqlData['hidden_image_height'])) {
            $html .= '<tr>
            <td>'.TGlobal::Translate('chameleon_system_core.text.image_dimensions').':</td>
            <td>'.TGlobal::OutHTML($this->oTableRow->sqlData['hidden_image_width']).' x  '.TGlobal::OutHTML($this->oTableRow->sqlData['hidden_image_height']).' '.TGlobal::Translate('chameleon_system_core.text.pixel')."</td>
          </tr>\n";
        }

        $html .= '<tr>
          <td>'.TGlobal::Translate('chameleon_system_core.text.file_size').':</td>
          <td>'.TGlobal::OutHTML($fileSize).'</td>
        </tr>
        <tr>
          <td>
          '.TCMSRender::DrawButton(TGlobal::Translate('chameleon_system_core.field_document.open_document'), $sShowDownloadURL, TGlobal::GetStaticURLToWebLib('/images/icons/drive_disk.png'), 'float-left', null, null, null, '_blank').'
          </td>
          <td >
          '.TCMSRender::DrawButton(TGlobal::Translate('chameleon_system_core.field_document.download'), $sDownloadURL, TGlobal::GetStaticURLToWebLib('/images/icons/drive_disk.png'), 'float-left').'
          </td>
        </tr>';

        $sDisplayType = $this->GetDisplayType();
        if ('readonly' != $sDisplayType && 'hidden' != $sDisplayType) {
            $html .= '<tr>
              <td colspan="2">
              '.TCMSRender::DrawButton(TGlobal::Translate('chameleon_system_core.field_document.replace'), 'javascript:OpenDocumentUploadWindow();', TGlobal::GetStaticURLToWebLib('/images/icons/action_refresh_blue.gif')).'
              </td>
            </tr>
          </table>';
        }

        return $html;
    }

    /**
     * renders the read only view of the field.
     *
     * @return string
     */
    public function GetReadOnly()
    {
        $html = $this->GetHTML();

        return $html;
    }

    /**
     * return an array of all js, css, or other header includes that are required
     * in the cms for this field. each include should be in one line, and they
     * should always be typed the same way so that no includes are included mor than once.
     *
     * @return array
     */
    public function GetCMSHtmlHeadIncludes()
    {
        $aIncludes = parent::GetCMSHtmlHeadIncludes();

        $url = PATH_CMS_CONTROLLER.'?pagedef=CMSUniversalUploader&mode=document&callback=_ReloadPage&singleMode=1&recordID='.$this->oTableRow->sqlData['id'];
        $html = "<script type=\"text/javascript\">
      function OpenDocumentUploadWindow() {
        CreateModalIFrameDialogCloseButton('".TGlobal::OutHTML($url)."');
      }

      function _ReloadPage(serverData) {
        if(serverData != false) {
          document.location.href=document.location.href;
        }
      }
      </script>";
        $aIncludes[] = $html;

        return $aIncludes;
    }
}
