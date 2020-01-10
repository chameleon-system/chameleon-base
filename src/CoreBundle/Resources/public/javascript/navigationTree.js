
$(document).ready(function () {

    $("#singleTreeNodeSelect")
        .jstree({
            "core":{
                "multiple": false,
                "open_all": true
            },
            "types": {
                "default": {
                    "icon": ""
                },
                "folder": {
                    "icon": "fas fa-folder-open",
                    "check_node": false
                },
                "page": {
                    "icon": "far fa-file"
                }
            },
            "checkbox": {
                "three_state": false,
                "cascade": "none"
            },
            "plugins":[ "types", "wholerow", "changed", "checkbox" ]
        });


    $('.jstree-selection').click(function () {
        var selectedItem = $("#singleTreeNodeSelect").jstree('get_selected');

        if (selectedItem.length > 0) {
            var singleSelection = $('#'+selectedItem[0]);
            var fieldName = singleSelection.data('selection').fieldName;
            var newId = singleSelection.data('selection').nodeId;
            chooseTreeNode(fieldName, newId);
        }
    });

    $('.jstree-exit').click(function () {
        parent.CloseModalIFrameDialog();
    });

    var singleTreeNodeSelectWysiwyg = $("#singleTreeNodeSelectWysiwyg");
    singleTreeNodeSelectWysiwyg.jstree({
        "core":{
            "multiple": false
        },
        "types": {
            "default": {
                "icon": ""
            },
            "folder": {
                "icon": "fas fa-folder-open",
                "check_node": false
            },
            "page": {
                "icon": "far fa-file"
            }
        },
        "checkbox": {
            "three_state": false,
            "cascade": "none"
        },
        "plugins":[ "types", "wholerow", "changed", "checkbox" ]
    }).on('ready.jstree', function() {
        $(this).jstree("open_all");
    });

    $('.jstree-selection-wysiwyg').click(function () {
        var CKEditorFuncNum = singleTreeNodeSelectWysiwyg.data('ckeditorfuncnum');
        var selectedItem = singleTreeNodeSelectWysiwyg.jstree('get_selected');
        var selectedItemText = singleTreeNodeSelectWysiwyg.jstree('get_selected').text;

        if (selectedItem.length > 0) {
            var singleSelection = $('#' + selectedItem[0]);
            var connectedPageId = singleSelection.data('selection').connectedPageId;
            chooseTreeNodeWysiwyg(CKEditorFuncNum, connectedPageId, selectedItemText);
        }
        window.close();
    });

    $('.jstree-exit-wysiwyg').click(function () {
        window.close();
    });

    var currentNodeId = null;
    var navTreeDataContainer = $("#navigationTreeDataContainer");

    $("#navigationTreeContainer")
        .jstree({
            "core":{
                "multiple": true,
                "data": {
                    "url":  $(navTreeDataContainer).data('tree-nodes-ajax-url'),
                    "data": function(node) {
                        return {
                            'id' : node.id
                        };
                    }
                },
                "check_callback" : true
            },
            "types": {
                "default": {
                    "icon": "fas fa-folder-open"
                },
                "folder": {
                    "icon": "fas fa-folder-open"
                },
                "folderRestrictedMenu": {
                    "icon": "fas fa-folder-open"
                },
                "folderRootRestrictedMenu": {
                    "icon": "fas fa-folder-open"
                },
                "folderWithPage": {
                    "icon": "fas fa-folder-open",
                    "check_node": false
                },
                "page": {
                    "icon": "far fa-file"
                },
                "locked": {
                    "icon": "fas fa-lock"
                },
                "pageHidden": {
                    "icon": "far fa-eye-slash"
                },
                "nodeHidden": {
                    "icon": "far fa-eye-slash"
                },
                "externalLink": {
                    "icon": "fas fa-external-link-alt"
                }
            },
            "plugins":[ "state", "types", "wholerow", "changed", "contextmenu", "dnd"],
            "contextmenu": {
                "items": navigationRightClickMenu
            }
        })
        .on("move_node.jstree", function (e, data) {
            moveNode(data.node.id, data.parent, data.position);
        });

    $("#navigationTreeContainer-checkboxes")
        .jstree({
            "core":{
                "multiple": true,
                "data": {
                    "url": $(navTreeDataContainer).data('tree-nodes-ajax-url'),
                    "data": function(node) {
                        return {
                            'id' : node.id
                        };
                    }
                },
                "check_callback" : true
            },
            "types": {
                "default": {
                    "icon": "fas fa-folder-open"
                },
                "folder": {
                    "icon": "fas fa-folder-open",
                    "check_node": false
                },
                "folderRestrictedMenu": {
                    "icon": "fas fa-folder-open",
                    "check_node": false
                },
                "folderRootRestrictedMenu": {
                    "icon": "fas fa-folder-open",
                    "check_node": false
                },
                "folderWithPage": {
                    "icon": "fas fa-folder-open",
                    "check_node": false
                },
                "page": {
                    "icon": "far fa-file"
                },
                "locked": {
                    "icon": "fas fa-lock"
                },
                "pageHidden": {
                    "icon": "far fa-eye-slash"
                },
                "nodeHidden": {
                    "icon": "far fa-eye-slash"
                },
                "externalLink": {
                    "icon": "fas fa-external-link-alt"
                }
            },
            "checkbox": {
                "three_state": false,
                "cascade": "none"
            },
            "plugins":[ "types", "wholerow", "changed", "checkbox", "contextmenu", "dnd"],
            "contextmenu": {
                "items": navigationRightClickMenuForCheckboxes,
                "select_node": false
            },
            "dnd": {
                "drag_selection": false
            }
        })
        .on("move_node.jstree", function (e, data) {
            moveNode(data.node.id, data.parent, data.position);
        })
        .on("select_node.jstree", function (e, data) {
            connectPageOnSelect(data.node.id);
        })
        .on("deselect_node.jstree", function (e, data) {
            disconnectPageOnDeselect(data.node.id);
        });

});

