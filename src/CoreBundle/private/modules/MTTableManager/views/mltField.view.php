<?php
$oCMSUser = $data['oCMSUser'];
/** @var $oCMSUser TCMSUser */
$oGlobal = TGlobal::instance();

$url = PATH_CMS_CONTROLLER.'?'.TTools::GetArrayAsURL(array('pagedef' => 'mltfieldList', 'name' => $oGlobal->GetUserData('name'), 'id' => $data['id'], 'sRestriction' => $data['sRestriction'], 'sRestrictionField' => $data['sRestrictionField']));
$sortUrlParameters = array('pagedef' => 'CMSFieldMLTPosition', '_rmhist' => 'false', 'field' => $data['field'], 'name' => $data['field'], 'module_fnc' => array('contentmodule' => 'GetSortElements'), 'tableSQLName' => $data['sTableName'], 'sRestriction' => $data['sRestriction'], 'sRestrictionField' => $data['sRestrictionField']);
if (isset($sRestrictionField) && '_mlt' == substr($sRestrictionField, -4)) {
    $sortUrlParameters['table'] = substr($sRestrictionField, 0, -4);
}

$sSortUrl = PATH_CMS_CONTROLLER.'?'.TTools::GetArrayAsURL($sortUrlParameters);

?>
<form id="cmsformdel" name="cmsformdel" method="post" action="<?=PATH_CMS_CONTROLLER; ?>" accept-charset="UTF-8">
    <input type="hidden" name="tableid" value="<?=TGlobal::OutHTML($data['id']); ?>"/>
    <input type="hidden" name="pagedef" value="tableeditor"/>
    <input type="hidden" name="id" value=""/>
    <input type="hidden" name="module_fnc[contentmodule]" value=""/>
    <?php foreach ($data['aHiddenFields'] as $key => $value) {
    ?>
    <input type="hidden" name="<?=TGlobal::OutHTML($key); ?>" value="<?=TGlobal::OutHTML($value); ?>"/>
    <?php
} ?>
</form>
<form id="cmsform" name="cmsform" method="post" target="_top" action="<?=PATH_CMS_CONTROLLER; ?>" accept-charset="UTF-8">
    <input type="hidden" name="tableid" value="<?=TGlobal::OutHTML($data['id']); ?>"/>
    <input type="hidden" name="pagedef" value="tableeditor"/>
    <input type="hidden" name="id" value=""/>
    <input type="hidden" name="module_fnc[contentmodule]" value=""/>
    <?php foreach ($data['aHiddenFields'] as $key => $value) {
        ?>
    <input type="hidden" name="<?=TGlobal::OutHTML($key); ?>" value="<?=TGlobal::OutHTML($value); ?>"/>
    <?php
    } ?>
</form>
<div style="position: relative; top: 2px;">
    <table border="0" cellpadding="0" cellspacing="0" width="100%">
        <tr>
            <td>
                <div style="padding-left: 2px;">
                    <div class="btn-group">
                            <?php
                            $oViewRenderer = new ViewRenderer();
                            $oViewRenderer->AddSourceObject('sTitle', TGlobal::Translate('chameleon_system_core.field_lookup_multi_select.action_connect_records'));
                            $oViewRenderer->AddSourceObject('sItemKey', 'chooseItems');
                            $oViewRenderer->AddSourceObject('sCSSClass', 'btn btn-sm btn-success');
                            $oViewRenderer->AddSourceObject('sOnClick', "parent.CreateModalIFrameDialogCloseButton('".TGlobal::OutHTML($url)."',0,0,'".TGlobal::Translate('chameleon_system_core.field_lookup_multi_select.action_connect_records')."');");
                            $oViewRenderer->AddSourceObject('sIconURL', TGlobal::GetStaticURLToWebLib('/images/icons/link.png'));
                            $oViewRenderer->AddSourceObject('sButtonStyle', '');
                            echo $oViewRenderer->Render('MTTableEditor/singleMenuButton.html.twig', null, false);

                            $data['oMenuItems']->GoToStart();
                            /** @var $oMenuItem TCMSTableEditorMenuItem */
                            while ($oMenuItem = $data['oMenuItems']->Next()) {
                                echo $oMenuItem->GetMenuItemHTML();
                            }
                            ?>
                            <?php if (!empty($data['bShowCustomSort'])) {
                                $oViewRenderer = new ViewRenderer();
                                $oViewRenderer->AddSourceObject('sTitle', TGlobal::Translate('chameleon_system_core.field_lookup_multi_select.action_sort_connections'));
                                $oViewRenderer->AddSourceObject('sItemKey', 'sortItems');
                                $oViewRenderer->AddSourceObject('sCSSClass', 'btn btn-sm btn-info');
                                $oViewRenderer->AddSourceObject('sOnClick', "parent.CreateModalIFrameDialogCloseButton('".TGlobal::OutHTML($sSortUrl)."',0,0,'".TGlobal::Translate('chameleon_system_core.field_lookup_multi_select.action_sort_connections')."');");
                                $oViewRenderer->AddSourceObject('sIconURL', TGlobal::GetStaticURLToWebLib('/images/icons/application_cascade.png'));
                                $oViewRenderer->AddSourceObject('sButtonStyle', '');
                                echo $oViewRenderer->Render('MTTableEditor/singleMenuButton.html.twig', null, false);
                            } ?>
                    </div>
                </div>
            </td>
        </tr>
    </table>
</div>
<script type="text/javascript">
    function deleteConnection(id) {
        parent.removeMLTConnection('<?php echo str_replace('_mlt', '', $data['sRestrictionField']); ?>', '<?=TGlobal::OutHTML($oGlobal->GetUserData('name')); ?>', '<?=TGlobal::OutHTML($data['sRestriction']); ?>', id)
    }
</script>
<?php echo $data['sTable']; ?>
