<?php require dirname(__FILE__).'/../TCMSField/postload.view.php'; ?>
if (isset($this->sqlData['<?= $sFieldDatabaseName; ?>']) && $this->sqlData['<?= $sFieldDatabaseName; ?>']=='1') $this-><?= $sFieldName; ?> = true;
else $this-><?= $sFieldName; ?> = false;
