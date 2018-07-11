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