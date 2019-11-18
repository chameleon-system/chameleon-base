<?php

use ChameleonSystem\CoreBundle\ServiceLocator;

$oController = TGlobal::GetController();
?>
<nav class="navbar navbar-light bg-light pl-2 pr-2 pt-0">
    <span class="navbar-brand pt-2"><?php
        if ('' === $sRecordName) {
            $sRecordName = TGlobal::Translate('chameleon_system_core.text.unnamed_record');
        } else {
            $length = 100;
            $sRecordName = strip_tags($sRecordName);
            if (mb_strlen($sRecordName) > $length) {
                $sRecordName = mb_substr($sRecordName, 0, $length);
                $lastSpacePos = mb_strrpos($sRecordName, ' ');
                $sRecordName = mb_substr($sRecordName, 0, $lastSpacePos);
                $sRecordName .= '...';
            }
        }
        echo $sRecordName; ?></span>
        <?php
        $idsPopoverText = '<div class="callout callout-info mt-0 mb-1"><strong class="text-muted">Auto-Increment ID:</strong><br><strong class="h6">'.$data['cmsident'].'</strong></div>
        <div class="callout callout-info mt-0 mb-1"><strong class="text-muted">ID:</strong><br><strong class="h6">'.$data['id'].'</strong></div>';
        ?>
        <button class="btn btn-outline-info btn-sm mt-2 mr-2" type="button" role="button" data-toggle="popover"
                data-placement="bottom"
                data-content="<?= TGlobal::OutHTML($idsPopoverText); ?>" data-original-title="IDs">
            <i class="fas fa-database"></i> IDs
        </button>

        <?php
        if ('' !== $oTableDefinition->sqlData['notes']) {
            ?>
            <button class="btn btn-outline-info btn-sm mt-2 mr-2" type="button" role="button" data-toggle="popover"
                    data-placement="bottom"
                    data-content="<?= nl2br(TGlobal::OutHTML($oTableDefinition->sqlData['notes'])); ?>"
                    data-original-title="<?= TGlobal::OutHTML(TGlobal::Translate('chameleon_system_core.cms_module_table_editor.field_help')); ?>">
                <i class="fas fa-question-circle"></i> <?= TGlobal::OutHTML(TGlobal::Translate('chameleon_system_core.cms_module_table_editor.field_help')); ?>
            </button>
            <?php
        }

        if ($oCmsLock) {
            $oLockUser = $oCmsLock->GetFieldCmsUser();
            /** @var $oLockUser TdbCmsUser */
            $sData = '<div class="callout callout-danger mt-0 mb-1"><strong class="text-muted">'.TGlobal::Translate('chameleon_system_core.record_lock.lock_owner_name').': </strong><br><strong class="h6">'.TGlobal::OutHTML($oLockUser->GetName()).'</strong></div>';
            if (!empty($oLockUser->fieldEmail)) {
                $sData .= '<div class="callout callout-danger mt-0 mb-1"><strong class="text-muted">'.TGlobal::Translate('chameleon_system_core.record_lock.lock_owner_mail').': </strong><br><strong class="h6"><a href="mailto:'.TGlobal::OutHTML($oLockUser->fieldEmail).'">'.TGlobal::OutHTML($oLockUser->fieldEmail).'</a></strong></div>';
            }
            if (!empty($oLockUser->fieldTel)) {
                $sData .= '<div class="callout callout-danger mt-0 mb-1"><strong class="text-muted">'.TGlobal::Translate('chameleon_system_core.record_lock.lock_owner_phone').': </strong><br><strong class="h6"><a href="tel:'.TGlobal::OutHTML($oLockUser->fieldEmail).'">'.TGlobal::OutHTML($oLockUser->fieldTel).'</a></strong></div>';
            }

            $sData .= '<div class="callout callout-danger mt-0 mb-1">'.$oCmsLock->GetDateField('time_stamp').'</div>'; ?>
            <button class="btn btn-danger btn-sm mt-2 mr-2" type="button" role="button" data-toggle="popover"
                    data-placement="bottom"
                    data-content="<?= TGlobal::OutHTML($sData); ?>"
                    data-original-title="<?= TGlobal::OutHTML(TGlobal::Translate('chameleon_system_core.record_lock.locked_by')); ?>">
                <i class="fas fa-user-lock"></i> <?= TGlobal::OutHTML(TGlobal::Translate('chameleon_system_core.cms_module_table_editor.header_lock')); ?>
            </button>
            <?php
        }

        if ($data['aPermission']['showlist'] && '1' != $data['only_one_record_tbl']) {
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
                '_fnc' => 'getAutocompleteRecords',
                'sRestrictionField' => $sRestrictionField,
                'sRestriction' => $sRestriction,
                'recordID' => $data['id'],
            ], PATH_CMS_CONTROLLER.'?', '&');

            $recordUrl = $urlUtil->getArrayAsUrl([
                'pagedef' => 'tableeditor',
                'tableid' => $data['tableid'],
                'sRestrictionField' => $sRestrictionField,
                'sRestriction' => $sRestriction,
                'popLastURL' => '1',
            ], PATH_CMS_CONTROLLER.'?', '&');
            ?>

            <div class="mt-2 typeahead-relative">
                <input id="quicklookuplist"
                       class="form-control"
                       placeholder="<?=TGlobal::OutHTML(TGlobal::Translate('chameleon_system_core.list.other_entries')); ?>"
                       autocomplete="off"
                       data-source-url="<?= $sAjaxURL; ?>"
                       data-record-url="<?= $recordUrl ?>"
                >
            </div>

        <?php
        }
        ?>
</nav>