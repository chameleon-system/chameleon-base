<?php

namespace ChameleonSystem\MarkdownCmsBundle\Bridge\Chameleon\ListManager;

use ChameleonSystem\CmsStringUtilitiesBundle\Interfaces\UrlUtilityServiceInterface;
use ChameleonSystem\CoreBundle\ServiceLocator;
use ChameleonSystem\CoreBundle\Util\InputFilterUtilInterface;

class ListManagerWithMarkdownEditorCallback extends \TCMSListManagerFullGroupTable
{

    public function _GetRecordClickJavaScriptFunctionName(): string
    {
        return 'selectRecordForMarkdown';
    }

    /**
     * @inheritDoc
     */
    public function GetHtmlHeadIncludes(): array
    {
        $editorId = $this->getInputFilterUtil()->getFilteredGetInput('editorId','');
        $pagedef = $this->getInputFilterUtil()->getFilteredGetInput('pagedef','');
        $pagedefType = $this->getInputFilterUtil()->getFilteredGetInput('_pagedefType','');
        $tableId = $this->getInputFilterUtil()->getFilteredGetInput('id','');

        $url = PATH_CMS_CONTROLLER.'?';
        $url = $this->getUrlUtilService()->addParameterToUrl($url,
            [
                'pagedef' => $pagedef,
                '_pagedefType' => $pagedefType,
                'editorId' => $editorId,
                'id' => $tableId,
                'module_fnc' => ['contentmodule' => 'ExecuteAjaxCall'],
                '_fnc' => 'getRecordName',
                'callListManagerMethod' => '1',
            ]
        );

        $aIncludes = parent::GetHtmlHeadIncludes();

        $aIncludes[] = '
        <script type="text/javascript">
        function addLinkToEditorInstance(data)
        {
            let dataObject = JSON.parse(data);
            let editorId = dataObject.editorId;
            let editorInstance = window.parent.TUIEditorManager.editors[editorId];
            editorInstance.insertText("["+dataObject.name+"]("+dataObject.tableName+"|"+dataObject.id+")");
            CloseModalIFrameDialog();
            window.parent.CloseModalIFrameDialog();
        }
        
        function selectRecordForMarkdown(id) {
            let editorId = "'. $editorId .'";
            let tableId = "'. $tableId .'";
            let url = "'. $url .'";
            let getNameAjaxUrl = url+"&recordId="+id;            
            GetAjaxCall(getNameAjaxUrl, addLinkToEditorInstance);
        }

        </script>
      ';

        return $aIncludes;
    }

    protected function DefineInterface()
    {
        parent::DefineInterface();
        $this->methodCallAllowed[] = 'getRecordName';
    }

    public function getRecordName(): string
    {
        $recordId = $this->getInputFilterUtil()->getFilteredGetInput('recordId');
        $tableId = $this->getInputFilterUtil()->getFilteredGetInput('id');
        $editorId = $this->getInputFilterUtil()->getFilteredGetInput('editorId');

        $tableConf = new \TCMSTableConf($tableId);
        $tdbObject = $tableConf->GetTableObjectInstance($recordId);

        $data = [
            'name' => $this->getRecordNameForLink($tdbObject),
            'tableName' => $tableConf->sqlData['name'],
            'id' => $recordId,
            'editorId' => $editorId,
        ];

        return \json_encode($data);
    }

    /**
     * Handles special cases of target tables where GetName can't be used to get the displayed name 
     * for the frontend like documents table.
     */
    protected function getRecordNameForLink(\TCMSRecord $tdbObject): string
    {
        if (\is_a($tdbObject, 'TdbCmsDocument')) {
            return $tdbObject->GetFileNameWithExtension();
        }
        
        return $tdbObject->GetName();
    }

    private function getInputFilterUtil(): InputFilterUtilInterface
    {
        return ServiceLocator::get('chameleon_system_core.util.input_filter');
    }

    private function getUrlUtilService(): UrlUtilityServiceInterface
    {
        return ServiceLocator::get('chameleon_system_cms_string_utilities.url_utility_service');
    }
}
