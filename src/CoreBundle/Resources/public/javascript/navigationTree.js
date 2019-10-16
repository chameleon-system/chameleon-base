
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


    $(".navigationTreeContainer")
        .jstree({
            "core":{
                "multiple": true
                // "data": {
                //     "url": "/cms?pagedef=CMSModulePageTreePlain&amp;module_fnc%5Bcontentmodule%5D=ExecuteAjaxCall&amp;_fnc=GetSubTreeForJsTree&amp;tableid=201&amp;sOutputMode=Plain&amp;nodeID=e2452e83-8afe-7d55-c827-bea96e2b6c73&amp;cmsauthenticitytoken=PF51drbPW6mqFlA_77H3Q2eMwb3MtXP-MH4iigARqAE",
                //     "data": function (node) {
                //         console.log('Huhu:'+node.id);
                //
                //         return { "id": node.id };
                //     }
                // }
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


    $(".navigationTreeContainer").bind("select_node.jstree", function(e, data) {
        $("#jstree_demo_div").jstree('open_node', data.node);
    });

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