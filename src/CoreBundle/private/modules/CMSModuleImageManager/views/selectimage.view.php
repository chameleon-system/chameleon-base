<style>
    .tblsearch {
        background-image: url(<?php echo TGlobal::GetPathTheme(); ?>/images/table_bg.gif);
        border-top: 1px solid #9A9A9A;
        width: 100%;
        padding: 5px;
        color: #841313;
        font-weight: bold;
        font-size: 13px;
        line-height: 20px;
    }
</style>
<script type="text/javascript">
    $(document).ready(function () {
        $("#mediaFolderSelectTree").jstree({
            "core":{
                "initially_open":[ "node1" ],
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
    });
</script>
<table width="100%">
    <tr>
        <td valign="top">
            <h1>
                <?=TGlobal::Translate('chameleon_system_core.cms_module_image_manager.folder_list'); ?>
            </h1>
            <div>
                <div id="mediaFolderSelectTree">
                    <ul>
                        <?php
                        echo $data['treeHTML'];
                        ?>
                    </ul>
                </div>
            </div>
        </td>
        <td valign="top">
            <?=$data['sTable']; ?>
        </td>
    </tr>
</table>