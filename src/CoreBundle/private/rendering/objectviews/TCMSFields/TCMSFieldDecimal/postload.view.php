<?php require dirname(__FILE__).'/../TCMSField/postload.view.php'; ?>
if ($oLocal === null && class_exists('TCMSLocal')) {
$oLocal = TCMSLocal::GetActive();
}
if (isset($this->sqlData['<?php echo $sFieldDatabaseName; ?>'])) {
$this-><?php echo $sFieldName; ?> = $this->sqlData['<?php echo $sFieldDatabaseName; ?>'];
} else {
$this-><?php echo $sFieldName; ?> = 0;
}
if (false !== $oLocal) {
$this-><?php echo $sFieldName; ?>Formated = $oLocal->FormatNumber($this-><?php echo $sFieldName; ?>,<?php echo $numberOfDecimals; ?>);
}