if (isset($this->sqlData['<?php echo $sFieldDatabaseName; ?>'])) {
<?php
if (true === ACTIVE_TRANSLATION && isset($oDefinition->sqlData['is_translatable']) && '1' == $oDefinition->sqlData['is_translatable']) {
    /* Note: we do not need to know the base language - we just avoid copying content if the __LN field does not exist */ ?>
    $this->transformFieldTranslation('<?php echo $sFieldDatabaseName; ?>',$sActiveLanguagePrefix);
<?php
}
?>
    $this-><?php echo $sFieldName; ?> = $this->sqlData['<?php echo $sFieldDatabaseName; ?>'];
}