/*
 * TreeNode field: sets selected node
 */
function chooseTreeNode(fieldName, newId) {
    parent.$('#' + fieldName).val(newId);
    var newPath = $('#' + fieldName + '_tmp_path_' + newId).html();
    parent.$('#' + fieldName + '_path').html(newPath);
    parent.CloseModalIFrameDialog();
}

function chooseTreeNodeWysiwyg(CKEditorFuncNum, pagedef, text) {
    var url = '/INDEX?pagedef=' + pagedef;

    parent.opener.window.CKEDITOR.tools.callFunction(
        CKEditorFuncNum, encodeURI(url), function () {
            // Get the reference to a dialog window.
            var element,
                dialog = this.getDialog(),
                editor = dialog.getParentEditor();
            // Get the reference to a text field that holds the "alt" attribute.
            element = dialog.getContentElement('info', 'linkDisplayText');
            if (element && (element.getValue() == '' || editor.plugins.chameleon_link.allowSuggestions)) {
                element.setValue(text);
            }
            element = dialog.getContentElement('advanced', 'advTitle');
            if (element && (element.getValue() == '' || editor.plugins.chameleon_link.allowSuggestions)) {
                element.setValue(text);
            }

            window.close();
        }
    );
}


function navigationRightClickMenu(node) {
    var items = {
        "editpageconnections": {
            "label": CHAMELEON.CORE.i18n.Translate('chameleon_system_core.cms_module_page_tree.connected_pages'),
            "icon": "fas fa-link",
            "action": function (obj) {
                this.openPageConnectionList(obj.reference);
            }
        },
        "editpage": {
            "label": CHAMELEON.CORE.i18n.Translate('chameleon_system_core.cms_module_page_tree.edit_page'),
            "icon": "far fa-edit",
            "action": function (obj) {
                this.openPageEditor(obj.reference);
            }
        },
        "editpageconfig": {
            "label": CHAMELEON.CORE.i18n.Translate('chameleon_system_core.cms_module_page_tree.page_settings'),
            "icon": "fas fa-cog",
            "action": function (obj) {
                this.openPageConfigEditor(obj.reference);
            }
        },
        "editnode": {
            "label": CHAMELEON.CORE.i18n.Translate('chameleon_system_core.cms_module_page_tree.edit_node'),
            "icon": "fas fa-sitemap",
            "action": function (obj) {
                this.openTreeNodeEditor(obj.reference);
            }
        },
        "newnode": {
            "label": CHAMELEON.CORE.i18n.Translate('chameleon_system_core.cms_module_page_tree.new'),
            "icon": "fas fa-plus",
            "action": function (obj) {
                this.openTreeNodeEditorAddNewNode(obj.reference);
            }
        },
        "deletenode": {
            "label": CHAMELEON.CORE.i18n.Translate('chameleon_system_core.cms_module_page_tree.delete'),
            "icon": "far fa-trash-alt",
            "action": function (obj) {
                this.deleteNode(obj.reference);
            }
        }
    };

    if (node.type === "folderRestrictedMenu")  {
        delete items.editpageconnections;
        delete items.editpage;
        delete items.editpageconfig;
        delete items.deletenode;
    }

    if (node.type === "folderRootRestrictedMenu") {
        items = {};
    }

    return items;
}

