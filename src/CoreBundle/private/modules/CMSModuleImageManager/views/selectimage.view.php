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
                <?php echo ChameleonSystem\CoreBundle\ServiceLocator::get('translator')->trans('chameleon_system_core.cms_module_image_manager.folder_list'); ?>
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
            <?php echo $data['sTable']; ?>
        </td>
    </tr>
</table>