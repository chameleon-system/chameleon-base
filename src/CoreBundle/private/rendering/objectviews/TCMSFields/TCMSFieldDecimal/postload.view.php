<?php require dirname(__FILE__).'/../TCMSField/postload.view.php'; ?>
if ($oLocal === null && class_exists('TCMSLocal')) {
$oLocal = TCMSLocal::GetActive();
}
if (isset($this->sqlData['<?= $sFieldDatabaseName; ?>'])) {
$this-><?= $sFieldName; ?> = $this->sqlData['<?= $sFieldDatabaseName; ?>'];
} else {
$this-><?= $sFieldName; ?> = 0;
}
if (false !== $oLocal) {
$this-><?= $sFieldName; ?>Formated = $oLocal->FormatNumber($this-><?= $sFieldName; ?>,<?= $numberOfDecimals; ?>);
}