function navigationRightClickMenuForCheckboxes(node) {
    return {
        "newnode": {
            "label": CHAMELEON.CORE.i18n.Translate('chameleon_system_core.cms_module_page_tree.new'),
            "icon": "fas fa-plus",
            "action": function (obj) {
                this.openTreeNodeEditorAddNewNode(obj.reference);
            }
        },
        "deletenode": {
            "label": CHAMELEON.CORE.i18n.Translate('chameleon_system_core.cms_module_page_tree.delete'),
            "icon": "far fa-trash-alt",
            "action": function (obj) {
                this.deleteNode(obj.reference);
            }
        }
    };
}


function openPageConnectionList(node) {
    var nodeId = $(node).parent().attr('id');
    currentNodeId = nodeId; // save nodeId in global var
    var url = $(navTreeDataContainer).data('open-page-connection-list-url') + "&sRestriction=" + nodeId;
    CreateModalIFrameDialogCloseButton(url);
    refreshNodeOnModalClose(nodeId);
}

/**
 * opens the page in edit mode
 */
function openPageEditor(node) {
    var pageId = $(node).parent().attr('isPageId');

    if (pageId !== false && typeof(pageId) !== "undefined") {
        parent.document.location.href = $(navTreeDataContainer).data('open-page-editor-url') + '&id=' + pageId;
    } else {
        alert(CHAMELEON.CORE.i18n.Translate('chameleon_system_core.cms_module_page_tree.node_has_no_page'));
    }
}

/**
 * opens the page in config edit mode
 */
function openPageConfigEditor(node) {
    var pageId = $(node).parent().attr('isPageId');
    if (pageId !== false && typeof(pageId) !== "undefined") {
        parent.document.location.href = $(navTreeDataContainer).data('open-page-config-editor-url') + '&id=' + pageId;
    } else {
        alert(CHAMELEON.CORE.i18n.Translate('chameleon_system_core.cms_module_page_tree.node_has_no_page'));
    }
}

/**
 * opens the tree node editor
 */
function openTreeNodeEditor(node) {
    var nodeId = $(node).parent().attr('id');
    currentNodeId = nodeId; // save nodeId in global var
    var url = $(navTreeDataContainer).data('open-tree-node-editor-url') + '&id=' + nodeId;
    CreateModalIFrameDialogCloseButton(url);
}

/**
 * opens the tree node editor and adds a new node
 *
 */
function openTreeNodeEditorAddNewNode(node) {
    var nodeId = $(node).parent().attr('id');
    currentNodeId = nodeId; // save nodeId in global var
    var url = $(navTreeDataContainer).data('open-tree-node-editor-add-new-node-url') + '&parent_id=' + nodeId;
    CreateModalIFrameDialogCloseButton(url);
}

/**
 * deletes a node and kills the page connections
 */
function deleteNode(node) {
    var confirmMessage = CHAMELEON.CORE.i18n.Translate('chameleon_system_core.cms_module_page_tree.confirm_delete');
    confirmMessage = confirmMessage.replace(/&quot;/g, '\"');

    var nodeTitle = $(node).text();
    confirmMessage = confirmMessage.replace('%nodeName%', nodeTitle);

    if(confirm(confirmMessage)){
        var nodeId = $(node).parent().attr('id');
        currentNodeId = nodeId; // save nodeId in global var
        var url = $(navTreeDataContainer).data('delete-node-url') + '&nodeId=' + nodeId;
        CHAMELEON.CORE.showProcessingModal();
        GetAjaxCallTransparent(url, deleteNodeSuccess);
    }
}

