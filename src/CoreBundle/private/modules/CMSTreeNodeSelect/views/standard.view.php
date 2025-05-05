<?php
/**
 * @deprecated since 6.3.8 - use NavigationTreeSingleSelect instead.
 */
?>
<script type="text/javascript">
    $(document).ready(function () {

        $("#treeNodeSelect").jstree({
            "core":{
                "initially_open":[ "node557" ],
                "animation":0,
                "html_titles":true
            },
            "theme":'default',
            "themes":{
                "theme":"default",
                "dots":true,
                "icons":true
            },
            "plugins":[ "themes", "html_data", "cookies" ]
        });

        $("#treeNodeSelect").jstree("open_all");

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

<div id="treeNodeSelect">
    <ul>
        <?php
        echo $data['treeHTML'];
?>
    </ul>
</div>
<?php
echo $data['treePathHTML'];
?>

