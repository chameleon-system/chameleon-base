<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use ChameleonSystem\AutoclassesBundle\TableConfExport\DoctrineNotTransformableMarkerInterface;

class TCMSFieldDocumentProperties extends TCMSField implements DoctrineNotTransformableMarkerInterface
{
    // Has been overwritten to show additional infos about the document. Is not relevant for doctrine.

    public function GetHTML()
    {
        $oFileType = new TCMSRecord('cms_filetype', $this->oTableRow->sqlData['cms_filetype_id']);
        $fileSize = TCMSDownloadFile::GetHumanReadableFileSize($this->oTableRow->sqlData['filesize']);

        $oDownloadItem = new TCMSDownloadFile();
        /* @var $oDownloadItem TCMSDownloadFile */
        $oDownloadItem->table = 'cms_document';
        $oDownloadItem->Load($this->oTableRow->sqlData['id']);
        $sShowDownloadURL = $oDownloadItem->getBackendDownloadLink(false);
        $sDownloadURL = $oDownloadItem->getBackendDownloadLink(true);

        $html = '<input type="hidden" id="'.TGlobal::OutHTML($this->name).'" name="'.TGlobal::OutHTML($this->name).'" value="'.TGlobal::OutHTML($this->data).'" />';

        $downloadIcon = $oDownloadItem->getDownloadHtmlTag(false, true, true);
        $html .= '<table class="table table-sm table-striped">
        <tr class="entry-id-copy-button" data-entry-id="'.TGlobal::OutHTML($oDownloadItem->id).'" title="'.TGlobal::OutHTML(ChameleonSystem\CoreBundle\ServiceLocator::get('translator')->trans('chameleon_system_core.text.copy_id_to_clipboard')).'">
          <td width="60">ID:</td>
          <td>'.$oDownloadItem->id.' <i class="far fa-clipboard"></i></td>
        </tr>
        <tr>
          <td width="60">'.ChameleonSystem\CoreBundle\ServiceLocator::get('translator')->trans('chameleon_system_core.text.file_type').':</td>
          <td><span class="float-left">'.$downloadIcon.'</span><div class="float-left">'.TGlobal::OutHTML($oFileType->GetName()).' (.'.TGlobal::OutHTML($oFileType->sqlData['file_extension']).")</div></td>
        </tr>\n";

        if (!empty($this->oTableRow->sqlData['hidden_image_width']) && !empty($this->oTableRow->sqlData['hidden_image_height'])) {
            $html .= '<tr>
            <td>'.ChameleonSystem\CoreBundle\ServiceLocator::get('translator')->trans('chameleon_system_core.text.image_dimensions').':</td>
            <td>'.TGlobal::OutHTML($this->oTableRow->sqlData['hidden_image_width']).' x  '.TGlobal::OutHTML($this->oTableRow->sqlData['hidden_image_height']).' '.ChameleonSystem\CoreBundle\ServiceLocator::get('translator')->trans('chameleon_system_core.text.pixel')."</td>
          </tr>\n";
        }

        $html .= '<tr>
          <td>'.ChameleonSystem\CoreBundle\ServiceLocator::get('translator')->trans('chameleon_system_core.text.file_size').':</td>
          <td>'.TGlobal::OutHTML($fileSize).'</td>
        </tr>
        <tr>
          <td>
          '.TCMSRender::DrawButton(ChameleonSystem\CoreBundle\ServiceLocator::get('translator')->trans('chameleon_system_core.field_document.open_document'), $sShowDownloadURL, 'far fa-file', 'float-left', null, null, null, '_blank').'
          </td>
          <td>
          '.TCMSRender::DrawButton(ChameleonSystem\CoreBundle\ServiceLocator::get('translator')->trans('chameleon_system_core.field_document.download'), $sDownloadURL, 'fas fa-download', 'float-left').'
          </td>
        </tr>';

        $sDisplayType = $this->GetDisplayType();
        if ('readonly' != $sDisplayType && 'hidden' != $sDisplayType) {
            $html .= '<tr>
              <td>
              '.TCMSRender::DrawButton(ChameleonSystem\CoreBundle\ServiceLocator::get('translator')->trans('chameleon_system_core.field_document.replace'), 'javascript:OpenDocumentUploadWindow();', 'fas fa-file').'
              </td>
              <td>
              '.$this->drawLinkCopyButton($oDownloadItem).'
              </td>
            </tr>
          </table>';
        }

        return $html;
    }

    protected function drawLinkCopyButton(TCMSDownloadFile $downloadItem): string
    {
        if ('1' === $this->oTableRow->sqlData['private']) {
            return '';
        }

        $link = $downloadItem->GetPlainDownloadLink();

        $showCopyLinkButton = TCMSRender::DrawButton(ChameleonSystem\CoreBundle\ServiceLocator::get('translator')->trans('chameleon_system_core.field_document.show_copy_link'), 'javascript:toggleCopyLinkDialog(true)', 'fas fa-link', 'float-left');
        $copyLinkButton = TCMSRender::DrawButton(ChameleonSystem\CoreBundle\ServiceLocator::get('translator')->trans('chameleon_system_core.field_document.copy_link'), 'javascript:copyLinkTextToClipboard(); toggleCopyLinkDialog(false);', 'fas fa-copy', 'float-left');
        $closeDialogButton = TCMSRender::DrawButton(ChameleonSystem\CoreBundle\ServiceLocator::get('translator')->trans('chameleon_system_core.action.close'), 'javascript:toggleCopyLinkDialog(false)', 'fas fa-times', 'float-left');

        $html = $showCopyLinkButton;
        $html .= '
            <div class="modal fade" id="link-copy-dialog" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-sm">
                    <div class="modal-content">
                        <div class="modal-body m-1">
                            <a href="'.TGlobal::OutHTML($link).'" target="_blank">'.TGlobal::OutHTML($link).'</a>
                        </div>
                        <div class="modal-footer">
                            '.$closeDialogButton.'
                            '.$copyLinkButton.'
                        </div>
                    </div>
                </div>
            </div>';

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

      let copyLinkDialogModal = null;
      
      function toggleCopyLinkDialog(show) {
        if (copyLinkDialogModal === null) {
            const linkCopyDialog = document.getElementById('link-copy-dialog');
            if (linkCopyDialog === null) {
              return;
            }
            
            copyLinkDialogModal = new bootstrap.Modal(linkCopyDialog);
        }
        
        if (show) {
            copyLinkDialogModal.show();
        } else {
            copyLinkDialogModal.hide();       
        }
      }
      
      async function copyLinkTextToClipboard() {
        const link = document.getElementById('link-copy-dialog')?.querySelector('a');
        if (link !== null) {
          const text = link.getAttribute('href');
          return await navigator.clipboard.writeText(text);
        }
      }
      
      function _ReloadPage(serverData) {
        if (serverData != false) {
          document.location.href=document.location.href;
        }
      }
      </script>";
        $aIncludes[] = $html;

        return $aIncludes;
    }
}
