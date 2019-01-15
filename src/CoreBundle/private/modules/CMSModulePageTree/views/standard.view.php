<?php
    /**
     * @var \Symfony\Component\Translation\TranslatorInterface
     */
    $translator = \ChameleonSystem\CoreBundle\ServiceLocator::get('translator');
?>

<script type="text/javascript">
    var currentNodeID = null;
    /**
     * opens the page in edit mode
     */
    function openPageEditor(node) {
        var pageID = $(node).attr('espageid');
        if (pageID != false && typeof(pageID) != "undefined") {
            var url = '<?=PATH_CMS_CONTROLLER; ?>?<?=TTools::GetArrayAsURLForJavascript(array('tableid' => $data['tplPageTableID'], 'pagedef' => 'templateengine')); ?>&id=' + pageID;
            parent.document.location.href = url;
        } else {
            alert('<?=TGlobal::OutJS($translator->trans('chameleon_system_core.cms_module_page_tree.node_has_no_page')); ?>');
        }
    }

    /**
     * opens the tree node editor
     */
    function openTreeNodeEditor(node) {
        var nodeID = $(node).attr('esrealid');
        currentNodeID = nodeID; // save node ID in global var
        var url = '<?=PATH_CMS_CONTROLLER; ?>?<?=TTools::GetArrayAsURLForJavascript(array('tableid' => $data['treeTableID'], 'pagedef' => 'tableeditorPopup')); ?>&id=' + nodeID;
        CreateModalIFrameDialogCloseButton(url);
    }

    /**
     * opens the tree node editor and adds a new node
     *
     */
    function openTreeNodeEditorAddNewNode(node) {
        var nodeID = $(node).attr('esrealid');
        currentNodeID = nodeID; // save node ID in global var
        var url = '<?=PATH_CMS_CONTROLLER; ?>?<?=TTools::GetArrayAsURLForJavascript(array('tableid' => $data['treeTableID'], 'pagedef' => 'tableeditorPopup', 'module_fnc' => array('contentmodule' => 'Insert'))); ?>&parent_id=' + nodeID;
        CreateModalIFrameDialogCloseButton(url);
    }

    /**
     * moves node by drag&drop data
     */
    function moveNode(nodeID, parentNodeID, position) {
        if (typeof parentNodeID != 'undefined' && typeof nodeID != 'undefined') {
            CHAMELEON.CORE.showProcessingModal();
            var url = '<?=PATH_CMS_CONTROLLER; ?>?<?=TTools::GetArrayAsURLForJavascript(array('pagedef' => 'CMSModulePageTreePlain', 'module_fnc' => array('module' => 'ExecuteAjaxCall'), '_fnc' => 'MoveNode', 'tableid' => $data['treeTableID'])); ?>&nodeID=' + nodeID + '&parentNodeID=' + parentNodeID + '&position=' + position;
            GetAjaxCallTransparent(url, moveNodeSuccess);
        }
    }

    /**
     * unblocks the UI
     */
    function moveNodeSuccess(nodeID, responseMessage) {
        window.parent.CHAMELEON.CORE.hideProcessingModal();
    }

    /**
     * opens the page in config edit mode
     */
    function openPageConfigEditor(node) {
        var pageID = $(node).attr('espageid');
        if (pageID != false && typeof(pageID) != "undefined") {

            var url = '<?=PATH_CMS_CONTROLLER; ?>?<?=TTools::GetArrayAsURLForJavascript(array('tableid' => $data['tplPageTableID'], 'pagedef' => 'tableeditor')); ?>&id=' + pageID;
            parent.document.location.href = url;
        } else {
            alert('<?=TGlobal::OutJS($translator->trans('chameleon_system_core.cms_module_page_tree.node_has_no_page')); ?>');
        }
    }

    function openPageConnectionList(node) {
        var nodeID = $(node).attr('esrealid');
        var url = '<?=PATH_CMS_CONTROLLER; ?>?<?=TTools::GetArrayAsURLForJavascript(array('id' => $data['treeNodeTableID'], 'pagedef' => 'tablemanagerframe', 'sRestrictionField' => 'cms_tree_id')); ?>&sRestriction=' + nodeID;
        CreateModalIFrameDialogCloseButton(url);
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

    /**
     * deletes a node and kills the page connections
     */
    function deleteNode(node) {
		var confirmMessage = '<?=$translator->trans('chameleon_system_core.cms_module_page_tree.confirm_delete'); ?>';

		var nodeTitle = $(node).find('span.active').text();

		confirmMessage = confirmMessage.replace('%nodeName%', nodeTitle);

		if(confirm(confirmMessage)){
			var nodeID = $(node).attr('esrealid');
			var url = '<?=PATH_CMS_CONTROLLER; ?>?<?=TTools::GetArrayAsURLForJavascript(array('pagedef' => 'CMSModulePageTreePlain', 'module_fnc' => array('module' => 'ExecuteAjaxCall'), '_fnc' => 'DeleteNode', 'tableid' => $data['treeTableID'], 'tbl' => 'cms_tpl_page')); ?>&nodeID=' + nodeID;
            CHAMELEON.CORE.showProcessingModal();

			GetAjaxCallTransparent(url, deleteNodeSuccess);
		}
    }

    /**
     * final remove of the node from DOM tree
     */
    function deleteNodeSuccess(nodeID, responseMessage) {
        $('#node' + nodeID).click();
        $('#node' + nodeID).focus();
        simpleTreeCollection.get(0).delNode();

		/**
		 * see "deleteNode" method for explanation why "window.parent" is used here
		 */
		window.parent.CHAMELEON.CORE.hideProcessingModal();

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

        BindContextMenu();
        CloseModalIFrameDialog();
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
<div id="rightClickMenuContainer" style="display: none;">
    <ul>
        <?php
        if (true == $data['showAssignDialog']) {
            ?>
            <li class="firstnode" id="assignpage"><a href="javascript:void(0);"><img
                src="<?=TGlobal::GetStaticURLToWebLib('/images/icons/page_tick.gif'); ?>"
                border="0"><?php echo $translator->trans('chameleon_system_core.cms_module_page_tree.action_connect_page'); ?></a></li>
            <?php
        }
        ?>
        <li id="editpageconnections"><a href="javascript:void(0);"><img
            src="<?=TGlobal::GetStaticURLToWebLib('/images/icons/page_edit.gif'); ?>"
            border="0"><?php echo $translator->trans('chameleon_system_core.cms_module_page_tree.connected_pages'); ?></a></li>
        <li id="editpage"><a href="javascript:void(0);"><img
            src="<?=TGlobal::GetStaticURLToWebLib('/images/icons/page_edit.gif'); ?>"
            border="0"><?php echo $translator->trans('chameleon_system_core.cms_module_page_tree.action_edit_page'); ?></a></li>
        <li id="editpageconfig"><a href="javascript:void(0);"><img
            src="<?=TGlobal::GetStaticURLToWebLib('/images/icons/application_edit.png'); ?>"
            border="0"><?php echo $translator->trans('chameleon_system_core.list.page_settings'); ?></a></li>
        <li id="editnode"><a href="javascript:void(0);"><img
            src="<?=TGlobal::GetStaticURLToWebLib('/images/icons/page_edit.gif'); ?>"
            border="0"><?php echo $translator->trans('chameleon_system_core.cms_module_page_tree.action_edit_node'); ?></a></li>
        <li id="newnode"><a href="javascript:void(0);"><img
            src="<?=TGlobal::GetStaticURLToWebLib('/images/icons/page_new.gif'); ?>"
            border="0"><?php echo $translator->trans('chameleon_system_core.action.new'); ?></a></li>
        <li id="deletenode"><a href="javascript:void(0);"><img
            src="<?=TGlobal::GetStaticURLToWebLib('/images/icons/page_delete.gif'); ?>"
            border="0"><?php echo $translator->trans('chameleon_system_core.action.delete'); ?></a></li>
    </ul>
    <div class="cleardiv">&nbsp;</div>
</div>
<div id="RootNodeRightClickMenuContainer" style="display: none;">
    <ul>
        <li id="newnode"><a href="javascript:void(0);"><img
            src="<?=TGlobal::GetStaticURLToWebLib('/images/icons/page_new.gif'); ?>"
            border="0"><?php echo $translator->trans('chameleon_system_core.action.new'); ?></a></li>
    </ul>
    <div class="cleardiv">&nbsp;</div>
</div>
<div id="RestrictedNodeRightClickMenuContainer" style="display: none;">
    <ul>
        <li id="editnode"><a href="javascript:void(0);"><img
            src="<?=TGlobal::GetStaticURLToWebLib('/images/icons/page_edit.gif'); ?>"
            border="0"><?php echo $translator->trans('chameleon_system_core.cms_module_page_tree.action_edit_node'); ?></a></li>
        <li id="newnode"><a href="javascript:void(0);"><img
            src="<?=TGlobal::GetStaticURLToWebLib('/images/icons/page_new.gif'); ?>"
            border="0"><?php echo $translator->trans('chameleon_system_core.action.new'); ?></a></li>
    </ul>
    <div class="cleardiv">&nbsp;</div>
</div>

<script type="text/javascript">
    function BindContextMenu() {
        $('.simpleTree span.standard').contextMenu('rightClickMenuContainer', {

            onContextMenu:function (e) {
                var sNodeClasses = $(e.target).attr('class');
                if (sNodeClasses.search('rightClickMenuDisabled') != -1) {
                    return false;
                } else return true;
            },

            bindings:{
                'editpageconnections':function (t) {
                    openPageConnectionList($(t).parents('li'));
                },

                'editpage':function (t) {
                    openPageEditor($(t).parents('li'));
                },

                'editpageconfig':function (t) {
                    openPageConfigEditor($(t).parents('li'));
                },

                'newnode':function (t) {
                    openTreeNodeEditorAddNewNode($(t).parents('li'));
                },

                'editnode':function (t) {
                    openTreeNodeEditor($(t).parents('li'));
                },

                'deletenode':function (t) {
                    deleteNode($(t).parents('li'));
                },

                'assignpage':function (t) {
                    assignPage($(t).parents('li'));
                }
            }
        });
    }

    function BindContextMenuToRootNode() {
        $('.simpleTree span.rootRightClickMenu').contextMenu('RootNodeRightClickMenuContainer', {

            bindings:{
                'newnode':function (t) {
                    openTreeNodeEditorAddNewNode($(t).parents('li'));
                }
            }
        });
    }

    function BindRestrictedContextMenuNode() {
        $('.simpleTree span.restrictedRightClickMenu').contextMenu('RestrictedNodeRightClickMenuContainer', {

            onContextMenu:function (e) {
                var sNodeClasses = $(e.target).attr('class');
                if (sNodeClasses.search('rightClickMenuDisabled') != -1) {
                    return false;
                } else return true;
            },

            bindings:{
                'newnode':function (t) {
                    openTreeNodeEditorAddNewNode($(t).parents('li'));
                },

                'editnode':function (t) {
                    openTreeNodeEditor($(t).parents('li'));
                }
            }
        });
    }

    $(document).ready(function () {
        BindContextMenu();
        BindRestrictedContextMenuNode();
        BindContextMenuToRootNode();

    });
</script>
<ul class="simpleTree">
    <?php
        echo $data['sTreeHTML'];
    ?>
</ul>

<div id="treelegend" style="position: absolute; top: 10px; right: 30px;">
    <h3>
        <span class="nodeIndicatorIcon"></span>
        <?=$translator->trans('chameleon_system_core.cms_module_page_tree.legend_header'); ?>
    </h3>

    <div class="text">
        <span class="nodeIndicatorIcon"></span>
        <span><?=$translator->trans('chameleon_system_core.cms_module_page_tree.legend_node_has_no_page'); ?></span>
    </div>

    <div class="otherConnectedNode">
        <span class="nodeIndicatorIcon"></span>
        <span><?=$translator->trans('chameleon_system_core.cms_module_page_tree.legend_has_connected_pages'); ?></span>
    </div>

    <div class="activeConnectedNode">
        <span class="nodeIndicatorIcon"></span>
        <span><?=$translator->trans('chameleon_system_core.cms_module_page_tree.legend_connected_to_selected_page'); ?></span>
    </div>

    <div class="restrictedPage">
        <span class="nodeIndicatorIcon iconRestricted" style="background-image: url('<?php echo TGlobal::OutHTML(TGlobal::GetStaticURLToWebLib('/images/tree/lock.png')); ?>');"></span>
        <span><?=$translator->trans('chameleon_system_core.cms_module_page_tree.legend_connected_to_protected_page'); ?></span>
    </div>

    <div class="legendLine">
        <span class="nodeIndicatorIcon iconHidden" style="background-image: url('<?php echo TGlobal::OutHTML(TGlobal::GetStaticURLToWebLib('/images/tree/hidden.png')); ?>');"></span>
        <span><?=$translator->trans('chameleon_system_core.cms_module_page_tree.legend_hidden'); ?></span>
    </div>

    <div class="legendLine">
        <span class="nodeIndicatorIcon iconExternalLink" style="background-image: url('<?php echo TGlobal::OutHTML(TGlobal::GetStaticURLToWebLib('/images/icon_external_link.gif')); ?>');"></span>
        <span><?=$translator->trans('chameleon_system_core.cms_module_page_tree.legend_external_link'); ?></span>
    </div>
</div>
