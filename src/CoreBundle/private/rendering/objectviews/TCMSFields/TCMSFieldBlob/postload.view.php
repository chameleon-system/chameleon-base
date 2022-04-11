//- unserialize data
if (isset($this->sqlData['<?= $sFieldDatabaseName; ?>'])) {
<?php

if (isset($oDefinition->sqlData['is_translatable']) && '1' == $oDefinition->sqlData['is_translatable']) {
    /*Note: we do not need to know the base language - we just avoid copying content if the __LN field does not exist */ ?>
    $this->transformFieldTranslation('<?= $sFieldDatabaseName; ?>',$sActiveLanguagePrefix);
<?php
}
?>
    if ($this->sqlData['<?= $sFieldDatabaseName; ?>'] === serialize(false)) {
        $this->sqlData['<?= $sFieldDatabaseName; ?>'] = false;  // special case - false was serialized
    } elseif(is_string($this->sqlData['<?= $sFieldDatabaseName; ?>'])) {
        $sTmpCleanData = @unserialize($this->sqlData['<?= $sFieldDatabaseName; ?>']);
        if ($sTmpCleanData !== false) {
            $this->sqlData['<?= $sFieldDatabaseName; ?>'] = $sTmpCleanData;
        }
    }

    $this-><?= $sFieldName; ?> = $this->sqlData['<?= $sFieldDatabaseName; ?>'];
}
