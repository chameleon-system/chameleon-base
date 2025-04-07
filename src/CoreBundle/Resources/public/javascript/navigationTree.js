$(document).ready(function () {
    currentNodeId = null;
    navTreeDataContainer = $("#navigationTreeDataContainer");

    $("#singleTreeNodeSelect")
        .jstree({
            "core":{
                "multiple": false,
                "open_all": true,
                "data": {
                    "url": navTreeDataContainer.data('tree-nodes-ajax-url'),
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
                    "icon": "fas fa-genderless"
                },
                "folder": {
                    "icon": "fas fa-folder-open",
                    "check_node": false
                },
                "noPage": {
                    "icon": "fas fa-genderless"
                },
                "page": {
                    "icon": "far fa-file"
                },
                "locked": {
                    "icon": "fas fa-lock"
                },
                "extranetpageHidden": {
                    "icon": "far fa-eye-slash"
                },
                "nodeHidden": {
                    "icon": "fas fa-eye-slash"
                },
                "externalLink": {
                    "icon": "fas fa-external-link-alt"
                }
            },
            "checkbox": {
                "three_state": false,
                "cascade": "none"
            },
            "plugins":[ "types", "wholerow", "changed", "checkbox" ]
        })
        .on("select_node.jstree", function (e, data) {
            updateCurrentPageOrPortal(data.node.id);
        })
        .on("deselect_node.jstree", function (e, data) {
            updateCurrentPageOrPortal('');
        });


    $("#singleTreeNodeSelectWysiwyg")
        .jstree({
            "core":{
                "multiple": false,
                "data": {
                    "url": navTreeDataContainer.data('tree-nodes-ajax-url'),
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
                    "icon": "fas fa-genderless"
                },
                "folder": {
                    "icon": "fas fa-folder-open",
                    "check_node": false
                },
                "noPage": {
                    "icon": "fas fa-genderless"
                },
                "page": {
                    "icon": "far fa-file"
                },
                "locked": {
                    "icon": "fas fa-lock"
                },
                "extranetpageHidden": {
                    "icon": "far fa-eye-slash"
                },
                "nodeHidden": {
                    "icon": "fas fa-eye-slash"
                },
                "externalLink": {
                    "icon": "fas fa-external-link-alt"
                }
            },
            "checkbox": {
                "three_state": false,
                "cascade": "none"
            },
            "plugins":[ "types", "wholerow", "changed", "checkbox" ]
        })
        .on('ready.jstree', function() {
            $(this).jstree("open_all");
        })
        .on("select_node.jstree", function (e, data) {
            updateSelectionWysiwyg(data.node);
        });

    $("#navigationTreeContainer")
        .jstree({
            "core":{
                "multiple": true,
                "data": {
                    "url": navTreeDataContainer.data('tree-nodes-ajax-url'),
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
                    "icon": "fas fa-genderless"
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
                "noPage": {
                    "icon": "fas fa-genderless"
                },
                "page": {
                    "icon": "far fa-file"
                },
                "locked": {
                    "icon": "fas fa-lock"
                },
                "extranetpageHidden": {
                    "icon": "far fa-eye-slash"
                },
                "nodeHidden": {
                    "icon": "fas fa-eye-slash"
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
                    "url": navTreeDataContainer.data('tree-nodes-ajax-url'),
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
                    "icon": "fas fa-genderless"
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
                "noPage": {
                    "icon": "fas fa-genderless"
                },
                "page": {
                    "icon": "far fa-file"
                },
                "locked": {
                    "icon": "fas fa-lock"
                },
                "extranetpageHidden": {
                    "icon": "far fa-eye-slash"
                },
                "nodeHidden": {
                    "icon": "fas fa-eye-slash"
                },
                "externalLink": {
                    "icon": "fas fa-external-link-alt"
                }
            },
            "checkbox": {
                "three_state": false,
                "cascade": "none"
            },
            "plugins":[ "types", "wholerow", "changed", "checkbox", "contextmenu"],
            "contextmenu": {
                "items": navigationRightClickMenuForCheckboxes,
                "select_node": false
            },
            "dnd": {
                "drag_selection": false
            }
        })
        .on("select_node.jstree", function (e, data) {
            connectPageOnSelect(data.node.id);
        })
        .on("deselect_node.jstree", function (e, data) {
            disconnectPageOnDeselect(data.node.id);
        });
});

function updateCurrentPageOrPortal(newNodeId) {
    let url = navTreeDataContainer.data('update-selection-url') + '&sRestriction=' + newNodeId + '&nodeId=' + newNodeId;
    GetAjaxCallTransparent(url, updateCurrentPageOrPortalSuccess);
}

function updateCurrentPageOrPortalSuccess(nodeId, responseMessage) {
    if ('success' === responseMessage) {
        const fieldName = navTreeDataContainer.data('field-name');
        chooseTreeNode(fieldName, nodeId);
    }
}

function chooseTreeNode(fieldName, newId) {
    const fieldElement = parent.document.getElementById(fieldName);
    if (fieldElement) {
        fieldElement.value = newId;
    }

    // display new path
    let newPath = "";
    if (newId !== "") {
        const tmpPathElement = document.getElementById(`${fieldName}_tmp_path_${newId}`);
        if (tmpPathElement) {
            newPath = tmpPathElement.innerHTML;
        }
    }

    const pathElement = parent.document.getElementById(`${fieldName}_path`);
    if (pathElement) {
        pathElement.innerHTML = newPath;
    }

    // update onclick attribute in assign button
    const assignButton = parent.document.getElementById(`${fieldName}_btn-assign`);
    if (assignButton) {
        const onclickValue = assignButton.getAttribute('onclick');
        if (onclickValue) {
            const updatedOnClick = onclickValue.replace(/(&id=)[^&]*/, newId ? `$1${newId}` : '');
            assignButton.setAttribute('onclick', updatedOnClick);
        }
    }
}

function updateSelectionWysiwyg(selectedItem) {
    let CKEditorFuncNum = navTreeDataContainer.data('ckeditorfuncnum');
    let selectedItemText = selectedItem.text;
    let connectedPageId = selectedItem.li_attr.isPageId;
    chooseTreeNodeWysiwyg(CKEditorFuncNum, connectedPageId, selectedItemText);
    parent.CloseModalIFrameDialog();
}

function chooseTreeNodeWysiwyg(CKEditorFuncNum, pagedef, text) {
    let url = '/INDEX?pagedef=' + pagedef;

    parent.opener.window.CKEDITOR.tools.callFunction(
        CKEditorFuncNum, encodeURI(url), function () {
            // Get the reference to a dialog window.
            let element,
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
    let items = {
        "editpageconnections": {
            "label": CHAMELEON.CORE.i18n.Translate('chameleon_system_core.navigation_tree.connected_pages'),
            "icon": "fas fa-link",
            "action": function (obj) {
                this.openPageConnectionList(obj.reference);
            }
        },
        "editpage": {
            "label": CHAMELEON.CORE.i18n.Translate('chameleon_system_core.navigation_tree.edit_page'),
            "icon": "far fa-edit",
            "action": function (obj) {
                this.openPageEditor(obj.reference);
            }
        },
        "editpageconfig": {
            "label": CHAMELEON.CORE.i18n.Translate('chameleon_system_core.navigation_tree.page_settings'),
            "icon": "fas fa-cog",
            "action": function (obj) {
                this.openPageConfigEditor(obj.reference);
            }
        },
        "editnode": {
            "label": CHAMELEON.CORE.i18n.Translate('chameleon_system_core.navigation_tree.edit_node'),
            "icon": "fas fa-sitemap",
            "action": function (obj) {
                this.openTreeNodeEditor(obj.reference);
            }
        },
        "newnode": {
            "label": CHAMELEON.CORE.i18n.Translate('chameleon_system_core.navigation_tree.new'),
            "icon": "fas fa-plus",
            "action": function (obj) {
                this.openTreeNodeEditorAddNewNode(obj.reference);
            }
        },
        "deletenode": {
            "label": CHAMELEON.CORE.i18n.Translate('chameleon_system_core.navigation_tree.delete'),
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
            "label": CHAMELEON.CORE.i18n.Translate('chameleon_system_core.navigation_tree.new'),
            "icon": "fas fa-plus",
            "action": function (obj) {
                this.openTreeNodeEditorAddNewNode(obj.reference);
            }
        },
        "deletenode": {
            "label": CHAMELEON.CORE.i18n.Translate('chameleon_system_core.navigation_tree.delete'),
            "icon": "far fa-trash-alt",
            "action": function (obj) {
                this.deleteNode(obj.reference);
            }
        }
    };
}


function openPageConnectionList(node) {
    const nodeId = $(node).parent().attr('id');
    currentNodeId = nodeId; // save nodeId in global var
    const url = navTreeDataContainer.data('open-page-connection-list-url') + "&sRestriction=" + nodeId;
    CreateModalIFrameDialogCloseButton(url);
    refreshNodeOnModalClose(nodeId);
}

/**
 * opens the page in edit mode
 */
function openPageEditor(node) {
    const pageId = $(node).parent().attr('isPageId');

    if (pageId !== false && typeof(pageId) !== "undefined") {
        parent.document.location.href = navTreeDataContainer.data('open-page-editor-url') + '&id=' + pageId;
    } else {
        alert(CHAMELEON.CORE.i18n.Translate('chameleon_system_core.navigation_tree.node_has_no_page'));
    }
}

/**
 * opens the page in config edit mode
 */
function openPageConfigEditor(node) {
    const pageId = $(node).parent().attr('isPageId');
    if (pageId !== false && typeof(pageId) !== "undefined") {
        parent.document.location.href = navTreeDataContainer.data('open-page-config-editor-url') + '&id=' + pageId;
    } else {
        alert(CHAMELEON.CORE.i18n.Translate('chameleon_system_core.navigation_tree.node_has_no_page'));
    }
}

/**
 * opens the tree node editor
 */
function openTreeNodeEditor(node) {
    const nodeId = $(node).parent().attr('id');
    currentNodeId = nodeId; // save nodeId in global var
    const url = navTreeDataContainer.data('open-tree-node-editor-url') + '&id=' + nodeId;
    CreateModalIFrameDialogCloseButton(url);
}

/**
 * opens the tree node editor and adds a new node
 *
 */
function openTreeNodeEditorAddNewNode(node) {
    const nodeId = $(node).parent().attr('id');
    currentNodeId = nodeId; // save nodeId in global var
    const url = navTreeDataContainer.data('open-tree-node-editor-add-new-node-url') + '&parent_id=' + nodeId;
    CreateModalIFrameDialogCloseButton(url);
}

/**
 * deletes a node and kills the page connections
 */
function deleteNode(node) {
    let confirmMessage = CHAMELEON.CORE.i18n.Translate('chameleon_system_core.navigation_tree.confirm_delete');
    confirmMessage = confirmMessage.replace(/&quot;/g, '\"');

    let nodeTitle = $(node).text();
    confirmMessage = confirmMessage.replace('%nodeName%', nodeTitle);

    if(confirm(confirmMessage)){
        const nodeId = $(node).parent().attr('id');
        currentNodeId = nodeId; // save nodeId in global var
        const url = navTreeDataContainer.data('delete-node-url') + '&nodeId=' + nodeId;
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
    const nodeId = $(node).attr('id');
    currentNodeId = nodeId; // save nodeId in global var
    const url = navTreeDataContainer.data('assign-page-url') + '&sRestriction=' + nodeId + '&nodeId=' + nodeId;
    CreateModalIFrameDialogCloseButton(url);
    refreshNodeOnModalClose(nodeId);
}


function connectPageOnSelect(nodeId) {
    currentNodeId = nodeId; // save nodeId in global var
    const url = navTreeDataContainer.data('connect-page-on-select-url') + '&sRestriction=' + nodeId + '&nodeId=' + nodeId;
    GetAjaxCallTransparent(url, connectPageSuccess);
}

function connectPageSuccess(nodeId, responseMessage) {
    if ('success' === responseMessage) {
        //don't do a refresh here, because refresh_node triggers "selected" again from state
        $(".navigationTreeContainer.jstree #"+nodeId).addClass('activeConnectedNode');
        updateTreeBreadcrumbs();
    } else {
        $(".navigationTreeContainer.jstree #" + nodeId + "_anchor").addClass('jstree-clicked');
    }
}

function disconnectPageOnDeselect(nodeId) {
    currentNodeId = nodeId; // save nodeId in global var
    const url = navTreeDataContainer.data('disconnect-page-on-deselect-url') + '&sRestriction=' + nodeId + '&nodeId=' + nodeId;
    GetAjaxCallTransparent(url, disconnectPageSuccess);
}

function disconnectPageSuccess(nodeId, responseMessage) {
    if ('success' === responseMessage) {
        //don't do a refresh here, because refresh_node triggers "selected" again from state
        $(".navigationTreeContainer.jstree #" + nodeId).removeClass('activeConnectedNode');
        updateTreeBreadcrumbs();
    } else {
        $(".navigationTreeContainer.jstree #" + nodeId + "_anchor").addClass('jstree-clicked');
    }
}

function updateTreeBreadcrumbs() {
    debugger;
    const fieldName = navTreeDataContainer.data('field-name');
    let newPath = "";
    const selectedItemIds = $("#navigationTreeContainer-checkboxes").jstree('get_selected');

    selectedItemIds.forEach(function (itemId){
        if (false === $('#'+itemId).hasClass('primaryConnectedNodeOfCurrentPage')) {
            newPath += $('#' + fieldName + '_tmp_path_' + itemId).html();
        }
    });
    parent.$('#' + fieldName + '_additional_paths').html(newPath);
}

/**
 * moves node by drag&drop data
 */
function moveNode(nodeId, parentNodeId, position) {
    if (typeof parentNodeId != 'undefined' && typeof nodeId != 'undefined') {
        CHAMELEON.CORE.showProcessingModal();
        const url = navTreeDataContainer.data('move-node-url') + '&nodeId=' + nodeId + '&parentNodeId=' + parentNodeId + '&position=' + position;
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
    $('#modalDialog').on('hidden.coreui.modal', function () {
        refreshNode(nodeId);
    });
}

function refreshNode(nodeId) {
    // if you want to use refresh_node together with checkboxes, you should know, that refresh_node saves
    // the selection state before refreshing and restores the state after it. So it's impossible to change the
    // selection state with refresh_node if a page connection was added or deleted, for example with editnode()
    // if we manually select or deselect the selected node here before refreshing, it also triggers the event
    // deselect_node and this kills the connection to the active page
    let tree = $(".navigationTreeContainer.jstree").jstree(true);

    // refresh_node can only refresh the children and NOT the node itself, otherwise we get duplicate ids and
    // after that we have problems with drag&drop, so we have to refresh the siblings too with the parentNode.
    const parentId = tree.get_parent(nodeId);
    tree.refresh_node(parentId);
}