/**
 * final remove of the node from DOM tree
 */
function deleteNodeSuccess(nodeId, responseMessage) {
    if ('success' === responseMessage && currentNodeId === nodeId) {
        //set to parent of deleted node
        currentNodeId = $(".navigationTreeContainer.jstree #" + nodeId).parent().parent().attr('id');
        refreshNode(nodeId);
    }
    window.CHAMELEON.CORE.hideProcessingModal();
}

/**
 * update jstree node(s)
 */
function updateTreeNode(formObject) {
    // maybe a new node was created, currentNodeId is the node from which the call was made, so refresh from this node
    refreshNode(currentNodeId);
    currentNodeId = formObject.id.value;
}

function assignPage(node) {
    var nodeId = $(node).attr('id');
    currentNodeId = nodeId; // save nodeId in global var
    var url = $(navTreeDataContainer).data('assign-page-url') + '&sRestriction=' + nodeId + '&nodeId=' + nodeId;
    CreateModalIFrameDialogCloseButton(url);
    refreshNodeOnModalClose(nodeId);
}


function connectPageOnSelect(nodeId) {
    currentNodeId = nodeId; // save nodeId in global var
    var url = $(navTreeDataContainer).data('connect-page-on-select-url') + '&sRestriction=' + nodeId + '&nodeId=' + nodeId;
    GetAjaxCallTransparent(url, connectPageSuccess);
}

function connectPageSuccess(nodeId, responseMessage) {
    if ('success' === responseMessage) {
        //don't do a refresh here, because refresh_node triggers "selected" again from state
        $(".navigationTreeContainer.jstree #"+nodeId+"_anchor").addClass('activeConnectedNode');
    } else {
        $(".navigationTreeContainer.jstree #" + nodeId + "_anchor").addClass('jstree-clicked');
    }
}

function disconnectPageOnDeselect(nodeId) {
    currentNodeId = nodeId; // save nodeId in global var
    var assignedpageId = $(navTreeDataContainer).data('current-page-id');
    var url = $(navTreeDataContainer).data('disconnect-page-on-deselect-url') + '&sRestriction=' + nodeId + '&pageId=' + assignedpageId + '&nodeId=' + nodeId;
    GetAjaxCallTransparent(url, disconnectPageSuccess);
}

function disconnectPageSuccess(nodeId, responseMessage) {
    if ('success' === responseMessage) {
        //don't do a refresh here, because refresh_node triggers "selected" again from state
        $(".navigationTreeContainer.jstree #" + nodeId + "_anchor").removeClass('activeConnectedNode');
    } else {
        $(".navigationTreeContainer.jstree #" + nodeId + "_anchor").addClass('jstree-clicked');
    }
}

/**
 * moves node by drag&drop data
 */
function moveNode(nodeId, parentNodeId, position) {
    if (typeof parentNodeId != 'undefined' && typeof nodeId != 'undefined') {
        CHAMELEON.CORE.showProcessingModal();
        var url = $(navTreeDataContainer).data('move-node-url') + '&nodeId=' + nodeId + '&parentNodeId=' + parentNodeId + '&position=' + position;
        GetAjaxCallTransparent(url, moveNodeSuccess);
    }
}

/**
 * unblocks the UI
 */
function moveNodeSuccess(nodeId, responseMessage) {
    window.CHAMELEON.CORE.hideProcessingModal();
}

function refreshNodeOnModalClose(nodeId) {
    $('#modalDialog').on('hidden.bs.modal', function () {
        refreshNode(nodeId);
    });
}

function refreshNode(nodeId) {
    // if you want to use refresh_node together with checkboxes, you should know, that refresh_node saves
    // the selection state before refreshing and restores the state after it. So it's impossible to change the
    // selection state with refresh_node if a page connection was added or deleted, for example with editnode()
    // if we manually select or deselect the selected node here before refreshing, it also triggers the event
    // deselect_node and this kills the connection to the active page
    var tree = $(".navigationTreeContainer.jstree").jstree(true);

    // refresh_node can only refresh the children and NOT the node itself, otherwise we get duplicate ids and
    // after that we have problems with drag&drop, so we have to refresh the siblings too with the parentNode.
    var parentId = tree.get_parent(nodeId);
    tree.refresh_node(parentId);
}
