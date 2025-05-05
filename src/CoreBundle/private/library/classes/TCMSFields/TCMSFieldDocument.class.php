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

class TCMSFieldDocument extends TCMSFieldLookup
{
    public function GetHTML()
    {
        $this->oTableConf = $this->oTableRow->GetTableConf();

        $currentFile = ServiceLocator::get('translator')->trans('chameleon_system_core.field_document.nothing_selected');
        if (!empty($this->data)) {
            $oCmsDocument = TdbCmsDocument::GetNewInstance();
            if ($oCmsDocument->Load($this->data)) {
                $currentFile = $oCmsDocument->getDownloadHtmlTag();
            }
        }

        $viewRenderer = $this->getViewRenderer();
        $viewRenderer->AddSourceObject('fieldName', $this->name);
        $viewRenderer->AddSourceObject('fieldValue', $this->data);
        $viewRenderer->AddSourceObject('currentFile', $currentFile);

        $viewRenderer->AddSourceObject('onClickDocument', $this->_GetOpenWindowJS());
        $onClickReset = "_ResetDocument('".TGlobal::OutHTML($this->name)."', '".ServiceLocator::get('translator')->trans('chameleon_system_core.field_document.nothing_selected')."','".TGlobal::OutHTML($this->oDefinition->sqlData['field_default_value'])."')";
        $viewRenderer->AddSourceObject('onClickReset', $onClickReset);
        $viewRenderer->AddSourceObject('onClickNewFile', $this->_GetOpenUploadWindowJS());

        $oTreeSelect = new TCMSRenderDocumentTreeSelectBox();
        $viewRenderer->AddSourceObject('optionsHTML', $oTreeSelect->GetTreeOptions());

        return $viewRenderer->Render('TCMSFieldDocument/fieldDocument.html.twig', null, false);
    }

    public function _GetOpenWindowJS()
    {
        $url = PATH_CMS_CONTROLLER.'?'.TTools::GetArrayAsURLForJavascript(['pagedef' => 'CMSDocumentSelect', 'documentfieldname' => $this->name, 'tableid' => $this->oTableConf->id, 'id' => $this->recordId]);
        $js = "saveCMSRegistryEntry('_currentFieldName','".TGlobal::OutHTML($this->name)."');CreateModalIFrameDialogCloseButton('".TGlobal::OutHTML($url)."');";

        return $js;
    }

    public function _GetOpenUploadWindowJS()
    {
        $js = "saveCMSRegistryEntry('_currentFieldName','".TGlobal::OutHTML($this->name)."');OpenUploadWindow(document.cmseditform.documentTreeId_".TGlobal::OutHTML($this->name).'.options[document.cmseditform.documentTreeId_'.TGlobal::OutHTML($this->name).'.selectedIndex].value);';

        return $js;
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

        $url = PATH_CMS_CONTROLLER.'?pagedef=CMSUniversalUploader&mode=document&callback=_SetDocument&singleMode=1&treeNodeID=';
        $html = "<script type=\"text/javascript\">
      function OpenUploadWindow(documentTreeID) {
        if(documentTreeID !== '') {
          CreateModalIFrameDialogCloseButton('".TGlobal::OutHTML($url)."' + documentTreeID);
        } else {
          toasterMessage('".ServiceLocator::get('translator')->trans('chameleon_system_core.field_document.error_missing_target').".','ERROR');
        }
      }
      </script>";
        $aIncludes[] = $html;

        return $aIncludes;
    }

    private function getViewRenderer(): ViewRenderer
    {
        return ServiceLocator::get('chameleon_system_view_renderer.view_renderer');
    }
}
