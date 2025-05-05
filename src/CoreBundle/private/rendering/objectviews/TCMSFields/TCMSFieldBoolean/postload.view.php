<?php require dirname(__FILE__).'/../TCMSField/postload.view.php'; ?>
if (isset($this->sqlData['<?php echo $sFieldDatabaseName; ?>']) && $this->sqlData['<?php echo $sFieldDatabaseName; ?>']=='1') $this-><?php echo $sFieldName; ?> = true;
else $this-><?php echo $sFieldName; ?> = false;
