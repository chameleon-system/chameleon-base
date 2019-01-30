<div id="rightClickMenuContainer" style="display: none;">
    <ul>
        <li class="firstnode haschildren"><a href="javascript:void(0);" class="haschildren"><img
            src="<?=TGlobal::GetPathTheme(); ?>/images/icons/page_navigation.gif" border="0"
            alt=""/><?=TGlobalBase::OutHTML(TGlobal::Translate('chameleon_system_core.cms_module_header.action_edit_navigation')); ?></a>
            <ul>
                <?php
                foreach ($data['breadcrumb'] as $histid => $item) {
                    ?>
                    <li><a href="#"
                           onclick="document.location.href='<?=TGlobalBase::OutHTML($item['url']); ?>"><img
                        src="<?=TGlobal::GetPathTheme(); ?>/images/icons/page_navigation.gif" border="0"
                        alt=""/><?=$item['name']; ?></a></li>
                    <?php
                }
                ?>
            </ul>
        </li>
        <?php $data['oMenuItems']->GoToStart();
        while ($oMenuItem = $data['oMenuItems']->Next()) {
            /** @var $oMenuItem TCMSTableEditorMenuItem */
            echo $oMenuItem->GetRightClickMenuItemHTML();
        }
        ?>
        <li><a href="#" onclick="$('#tableEditorContainer').unbind('contextmenu');$('#jqContextMenu').hide();"><img
            src="<?=URL_CMS; ?>/images/icons/application_form_delete.png" border="0"
            alt=""/><?=TGlobalBase::OutHTML(TGlobal::Translate('chameleon_system_core.cms_module_table_editor.right_click_disable')); ?></a></li>
    </ul>
    <div class="cleardiv">&nbsp;</div>
</div>
<script type="text/javascript">
    $(document).ready(function () {
        $('#tableEditorContainer').contextMenu('rightClickMenuContainer', {

            onContextMenu:function (e) {
                if ($(e.target).attr('class') == 'contextMenuDisabled') return false;
                else return true;
            }
        });
    });
</script>