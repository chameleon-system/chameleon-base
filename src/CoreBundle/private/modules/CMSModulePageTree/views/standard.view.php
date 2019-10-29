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


    // updates the tree node HTML without page connection
    function updateTreeNode(formObject, nodeID) {
        var pageID = '';
        updateTreeNodeWithPage(formObject, nodeID, pageID);
    }

    /**
     * changes the node to show changes (hidden, external link etc.)
     */
    function updateTreeNodeWithPage(formObject, nodeID, realID, pageID) {
        var isHidden = getRadioValue(formObject.elements['hidden']);
        var newNodeTitle = formObject.name.value;

        var hiddenClass = '';
        if (isHidden != false && isHidden != 0 && isHidden != 'false') {
            var hiddenClass = 'hiddenNode';
        }

        var newNodeAfterLabel = '';
        if (formObject.link.value != '') {
            var newNodeAfterLabel = '<a href="' + formObject.link.value + '\" target="_blank"><img src="<?=TGlobal::GetStaticURLToWebLib('/images/icon_external_link.gif'); ?>" style="padding-left: 5px;" border="0" width="15" height="13" style="float: right;" /></a>';
        }

        if (formObject.id.value != currentNodeID) { // parent node is current node, so we have created a new node and need to add it to the tree
            // add new node
            simpleTreeCollection.get(0).addNode('node' + nodeID, newNodeTitle);
            $('ul.simpletree span.active').removeClass('active').addClass('text');
            $('#node' + nodeID + ' span').addClass('standard active');
            $('#node' + nodeID + ' span').removeClass('text');
        } else {
            // update title
            $('#node' + nodeID + ' span.active').html(newNodeTitle);
        }

        // add hidden class
        if (hiddenClass != '') {
            $('#node' + nodeID + ' span.active').addClass(hiddenClass);
        } else {
            $('#node' + nodeID + ' span.active').removeClass('hiddenNode');
        }

        // add external link icon
        if (newNodeAfterLabel != '') {
            if ($('#node' + nodeID + ' span.active').next('a').html() == null) {
                $('#node' + nodeID + ' span.active').append(newNodeAfterLabel);
            }
        } else {
            $('#node' + nodeID + ' span').next('a').remove();
        }

        if (pageID != '' && pageID != false && pageID != 'false') { // add page id attribute
            $('#node' + nodeID).attr({ espageid:pageID });
            $('#node' + nodeID + ' span.active').addClass('otherConnectedNode');
        }

        $('#node' + nodeID).attr({ esrealid:realID });

        // BindContextMenu();
        // CloseModalIFrameDialog();
    }

    <?php
    if ($data['iTreeNodeCount'] < 300) {
        ?>
    $(window).unload(function () {
            var sOpenNodes = '';
            // save current tree state to cookie
            $(".simpleTree li.folder-open").each(function (i) {
                if (sOpenNodes != '') sOpenNodes += ',';
                sOpenNodes += this.getAttribute('esrealid');
            });

            $.cookie('chameleonTreeState', sOpenNodes, { expires:7, domain:'<?=$_SERVER['HTTP_HOST']; ?>' });
        }
    );
        <?php
    } else {
        ?>
    $(window).unload(function () {
        $.cookie('chameleonTreeState', '', { expires:-1 });
    });
        <?php
    }
    ?>
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

echo $viewRenderer->Render('CMSModulePageTree/standard.html.twig');

