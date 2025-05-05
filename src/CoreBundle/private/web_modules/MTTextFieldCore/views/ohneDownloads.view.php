<?php
$oSingleDynPage = $data['oTableRow'];
/** @var $oSingleDynPage TCMSRecord */
$oDownloadList = $oSingleDynPage->GetDownloads('data_pool');
?>
<?php if ($oSingleDynPage->sqlData['name']) {
    ?>
<h1><?php echo TGlobal::OutHTML($oSingleDynPage->sqlData['name']); ?></h1><?php
} ?>
<?php if ($oSingleDynPage->sqlData['subheadline']) {
    ?>
<h2><?php echo TGlobal::OutHTML($oSingleDynPage->sqlData['subheadline']); ?></h2><?php
} ?>
<div><?php echo $oSingleDynPage->GetTextField('content', 790); ?></div>