<?php
/**
* @deprecated since 6.3.7 - use NavigationTreeSingleSelectWysiwyg instead.
*/
?>

<script type="text/javascript">
    $(document).ready(function () {

        $("#treeNodeSelect").jstree({
            "core":{
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
    /**
     * TreeNode field: sets selected node
     * parameter: treeNode is @deprecated since 6.1.6
     */
    function chooseTreeNode(pagedef, treeNode, text) {
        var url = '/INDEX?pagedef=' + pagedef;

        parent.opener.window.CKEDITOR.tools.callFunction(<?=$CKEditorFuncNum; ?>, encodeURI(url), function () {
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
        });
    }
</script>

<div id="treeNodeSelect">
    <ul>
        <?=$treeHTML; ?>
    </ul>
</div>
<?=$treePathHTML; ?>
