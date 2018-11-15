<?php require dirname(__FILE__).'/parts/navi.inc.php'; ?>

<div class="card">
    <div class="card-header">
    <?php
    require dirname(__FILE__).'/parts/header.inc.php';
    ?>
    </div>
    <div class="card-body p-0">
    <?php
    $oCmsUser = TCMSUser::GetActiveUser();
    $oEditLanguage = $oCmsUser->GetCurrentEditLanguageObject();
    ?>
    <iframe name="userwebpage" id="userwebpageiframe" frameborder="0"
            src="<?=URL_WEB_CONTROLLER; ?>?pagedef=<?=TGlobal::OutHTML(urlencode($data['oPage']->id)); ?>&amp;__modulechooser=true&amp;id=<?=TGlobal::OutHTML(urlencode($data['oPage']->id)); ?>&amp;entropy=<?=md5(rand()); ?>&amp;esdisablelinks=true&amp;esdisablefrontendjs=true&amp;__previewmode=true&amp;previewLanguageId=<?=$oEditLanguage->id; ?>"
            width="100%" height="600"></iframe>
    </div>
</div>
