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

class TCMSFieldMediaProperties extends TCMSFieldNumber
{
    /** @var bool */
    protected $bIsReadOnlyMode = false;

    public function GetHTML()
    {
        $sFileTypeIconPath = '';
        if (!empty($this->oTableRow->sqlData['cms_filetype_id'])) {
            $oImageType = TdbCmsFiletype::GetNewInstance();
            $oImageType->Load($this->oTableRow->sqlData['cms_filetype_id']);
            $sFileTypeIconPath = TGlobal::GetStaticURLToWebLib(URL_FILETYPE_ICONS_LOW_QUALITY.$oImageType->sqlData['file_extension'].'.png');
        }
        $fileSize = TCMSDownloadFile::GetHumanReadableFileSize($this->oTableRow->sqlData['filesize']);

        $oImage = new TCMSImage();
        $oImage->Load($this->oTableRow->sqlData['id']);
        /* @var $oImage TCMSImage */

        $html = '<input type="hidden" id="'.TGlobal::OutHTML($this->name).'" name="'.TGlobal::OutHTML($this->name).'" value="'.TGlobal::OutHTML($this->data).'" />';
        $html .= '
      <div style="float: right;">';

        $html .= $oImage->GetThumbnailTag(200, 140, 400, 400);

        $html .= '</div>
      <table border="0" style="float: left; width: 50%" class="table table-sm table-striped">
        <tr>
          <td width="60">ID:</td>
          <td>'.TGlobal::OutHTML($this->oTableRow->sqlData['id']).'</td>
        </tr>';

        if (!empty($sFileTypeIconPath)) {
            $html .= '<tr>
          <td width="60">'.TGlobal::Translate('chameleon_system_core.text.file_type').':</td>
                <td><img src ="'.$sFileTypeIconPath.'" width="16" height="16" style="float: left; margin-right: 10px;" /><div style="float: left;">'.TGlobal::OutHTML($oImageType->GetName()).'</div></td>
        </tr>';
        }

        $html .= '<tr>
          <td>'.TGlobal::Translate('chameleon_system_core.text.image_dimensions').':</td>
          <td>'.TGlobal::OutHTML($this->oTableRow->sqlData['width']).' x  '.TGlobal::OutHTML($this->oTableRow->sqlData['height']).' '.TGlobal::Translate('chameleon_system_core.text.pixel').'</td>
        </tr>
        <tr>
          <td>'.TGlobal::Translate('chameleon_system_core.text.file_size').':</td>
          <td>'.TGlobal::OutHTML($fileSize).'</td>
        </tr>';

        $html .= '<tr>
            <td colspan="2">
            '.TCMSRender::DrawButton(TGlobal::Translate('chameleon_system_core.field_document.download'), $oImage->GetFullURL(), TGlobal::GetStaticURLToWebLib('/images/icons/drive_disk.png'), 'float-left').'
            <div style="padding-left: 10px; float: left;">('.TGlobal::Translate('chameleon_system_core.field_document.right_click_download').')</div>
            </td>
          </tr>';

        if (!$this->bIsReadOnlyMode && empty($this->oTableRow->sqlData['external_video_id'])) {
            $html .= '<tr>
            <td colspan="2">
            '.TCMSRender::DrawButton(TGlobal::Translate('chameleon_system_core.field_document.replace'), 'javascript:OpenMediaUploadWindow();', TGlobal::GetStaticURLToWebLib('/images/icons/action_refresh_blue.gif')).'
            </td>
          </tr>';
        }

        $html .= '</table>';

        return $html;
    }

    /**
     * {@inheritdoc}
     */
    public function GetDisplayType()
    {
        if (false === $this->renderMediaPropertiesField()) {
            return 'hidden';
        }

        return parent::GetDisplayType();
    }

    /**
     * @return bool
     */
    private function renderMediaPropertiesField()
    {
        return ServiceLocator::getParameter('chameleon_system_core.render_media_properties_field');
    }

    /**
     * renders the read only view of the field.
     *
     * @return string
     */
    public function GetReadOnly()
    {
        $this->bIsReadOnlyMode = true;
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

        $url = PATH_CMS_CONTROLLER.'?pagedef=CMSUniversalUploader&mode=media&callback=_ReloadPage&singleMode=1&showMetaFields=0&recordID='.$this->oTableRow->sqlData['id'];
        $html = "<script type=\"text/javascript\">
      function OpenMediaUploadWindow() {
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
