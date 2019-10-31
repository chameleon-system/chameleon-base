<?php
    /**
     * @var \Symfony\Component\Translation\TranslatorInterface
     */
    $translator = \ChameleonSystem\CoreBundle\ServiceLocator::get('translator');
?>

<script type="text/javascript">
    var currentNodeID = null;

    /**
     * moves node by drag&drop data
     */
    function moveNode(nodeID, parentNodeID, position) {
        if (typeof parentNodeID != 'undefined' && typeof nodeID != 'undefined') {
            CHAMELEON.CORE.showProcessingModal();
            var url = '<?=PATH_CMS_CONTROLLER; ?>?<?=TTools::GetArrayAsURLForJavascript(array('pagedef' => 'CMSModulePageTree', 'module_fnc' => array('contentmodule' => 'ExecuteAjaxCall'), '_fnc' => 'MoveNode', 'tableid' => $data['treeTableID'])); ?>&nodeID=' + nodeID + '&parentNodeID=' + parentNodeID + '&position=' + position;
            GetAjaxCallTransparent(url, moveNodeSuccess);
        }
    }

    /**
     * unblocks the UI
     */
    function moveNodeSuccess(nodeID, responseMessage) {
        window.parent.CHAMELEON.CORE.hideProcessingModal();
    }


    function assignPage(node) {
        var nodeID = $(node).attr('esrealid');
        var assignedDataID = '<?php if (!empty($data['dataID'])) {
    echo $data['dataID'];
} ?>';
        var url = '<?=PATH_CMS_CONTROLLER; ?>?<?=TTools::GetArrayAsURLForJavascript(array('tableid' => $data['treeNodeTableID'], 'pagedef' => 'tableeditorPopup', 'sRestrictionField' => 'cms_tree_id', 'module_fnc' => array('contentmodule' => 'Insert'), 'active' => '1', 'preventTemplateEngineRedirect' => '1')); ?>&sRestriction=' + nodeID + '&contid=' + assignedDataID + '&cms_tree_id=' + nodeID;
        CreateModalIFrameDialogCloseButton(url);
    }

    /**
     * changes the display of the current node after assign dialog
     */
    function TreeNodeAssignFormResponse(data, responseMessage) {
        if (data) {
            CloseModalIFrameDialog();
            if (data.assigned) {
                $('#' + data.treeNodeID + ' span').addClass('activeConnectedNode');
            } else {
                $('#' + data.treeNodeID + ' span').removeClass('activeConnectedNode');
            }
        }
    }


</script>


<?php

/**
 * @var \ChameleonSystem\CoreBundle\DataModel\BackendTreeNodeDataModel $treeNodes

 * @var string $isInIframe
 * @var string $showAssignDialog
 * @var string $treeNodesAjaxUrl
 * @var string $openPageConnectionListUrl
 * @var string $openPageEditorUrl
 * @var string $openPageConfigEditorUrl
 * @var string $openTreeNodeEditorUrl
 * @var string $openTreeNodeEditorAddNewNodeUrl
 * @var string $deleteNodeUrl
 * @var string $assignPageUrl
 * @var object $dataID
 */
$viewRenderer = new ViewRenderer();
$viewRenderer->AddSourceObject('isInIframe', $isInIframe);
$viewRenderer->AddSourceObject('showAssignDialog', $showAssignDialog);
$viewRenderer->AddSourceObject('treeNodesAjaxUrl', $treeNodesAjaxUrl);
$viewRenderer->AddSourceObject('openPageConnectionListUrl', $openPageConnectionListUrl);
$viewRenderer->AddSourceObject('openPageEditorUrl', $openPageEditorUrl);
$viewRenderer->AddSourceObject('openPageConfigEditorUrl', $openPageConfigEditorUrl);
$viewRenderer->AddSourceObject('openTreeNodeEditorUrl', $openTreeNodeEditorUrl);
$viewRenderer->AddSourceObject('openTreeNodeEditorAddNewNodeUrl', $openTreeNodeEditorAddNewNodeUrl);
$viewRenderer->AddSourceObject('deleteNodeUrl', $deleteNodeUrl);
$viewRenderer->AddSourceObject('assignPageUrl', $assignPageUrl);
$viewRenderer->AddSourceObject('pageId', $dataID);

echo $viewRenderer->Render('CMSModulePageTree/standard.html.twig');

