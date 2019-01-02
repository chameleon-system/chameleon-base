<?php

use ChameleonSystem\CoreBundle\ServiceLocator;

if ($data['aPermission']['showlist'] && '1' != $data['only_one_record_tbl']) {
    ?>
<script type="text/javascript">
    function switchRecord(id) {
        if (id != '') {
            url = '<?=PATH_CMS_CONTROLLER; ?>?pagedef=tableeditor&id=' + id + '&tableid=<?=urlencode($data['tableid']); ?>&sRestriction=<?php if (!empty($data['sRestriction'])) {
        echo urlencode($data['sRestriction']);
    } ?>&sRestrictionField=<?php if (!empty($data['sRestrictionField'])) {
        echo urlencode($data['sRestrictionField']);
    } ?>&popLastURL=1';
            document.location.href = url;
        }
    }
</script>
<?php
}
$oController = TGlobal::GetController();
?>
<div class="cmsBoxBorder additionalInfoHeaderContainer">
<table cellpadding="0" cellspacing="0" id="tableEditorHeader">
<tr>
    <th>IDs</th>
    <th><?=TGlobal::OutHTML(TGlobal::Translate('chameleon_system_core.cms_module_table_editor.header_record')); ?></th>
    <?php
    if ($oCmsLock) {
        ?>
        <th><?=TGlobal::OutHTML(TGlobal::Translate('chameleon_system_core.cms_module_table_editor.header_lock')); ?></th>
        <?php
    }

    if ($bRevisionManagementActive) {
        ?>
        <th><?=TGlobal::OutHTML(TGlobal::Translate('chameleon_system_core.template_engine.header_revision')); ?></th>
        <?php
    }

    if ($data['aPermission']['showlist'] && '1' != $data['only_one_record_tbl']) {
        ?>
        <th><?=TGlobal::Translate('chameleon_system_core.cms_module_table_editor.quick_switch'); ?></th>
        <?php
    }
    ?>
