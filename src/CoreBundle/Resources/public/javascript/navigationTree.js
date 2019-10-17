
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
                "pageFolder": {
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


    $(".no-navigationTreeContainer")
        .jstree({
            "core":{
                "multiple": true,
                'data' : {
                    'text': 'Website',
                    'children': [
                        {
                            'text': 'Home',
                            'children': false
                        },
                        {
                            'text': 'Impressum',
                            'children': true
                        }
                    ]

                }
            },
            "types": {
                "default": {
                    "icon": "fas fa-folder-open"
                },
                "pageFolder": {
                    "icon": "fas fa-folder-open",
                    "check_node": false
                },
                "page": {
                    "icon": "far fa-file"
                }
            },
            "plugins":[ "types", "wholerow", "changed" ]
        });


    // $('.navigationTreeContainer').jstree(options).bind("select_node.jstree",function(event, data){
    //         //Load child node here
    //     console.log('Click!');
    //
    // });


    // $(".navigationTreeContainer").bind("select_node.jstree", function(e, data) {
    //     $("#jstree_demo_div").jstree('open_node', data.node);
    // });

    $(".navigationTreeContainer-checkboxes")
        .jstree({
            "core":{
                "multiple": true
            },
            "types": {
                "default": {
                    "icon": "fas fa-folder-open"
                },
                "pageFolder": {
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