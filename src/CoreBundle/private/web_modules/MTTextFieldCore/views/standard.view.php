<?php
$oSingleDynPage = &$data['oTableRow'];
/** @var $oSingleDynPage TCMSRecord */
$oDownloadList = &$oSingleDynPage->GetDownloads('data_pool');
?>
<?php if ($oSingleDynPage->sqlData['name']) {
    ?>
<h1><?=TGlobal::OutHTML($oSingleDynPage->sqlData['name']); ?></h1><?php
} ?>
<?php if ($oSingleDynPage->sqlData['subheadline']) {
        ?>
<h2><?=TGlobal::OutHTML($oSingleDynPage->sqlData['subheadline']); ?></h2><?php
    } ?>
<div><?=$oSingleDynPage->GetTextField('content', 790); ?></div>
<?php if ($oDownloadList->Length() > 0) {
        ?>
<div style="padding-top:10px">
    <hr/>
    <div class="downloadsheadline">Downloads</div>
    <?php while ($oDownload = $oDownloadList->Next()) {
            /** @var $oDownload TCMSDownloadFile */ ?>
    <div style="padding-bottom:3px"><?=$oDownload->getDownloadHtmlTag(); ?></div>
    <?php
        } ?>
</div>
<?php
    } ?>
