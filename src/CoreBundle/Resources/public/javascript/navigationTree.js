
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