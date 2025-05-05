//- unserialize data
if (isset($this->sqlData['<?php echo $sFieldDatabaseName; ?>'])) {
<?php

if (isset($oDefinition->sqlData['is_translatable']) && '1' == $oDefinition->sqlData['is_translatable']) {
    /* Note: we do not need to know the base language - we just avoid copying content if the __LN field does not exist */ ?>
    $this->transformFieldTranslation('<?php echo $sFieldDatabaseName; ?>',$sActiveLanguagePrefix);
<?php
}
?>
    if ($this->sqlData['<?php echo $sFieldDatabaseName; ?>'] === serialize(false)) {
        $this->sqlData['<?php echo $sFieldDatabaseName; ?>'] = false;  // special case - false was serialized
    } elseif(is_string($this->sqlData['<?php echo $sFieldDatabaseName; ?>'])) {
        $sTmpCleanData = @unserialize($this->sqlData['<?php echo $sFieldDatabaseName; ?>']);
        if ($sTmpCleanData !== false) {
            $this->sqlData['<?php echo $sFieldDatabaseName; ?>'] = $sTmpCleanData;
        }
    }

    $this-><?php echo $sFieldName; ?> = $this->sqlData['<?php echo $sFieldDatabaseName; ?>'];
}
