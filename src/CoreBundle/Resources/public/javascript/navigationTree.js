
$(document).ready(function () {

    $("#singleTreeNodeSelect")
        .jstree({
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