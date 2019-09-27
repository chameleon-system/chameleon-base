<script type="text/javascript">
    $(document).ready(function () {

        $("#treeNodeSelect")
            .jstree({
            "core":{
                "initially_open":[ "node557" ],
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
        }).on('select');


        $('.jstree-selection').click(function () {
            var selectedItem = $("#treeNodeSelect").jstree('get_selected');

            if (selectedItem.length > 0) {
                var fieldName = $('#'+selectedItem[0]).data('selection').fieldName;
                var newId = $('#'+selectedItem[0]).data('selection').nodeId;
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
        parent.$('#' + fieldName + '_path').html(document.getElementById(fieldName + '_tmp_path_' + newId).innerHTML);
        parent.CloseModalIFrameDialog();
    }
</script>

<button class="btn btn-success mb-4 jstree-selection">Auswahl übernehmen</button>
<button class="btn btn-danger mb-4 jstree-exit">Abbrechen</button>

<div id="treeNodeSelect">
    <ul>
        <?php
        echo $data['treeHTML'];
        ?>
    </ul>
</div>

<button class="btn btn-success mt-4 jstree-selection">Auswahl übernehmen</button>
<button class="btn btn-danger mt-4 jstree-exit">Abbrechen</button>

<?php
echo $data['treePathHTML'];
?>