</tr>
<tr>
    <td>
        <div><strong>Auto-Increment ID:</strong> <?=TGlobal::OutHTML($data['cmsident']); ?></div>
        <div><strong>ID:</strong> <span class="guid"><?=TGlobal::OutHTML($data['id']); ?></span></div>
    </td>
    <td>
        <div><?=$sTableTitle; ?></div>
        <div><?php
            $length = 50;
            $sRecordName = strip_tags($sRecordName);
            if (mb_strlen($sRecordName) > $length) {
                $sRecordName = mb_substr($sRecordName, 0, $length);
                $lastSpacePos = mb_strrpos($sRecordName, ' ');
                $sRecordName = mb_substr($sRecordName, 0, $lastSpacePos);
                $sRecordName .= '...';
            }

            echo $sRecordName; ?></div>
    </td>
    <?php
    if ($oCmsLock) {
        $oLockUser = $oCmsLock->GetFieldCmsUser(); /** @var $oLockUser TdbCmsUser */ ?>
        <td class="<?='user'.$oLockUser->id; ?>" style="cursor:pointer;">
            <?php
            $sData = $oLockUser->GetUserIcon(false).'<div class="name"><strong>'.TGlobal::Translate('chameleon_system_core.record_lock.lock_owner_name').': </strong>'.TGlobal::OutJS($oLockUser->GetName()).'</div>';
        if (!empty($oLockUser->fieldEmail)) {
            $sData .= '<div class="email"><strong>'.TGlobal::Translate('chameleon_system_core.record_lock.lock_owner_mail').': </strong>'.TGlobal::OutJS($oLockUser->fieldEmail).'</div>';
        }
        if (!empty($oLockUser->fieldTel)) {
            $sData .= '<div class="tel"><strong>'.TGlobal::Translate('chameleon_system_core.record_lock.lock_owner_phone').': </strong>'.TGlobal::OutJS($oLockUser->fieldTel).'</div>';
        }
        if (!empty($oLockUser->fieldFax)) {
            $sData .= '<div class="fax"><strong>'.TGlobal::Translate('chameleon_system_core.record_lock.lock_owner_fax').': </strong>'.TGlobal::OutJS($oLockUser->fieldFax).'</div>';
        }
        if (!empty($oLockUser->fieldCity)) {
            $sData .= '<div class="city"><strong>'.TGlobal::Translate('chameleon_system_core.record_lock.lock_owner_city').': </strong>'.TGlobal::OutJS($oLockUser->fieldCity).'</div>';
        }

        $oController->AddHTMLHeaderLine('
                <script type="text/javascript">
                  $(document).ready(function() {
                    $(".user'.$oLockUser->id.'").wTooltip({
                      content: \''.$sData.'\',
                      offsetY: 15,
                      offsetX: -8,
                      className: "lockUserinfo chameleonTooltip",
                      style: false
                    });
                  });
                </script>
              ');

        echo '<div>'.$oCmsLock->GetDateField('time_stamp').'</div>';
        echo '<div>'.$oLockUser->GetName().'</div>'; ?>
        </td>
        <?php
    }

    if ($bRevisionManagementActive) {
        ?>
        <td class="revision<?=$iBaseRevisionNumber; ?>" style="cursor:pointer;">
            <div>
                <?php
                if (!empty($iBaseRevisionNumber)) {
                    echo TGlobal::OutHTML(TGlobal::Translate('chameleon_system_core.record_revision.based_on')).' '.$iBaseRevisionNumber;
                    if (!is_null($oLastRevision)) {
                        $sData = '<div class="revisionHeader" style="float:left;"><strong>'.TGlobal::Translate('chameleon_system_core.record_revision.revision_number').':</strong> '.$oLastRevision->fieldRevisionNr.'</div>';
                        $sData .= '<div class="revisionHeader" style="float:right;"><strong>'.TGlobal::Translate('chameleon_system_core.record_revision.last_used_date').':</strong> '.$oLastRevision->fieldLastActiveTimestamp.'</div>';
                        $sData .= '<div class="cleardiv">&nbsp;</div>';
                        $sData .= '<div class="revisionDescription" style="margin-top:10px;"><div><strong>'.TGlobal::OutHTML(TGlobal::Translate('chameleon_system_core.record_revision.description')).':</strong></div><div> '.$oLastRevision->GetTextFieldPlain('description', 300).'</div></div>';

                        $oController->AddHTMLHeaderLine('
                      <script type="text/javascript">
                        $(document).ready(function() {
                          $(".revision'.$iBaseRevisionNumber.'").wTooltip({
                            content: \''.$sData.'\',
                            offsetY: 15,
                            offsetX: -8,
                            className: "revision chameleonTooltip",
                            style: false
                          });
                        });
                      </script>
                    ');
                    }
                } else {
                    echo TGlobal::OutHTML(TGlobal::Translate('chameleon_system_core.record_revision.no_revision_exists'));
                } ?>
            </div>
        </td>
        <?php
    }

    if ($data['aPermission']['showlist'] && '1' != $data['only_one_record_tbl']) {
        ?>
        <td>
            <div style="width: 180px;">
                <div id="quicklookuplistBG" class="right-inner-addon"><i class="glyphicon glyphicon-search"></i><input id="quicklookuplist" class="form-control input-sm" type="search" name="quicklookuplist" value="" class="ac_input" autocomplete="off" placeholder="<?=TGlobal::OutHTML(TGlobal::Translate('chameleon_system_core.list.search_term')); ?>" /></div>
                <?php
                $sRestrictionField = '';
        $sRestriction = '';
        if (!empty($data['sRestrictionField'])) {
            $sRestrictionField = urlencode($data['sRestrictionField']);
        }
        if (!empty($data['sRestriction'])) {
            $sRestriction = urlencode($data['sRestriction']);
        }

        /**
         * @var \ChameleonSystem\CoreBundle\Util\UrlUtil $urlUtil
         */
        $urlUtil = ServiceLocator::get('chameleon_system_core.util.url');
        $sAjaxURL = $urlUtil->getArrayAsUrl([
                        'id' => TGlobal::OutJS($data['tableid']),
                        'pagedef' => 'tablemanager',
                        '_rmhist' => 'false',
                        'sOutputMode' => 'Ajax',
                        'module_fnc[contentmodule]' => 'ExecuteAjaxCall',
                        '_fnc' => 'GetAutoCompleteAjaxList',
                        'sRestrictionField' => $sRestrictionField,
                        'sRestriction' => $sRestriction,
                        'recordID' => $data['id'],
                ], PATH_CMS_CONTROLLER.'?', '&'); ?>
                <script type="text/javascript">
                    $(document).ready(function () {
                        $("#quicklookuplist").autocomplete(
                            {
                                source: "<?= \addslashes($sAjaxURL); ?>",
                                minLength: 1,
                                select: function( event, ui ) {
                                    switchRecord(ui.item.value);
                                },
                                open: function(event,ui) {
                                    $('.ui-autocomplete:last').css('width', 'auto').css('min-width', '155px').css('z-index', '100');
                                }
                            }
                        );
                    });
                </script>
            </div>
        </td>
        <?php
    }
    ?>
</tr>
</table>
</div>