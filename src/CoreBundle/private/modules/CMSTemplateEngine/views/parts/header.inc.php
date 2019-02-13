<?php
require_once dirname(__FILE__).'/sharedJS.inc.php';

if ($data['aPermission']['showlist']) {
    ?>
<script type="text/javascript">
    function switchRecord(id) {
        if (id != '') {
            url = '<?=PATH_CMS_CONTROLLER; ?>?pagedef=tableeditor&id=' + id + '&tableid=<?=urlencode($data['tableid']); ?>&popLastURL=1';
            document.location.href = url;
        }
    }
</script>
<?php
}
$oController = TGlobal::GetController();
?>
<table cellpadding="0" cellspacing="0" id="tableEditorHeader">
<tr>
    <th>IDs</th>
    <th><?=TGlobal::OutHTML(TGlobal::Translate('chameleon_system_core.template_engine.header_column_record')); ?></th>
    <?php
    if ($oCmsLock) {
        ?>
        <th><?=TGlobal::OutHTML(TGlobal::Translate('chameleon_system_core.template_engine.header_lock')); ?></th>
        <?php
    }

    /**
     * @deprecated since 6.3.0 - revision management is no longer supported
     */
    if ($bRevisionManagementActive) {
        ?>
        <th><?=TGlobal::OutHTML(TGlobal::Translate('chameleon_system_core.template_engine.header_revision')); ?></th>
        <?php
    }
    ?>
    <th id="navigationBreadcrumbHeader">
        <script type="text/javascript">
            $(document).ready(function () {
                if (($(window).width() >= 1260)) {
                    $('#navigationBreadcrumbHeader').html("<?=TGlobal::OutJS(TGlobal::Translate('chameleon_system_core.template_engine.header_navigation')); ?>");
                } else {
                    // disable navigation list because we have not enough space
                    $(".pageNavigationTreeSelectBox").hide();
                }
            });
        </script>
    </th>
</tr>
<tr>
<td>
    <div>ID: <?=TGlobal::OutHTML($data['cmsident']); ?></div>
    <div>GUID: <span class="guid"><?=TGlobal::OutHTML($data['id']); ?></span></div>
</td>
<td>
    <div><?=$sTableTitle; ?></div>
    <div><?php
        $length = 50;
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
            /** @var $oLockUser TdbCmsUser */
            $oLockUser = $oCmsLock->GetFieldCmsUser(); ?>
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
                $sData .= '<div class="city"><strong>'.TGlobal::Translate('chameleon_system_core.record_lock.lock_owner_city').':</strong>'.TGlobal::OutJS($oLockUser->fieldCity).'</div>';
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

/**
 * @deprecated since 6.3.0
 */
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
                            className: "workflow chameleonTooltip",
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
?>
<td>
    <div class="pageNavigationTreeSelectBox">
        <div class="treeSelectBox">
            <div><img src="<?=TGlobal::GetPathTheme(); ?>/images/dropdown/dropdown_left.png" height="26" width="5"
                      alt=""/></div>
            <div
                style="background: url(<?=TGlobal::GetPathTheme(); ?>/images/dropdown/dropdown_bg.png); line-height: 21px; padding-right: 8px;">
                <div style="margin-top: 3px;"><?php
                    if (is_array($aNavigationBreadCrumbs)) {
                        echo array_shift($aNavigationBreadCrumbs);
                    }
                    ?></div>
            </div>
            <div><img src="<?=TGlobal::GetPathTheme(); ?>/images/dropdown/dropdown_right.png" height="26" width="23"
                      alt="" class="dropdownButton"/></div>
        </div>
        <div class="cleardiv">&nbsp;</div>
        <div class="additionalNavis">
            <table border="0" cellpadding="0" cellspacing="0">
                <tr>
                    <td width="4" height="5" class="boxborder" style="width: 4px;"><img
                        src="<?=TGlobal::GetPathTheme(); ?>/images/dropdown/dropdown_layer_top_left.png" alt="" height="5"
                        width="4"/></td>
                    <td height="5" width="99%" class="boxborder"
                        style="background: url(<?=TGlobal::GetPathTheme(); ?>/images/dropdown/dropdown_layer_top_middle.png);"></td>
                    <td width="6" height="5" class="boxborder" style="width: 6px;"><img
                        src="<?=TGlobal::GetPathTheme(); ?>/images/dropdown/dropdown_layer_top_right.png" alt=""
                        height="5" width="6"/></td>
                </tr>
                <tr>
                    <td width="4" class="boxborder"
                        style="width: 4px; background: url(<?=TGlobal::GetPathTheme(); ?>/images/dropdown/dropdown_layer_left.png);">
                        &nbsp;</td>
                    <td width="99%" class="boxcontent"><?php
                        if (is_array($aNavigationBreadCrumbs) && count($aNavigationBreadCrumbs) > 0) {
                            foreach ($aNavigationBreadCrumbs as $sNavi) {
                                echo $sNavi."\n";
                            }
                        } else {
                            echo TGlobal::OutHTML(TGlobal::Translate('chameleon_system_core.template_engine.no_secondary_navigation_nodes'));
                        }
                        ?>
                    </td>
                    <td width="6" class="boxborder"
                        style="width: 6px; background: url(<?=TGlobal::GetPathTheme(); ?>/images/dropdown/dropdown_layer_right.png);">
                        &nbsp;</td>
                </tr>
                <tr>
                    <td width="4" height="10" class="boxborder" style="width: 4px;"><img
                        src="<?=TGlobal::GetPathTheme(); ?>/images/dropdown/dropdown_layer_bottom_left.png" alt=""
                        height="10" width="4"/></td>
                    <td width="99%" height="10" class="boxborder"
                        style="background: url(<?=TGlobal::GetPathTheme(); ?>/images/dropdown/dropdown_layer_bottom_middle.png);">
                        &nbsp;</td>
                    <td width="6" height="10" class="boxborder" style="width: 6px;"><img
                        src="<?=TGlobal::GetPathTheme(); ?>/images/dropdown/dropdown_layer_bottom_right.png" alt=""
                        height="10" width="6"/></td>
                </tr>
            </table>
        </div>
    </div>
</td>
</tr>
</table>
<script type="text/javascript">
    $(document).ready(function () {
        $('.pageNavigationTreeSelectBox').hover(function () {
            $('.pageNavigationTreeSelectBox .additionalNavis').show();
        }, function () {
            $('.pageNavigationTreeSelectBox .additionalNavis').hide();
        });
    });
</script>
<form name="cmseditform" id="cmseditform" method="post" action="<?=PATH_CMS_CONTROLLER; ?>" style="margin 0; padding 0;"
      accept-charset="UTF-8">
    <input type="hidden" name="pagedef" value="tableeditor"/>
    <input type="hidden" name="tableid" value="<?=TGlobal::OutHTML($data['tableid']); ?>"/>
    <input type="hidden" name="id" value="<?=TGlobal::OutHTML($data['id']); ?>"/>
    <input type="hidden" name="_fnc" value=""/>
    <input type="hidden" name="module_fnc[contentmodule]" value=""/>
    <input type="hidden" name="_noModuleFunction" value="false"/>
</form>

<script type="text/javascript">
    $(document).ready(function() {
        var bodyHeight = parseInt($(window).height());
        var iFramePos = $('#templateengine .card-body').position();
        var additionPaddings = 235;
        var iFrameHeight = bodyHeight - iFramePos.top - additionPaddings;

        if(iFrameHeight < 450){
            iFrameHeight = 450;
        }

        $('#userwebpageiframe').css("height", iFrameHeight);
    });
</script>